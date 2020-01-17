<?php

namespace App\Models;

use \App\Helpers\ImageHelper;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Traits\UserAudits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'content_rating_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('uploaded', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function ($query) {
                $query->where('upload_complete', true);
            });
        });
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoEagerLoads($query){
        return $query->setEagerLoads([]);
    }



    public function verifyMD5(string $path): bool
    {
        $storedHash = ImageHelper::imageToMD5($path);

        return $storedHash === $this->md5;
    }

    /**
     * Processes an uploaded file for this Record.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Path where file was stored
     */
    public function uploadFile(\Illuminate\Http\UploadedFile $file): string
    {
        // Make sure the user is logged in.
        if(!Auth::check())
        {
            //throw new \Exception("Not authenticated");
        }

        // Make sure the record isn't already uploaded.
        if($this->upload_complete)
        {
            throw new \Exception("Record already marked as uploaded");
        }



        // Build the paths and filenames.
        $filename = $this->md5;
        $thumbnail_name = $this->md5 . '_thumbnail.' . config('rhobooru.media.images.thumbnails.format');
        $preview_name = $this->md5 . '_preview.' . config('rhobooru.media.images.previews.format');

        $staging_path = config('rhobooru.media.images.staging_path');
        $final_path = config('rhobooru.media.images.originals.storage_path') . '/' . substr($this->md5, 0, 3);
        $thumbnail_path = config('rhobooru.media.images.thumbnails.storage_path') . '/' .  substr($this->md5, 0, 3);
        $preview_path = config('rhobooru.media.images.previews.storage_path') . '/' .  substr($this->md5, 0, 3);


        $full_staging_path = $staging_path . '/' . $filename;
        $full_staging_thumbnail_path = $staging_path . '/' . $thumbnail_name;
        $full_staging_preview_path = $staging_path . '/' . $preview_name;

        $full_thumbnail_path = $thumbnail_path . '/' . $thumbnail_name;
        $full_preview_path = $preview_path . '/' . $preview_name;



        // Check if the file already exists.
        $file_exists_in_staging = Storage::exists($full_staging_path);

        // If it exists in staging, we may be racing with another upload
        // or retrying a failed upload.
        if($file_exists_in_staging === true)
        {
            Storage::delete($full_staging_path);
        }



        // Save the file into the staging path.
        $file->storePubliclyAs($staging_path, $filename);

        $this->file_extension = image_type_to_extension(exif_imagetype(Storage::path($full_staging_path)), false);

        $filename = $this->md5 . '.' . $this->file_extension;
        $full_final_path = $final_path . '/' . $filename;
        $old_full_staging_path = $full_staging_path;
        $full_staging_path = $staging_path . '/' . $filename;



        // Check if the file already exists.
        $file_exists_in_staging = Storage::exists($full_staging_path);

        // If it exists in staging, we may be racing with another upload
        // or retrying a failed upload.
        if($file_exists_in_staging === true)
        {
            Storage::delete($full_staging_path);
        }

        Storage::move($old_full_staging_path, $full_staging_path);


        $image = Image::make(Storage::path($full_staging_path));



        $file_exists_in_final = Storage::exists($full_final_path);

        // If it exists in the final path, we are uploading an MD5
        // duplicate.
        if($file_exists_in_final === true)
        {
            throw new \Exception("File already exists for MD5");
        }



        // If the saved file's hash doesn't equal our
        // expected hash, the file may be corrupt.
        if(!$this->verifyMD5(Storage::path($full_staging_path)))
        {
            Storage::delete($full_staging_path);

            throw new \Exception("MD5 mismatch: File corrupted in transit to server.");
        }

        // Calculate the pHash of the saved file.
        $pHash = ImageHelper::imageToPHash(Storage::path($full_staging_path));

        $similar_records = Record::whereRaw('BIT_COUNT(phash ^ ?) < 1', [$pHash])->pluck('id');

        if($similar_records->isNotEmpty())
        {
            //Storage::delete($full_staging_path);

            //throw new \Exception("Similar records: " . implode(', ', $similar_records->toArray()));
        }



        $generated_thumbnail_path = ImageHelper::makeThumbnail(Storage::path($full_staging_path));
        if($generated_thumbnail_path != null)
        {
            Storage::move($full_staging_thumbnail_path, $full_thumbnail_path);
        }

        $generated_preview_path = ImageHelper::makePreview(Storage::path($full_staging_path));
        if($generated_preview_path != null)
        {
            Storage::move($full_staging_preview_path, $full_preview_path);
        }



        // Move file from local staging to final storage.
        Storage::move($full_staging_path, $full_final_path);

        // If the moved file's hash doesn't equal our
        // expected hash, the file may be corrupt.
        if(!$this->verifyMD5(Storage::path($full_final_path)))
        {
            Storage::delete($full_final_path);

            throw new \Exception("MD5 mismatch: File corrupted in transit to storage.");
        }



        // Update the record.
        try
        {
            $this->upload_complete = true;
            $this->phash = $pHash;
            $this->width = $image->width();
            $this->height = $image->height();
            $this->file_size = $image->filesize();
            $this->aspect_ratio = $this->width / $this->height;
            $this->save();
        }
        catch(\Exception $e)
        {
            Storage::delete($full_final_path);
            Storage::delete($full_thumbnail_path);
            Storage::delete($full_preview_path);

            throw $e;
        }



        return $full_final_path;
    }

    /**
     * Get the record's image url.
     *
     * @return string
     */
    public function getImageAttribute(): string
    {
        // Make sure the record is already uploaded.
        if(!$this->upload_complete)
        {
            throw new \Exception("Record is not completely uploaded yet");
        }



        // Build the paths and filenames.
        $filename = $this->md5 . '.' . $this->file_extension;

        return asset('storage/uploads/images/' . substr($this->md5, 0, 3) . '/' . $filename);
    }

    /**
     * Get the record's thumbnail url.
     *
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        // Make sure the record is already uploaded.
        if(!$this->upload_complete)
        {
            throw new \Exception("Record is not completely uploaded yet");
        }



        // Build the paths and filenames.
        $filename = $this->md5 . '.' . $this->file_extension;
        $thumbnail_name = $this->md5 . '_thumbnail.' . config('rhobooru.media.images.thumbnails.format');

        $thumbnail_path = config('rhobooru.media.images.thumbnails.storage_path') . '/' . substr($this->md5, 0, 3);

        return asset('storage/uploads/thumbnails/' . substr($this->md5, 0, 3) . '/' . $thumbnail_name);
    }

    /**
     * Get the record's preview url.
     *
     * @return string|null
     */
    public function getPreviewAttribute(): ?string
    {
        // Make sure the record is already uploaded.
        if(!$this->upload_complete)
        {
            throw new \Exception("Record is not completely uploaded yet");
        }



        // Build the paths and filenames.
        $filename = $this->md5 . '.' . $this->file_extension;
        $preview_name = $this->md5 . '_preview.' . config('rhobooru.media.images.previews.format');

        $preview_path = config('rhobooru.media.images.previews.storage_path') . '/' . substr($this->md5, 0, 3);

        if(!Storage::exists($preview_path . '/' . $preview_name))
        {
            return null;
        }

        return asset('storage/uploads/previews/' . substr($this->md5, 0, 3) . '/' . $preview_name);
    }

    /**
     * Searches for records via query.
     *
     * @param  String  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function search($root, array $args, \Nuwave\Lighthouse\Schema\Context $context, \GraphQL\Type\Definition\ResolveInfo $resolveInfo)
    {
        $raw_query = trim($args['query']);

        if(!$raw_query)
        {
            return Record::query();
        }

        $search = new \App\SearchQuery($raw_query);

        $tag_ids = array_map(function($item){ return $item->tag_ids[0];}, $search->terms());

        return Record::whereExists(function ($query) use($tag_ids) {
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
    public function similarRecords($root, array $args, \Nuwave\Lighthouse\Schema\Context $context, \GraphQL\Type\Definition\ResolveInfo $resolveInfo)
    {
        $id = false;
        $phash = false;

        if(array_key_exists('id', $args))
        {
            $id = $args['id'];
        }
        else if(array_key_exists('phash', $args) && is_numeric($args['phash']))
        {
            $phash = $args['phash'];
        }

        if($id)
        {
            $record = Record::findOrFail($id);
            $phash = $record->phash;
        }

        $query = Record::select('*')
            ->addSelect(\DB::raw('BIT_COUNT(phash ^ ' . $phash . ') as `distance`'))
            ->whereRaw('BIT_COUNT(phash ^ ?) < ?', [$phash, 20])
            ->orderBy('distance');

        if($id)
        {
            $query->where('id', '!=', $id);
        }

        return $query;
    }
}
