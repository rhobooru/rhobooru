<?php

namespace App\Helpers;

use App\Models\Record;
use Intervention\Image\Facades\Image;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class ImageHelper
{
    /**
     * Caluclates the MD5 hash using the contents of the
     * given file.
     *
     * @param string $file
     *
     * @return string
     */
    public static function imageToMD5(string $file): string
    {
        return md5_file($file);
    }

    /**
     * Returns the perceptual hash of the given image.
     *
     * @param string $file
     *
     * @return string pHash returned as an integer to make DB
     *                storage/comparisons easier.
     */
    public static function imageToPHash(string $file): string
    {
        $hasher = new ImageHash(new DifferenceHash());

        $hash = $hasher->hash($file);

        // MariaDB is a picky bitch about how binaries
        // are stored and used, so convert hex to dec.
        return base_convert($hash, 16, 10);
    }

    /**
     * Generates a thumbnail for the given image according to
     * the system settings.
     *
     * @param string $input_file
     *
     * @return string|null Path to thumbnail
     */
    public static function makeThumbnail(string $input_file): ?string
    {
        return self::makeVersion(
            $input_file,
            'rhobooru.media.images.thumbnails',
            'generateThumbnailFilename'
        );
    }

    /**
     * Generates a preview for the given image acoording to
     * the system settings.
     *
     * @param string $input_file
     *
     * @return string|null Path to preview.
     */
    public static function makePreview(string $input_file): ?string
    {
        return self::makeVersion(
            $input_file,
            'rhobooru.media.images.previews',
            'generatePreviewFilename'
        );
    }

    /**
     * Generates a version of the given image acoording to
     * the system settings.
     *
     * @param string $input_file
     * @param string $config_root
     * @param string $filename_func
     *
     * @return string|null Path to the version.
     */
    public static function makeVersion(
        string $input_file,
        string $config_root,
        string $filename_func
    ): ?string {
        $path_info = pathinfo($input_file);

        $image = self::fitImage(
            $input_file,
            intval(config("${config_root}.width")),
            intval(config("${config_root}.height"))
        );

        if(! $image){
            return null;
        }

        $path = "{$path_info['dirname']}/" . call_user_func(
            [Record::class, $filename_func],
            $path_info['filename']
        );

        $image->save($path, intval(config("${config_root}.quality")));

        return $path;
    }

    /**
     * Resizes an image to fit within a bounding box, preserving aspect
     * ratio and avoiding upscaling.
     *
     * @param string $file
     * @param int $max_width
     * @param int $max_height
     *
     * @return \Intervention\Image\Image|null
     */
    public static function fitImage(
        string $file,
        int $max_width,
        int $max_height
    ): ?\Intervention\Image\Image {
        $image = Image::make($file);

        if(! $image) {
            throw new \Exception('Creating image failed');
        }

        if ($image->width() <= $max_width
            && $image->height() <= $max_height) {
            return null;
        }

        $image->resize($max_width, $max_height, static function($img) {
            $img->aspectRatio();
            $img->upsize();
        });

        return $image;
    }
}
