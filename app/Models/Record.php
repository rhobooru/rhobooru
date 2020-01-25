<?php

namespace App\Models;

use App\Exceptions\NotAuthenticatedException;
use App\Helpers\ImageHelper;
use App\Models\Traits\UserAudits;
use App\Scopes\UploadedScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class Record extends Model
{
    use SoftDeletes, UserAudits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'md5',
        'title',
        'file_extension',
        'file_size',
        'width',
        'height',
        'megapixels',
        'aspect_ratio',
        'duration',
        'framerate',
        'record_type_id',
        'content_rating_id',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UploadedScope);
    }

    /**
     * Get the record's content rating.
     */
    public function content_rating()
    {
        return $this->belongsTo('App\Models\ContentRating');
    }

    /**
     * Get the record's type.
     */
    public function record_type()
    {
        return $this->belongsTo('App\Models\RecordType');
    }

    /**
     * Get the record's tags.
     */
    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    /**
     * Scope a query to only include approved records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope a query to only include unapproved records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotApproved($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Scope a query to only include uploaded records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUploadCompleted($query)
    {
        return $query->where('upload_complete', true);
    }

    /**
     * Scope a query to only include not uploaded records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotUploadCompleted($query)
    {
        return $query->where('upload_complete', false);
    }

    /**
     * Removed default eager loads from query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoEagerLoads($query)
    {
        return $query->setEagerLoads([]);
    }

    /**
     * Limit a query by record phash distance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSimilarTo(
        $query,
        int $max_distance,
        int $phash,
        ?int $id
    ) {
        $distance = \DB::raw("BIT_COUNT(phash ^ ${phash}) as `distance`");

        $query->addSelect($distance)
            ->whereRaw('BIT_COUNT(phash ^ ?) < ?', [$phash, $max_distance]);

        if ($id !== null) {
            $query->where('id', '!=', $id);
        }

        return $query;
    }

    /**
     * Check if the given file matches this model's `md5`.
     *
     * @param string $path
     *
     * @return bool
     */
    public function verifyMD5(string $path): bool
    {
        $storedHash = ImageHelper::imageToMD5($path);

        return $storedHash === $this->md5;
    }

    /**
     * Processes an uploaded file for this Record.
     *
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return string Path where file was stored
     */
    public function uploadFile(\Illuminate\Http\UploadedFile $file): string
    {
        // Make sure the user is logged in.
        if (! Auth::check()) {
            throw new NotAuthenticatedException();
        }

        // Make sure the record isn't already uploaded.
        if ($this->upload_complete) {
            throw new \Exception('Record already marked as uploaded');
        }

        $config_root = 'rhobooru.media.images';

        // Build the paths and filenames.
        $filename = $this->md5;
        $thumbnail_name = "${filename}_thumbnail." . config("${config_root}.thumbnails.format");
        $preview_name = "${filename}_preview." . config("${config_root}.previews.format");

        $staging_path = config("${config_root}.staging_path");
        $final_path = config("${config_root}.originals.storage_path") . '/' . $this->hashFolder;
        $thumbnail_path = config("${config_root}.thumbnails.storage_path") . '/' . $this->hashFolder;
        $preview_path = config("${config_root}.previews.storage_path") . '/' . $this->hashFolder;

        $full_staging_path = "${staging_path}/${filename}";
        $full_staging_thumbnail_path = "${staging_path}/${thumbnail_name}";
        $full_staging_preview_path = "${staging_path}/${preview_name}";

        $full_thumbnail_path = "${thumbnail_path}/${thumbnail_name}";
        $full_preview_path = "${preview_path}/${preview_name}";

        // Check if the file already exists.
        $file_exists_in_staging = Storage::exists($full_staging_path);

        // If it exists in staging, we may be racing with another upload
        // or retrying a failed upload.
        if ($file_exists_in_staging === true) {
            Storage::delete($full_staging_path);
        }

        // Save the file into the staging path.
        $file->storePubliclyAs($staging_path, $filename);

        $this->file_extension = image_type_to_extension(
            exif_imagetype(Storage::path($full_staging_path)),
            false
        );

        $filename = $this->md5 . '.' . $this->file_extension;
        $full_final_path = $final_path . '/' . $filename;
        $old_full_staging_path = $full_staging_path;
        $full_staging_path = $staging_path . '/' . $filename;

        // Check if the file already exists.
        $file_exists_in_staging = Storage::exists($full_staging_path);

        // If it exists in staging, we may be racing with another upload
        // or retrying a failed upload.
        if ($file_exists_in_staging === true) {
            Storage::delete($full_staging_path);
        }

        Storage::move($old_full_staging_path, $full_staging_path);

        $image = Image::make(Storage::path($full_staging_path));

        $file_exists_in_final = Storage::exists($full_final_path);

        // If it exists in the final path, we are uploading an MD5
        // duplicate.
        if ($file_exists_in_final === true) {
            throw new \Exception('File already exists for MD5');
        }

        // If the saved file's hash doesn't equal our
        // expected hash, the file may be corrupt.
        if (! $this->verifyMD5(Storage::path($full_staging_path))) {
            Storage::delete($full_staging_path);

            throw new \Exception('MD5 mismatch: File corrupted in transit to server.');
        }

        // Calculate the pHash of the saved file.
        $pHash = ImageHelper::imageToPHash(Storage::path($full_staging_path));

        $generated_thumbnail_path = ImageHelper::makeThumbnail(Storage::path($full_staging_path));
        if ($generated_thumbnail_path !== null) {
            Storage::move($full_staging_thumbnail_path, $full_thumbnail_path);
        }

        $generated_preview_path = ImageHelper::makePreview(Storage::path($full_staging_path));
        if ($generated_preview_path !== null) {
            Storage::move($full_staging_preview_path, $full_preview_path);
        }

        // Move file from local staging to final storage.
        Storage::move($full_staging_path, $full_final_path);

        // If the moved file's hash doesn't equal our
        // expected hash, the file may be corrupt.
        if (! $this->verifyMD5(Storage::path($full_final_path))) {
            Storage::delete($full_final_path);

            throw new \Exception('MD5 mismatch: File corrupted in transit to storage.');
        }

        // Update the record.
        try {
            $this->upload_complete = true;
            $this->phash = $pHash;
            $this->width = $image->width();
            $this->height = $image->height();
            $this->file_size = $image->filesize();
            $this->aspect_ratio = $this->width / $this->height;
            $this->save();
        } catch (\Exception $exception) {
            Storage::delete($full_final_path);
            Storage::delete($full_thumbnail_path);
            Storage::delete($full_preview_path);

            throw $exception;
        }

        return $full_final_path;
    }

    /**
     * Get the portion of the MD5 hash that corresponds
     * to the stroage folder.
     *
     * @return string|null
     */
    public function getHashFolderAttribute(): ?string
    {
        if ($this->md5 === null) {
            return null;
        }

        return substr($this->md5, 0, 3);
    }

    /**
     * Get filename for a record's original image.
     *
     * @return string|null
     */
    public static function generateFilename(string $base, string $extension): ?string
    {
        if ($base === null) {
            return null;
        }

        if ($extension === null) {
            return null;
        }

        return $base . '.' . $extension;
    }

    /**
     * Get filename for this record's original image.
     *
     * @return string|null
     */
    public function getFilenameAttribute(): ?string
    {
        return self::generateFilename($this->md5, $this->file_extension);
    }

    /**
     * Get filename for a record's thumbnail.
     *
     * @return string|null
     */
    public static function generateThumbnailFilename(string $base): ?string
    {
        if ($base === null) {
            return null;
        }

        return "${base}_thumbnail."
            . config('rhobooru.media.images.thumbnails.format');
    }

    /**
     * Get filename for this record's thumbnail.
     *
     * @return string|null
     */
    public function getThumbnailFilenameAttribute(): ?string
    {
        return self::generateThumbnailFilename($this->md5);
    }

    /**
     * Get filename for a record's preview.
     *
     * @return string|null
     */
    public static function generatePreviewFilename(string $base): ?string
    {
        if ($base === null) {
            return null;
        }

        return "${base}_preview."
            . config('rhobooru.media.images.previews.format');
    }

    /**
     * Get filename for this record's preview.
     *
     * @return string|null
     */
    public function getPreviewFilenameAttribute(): ?string
    {
        return self::generatePreviewFilename($this->md5);
    }

    /**
     * Get the record's image url.
     *
     * @return string
     */
    public function getImageAttribute(): string
    {
        return $this->buildAssetPath('original');
    }

    /**
     * Get the record's thumbnail url.
     *
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        return $this->buildAssetPath('thumbnail');
    }

    /**
     * Get the record's preview url.
     *
     * @return string|null
     */
    public function getPreviewAttribute(): ?string
    {
        return $this->buildAssetPath('preview', true);
    }

    /**
     * Build the URL for a given media version.
     *
     * @param string $type       The version to request.
     * @param bool $check_exists Check if the version exists.
     *
     * @return string|null The media URL.
     */
    private function buildAssetPath(
        string $type,
        bool $check_exists = false
    ): ?string {
        if (! $this->upload_complete) {
            throw new \Exception('Record is not uploaded yet');
        }

        switch ($type) {
            case 'thumbnail':
                $path = config("rhobooru.media.images.${type}s.storage_path")
                    . "/{$this->hashFolder}/{$this->thumbnail_filename}";
                break;

            case 'preview':
                $path = config("rhobooru.media.images.${type}s.storage_path")
                    . "/{$this->hashFolder}/{$this->preview_filename}";
                break;

            case 'original':
                $path = config("rhobooru.media.images.${type}s.storage_path")
                    . "/{$this->hashFolder}/{$this->filename}";
        }

        if ($check_exists && ! Storage::exists($path)) {
            return null;
        }

        return Storage::url($path);
    }

    /**
     * Searches for records via query.
     *
     * @param  String  $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function search(
        $root,
        array $args,
        \Nuwave\Lighthouse\Schema\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $resolveInfo
    ) {
        $raw_query = trim($args['query']);

        if (! $raw_query) {
            return Record::query();
        }

        $search = new \App\SearchQuery($raw_query);

        $tag_ids = array_map(static function($item) {
            return $item->tag_ids[0];
        }, $search->terms());

        return Record::whereExists(static function($query) use ($tag_ids) {
            $query->select(\DB::raw(1))
                ->from('record_tag')
                ->whereRaw('record_tag.record_id = records.id')
                ->whereIn('record_tag.tag_id', $tag_ids);
        });
    }

    /**
     * Searches for records via query.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function similarRecords(
        $root,
        array $args,
        \Nuwave\Lighthouse\Schema\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $resolveInfo
    ) {
        $max_distance = 20; // TODO: Replace with system setting.

        $phash = array_key_exists('phash', $args) && is_numeric($args['phash'])
            ? $args['phash']
            : null;

        $id = array_key_exists('id', $args) && is_numeric($args['id'])
            ? $args['id']
            : null;

        if ($id !== null) {
            $phash = Record::findOrFail($id)->phash;
        }

        return Record::select('*')
            ->similarTo($max_distance, $phash, $id)
            ->orderBy('distance');
    }
}
