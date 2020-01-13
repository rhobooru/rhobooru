<?php

namespace App\Helpers;

use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class ImageHelper
{
    /**
     * Caluclates the MD5 hash using the contents of the 
     * given file.
     *
     * @param string $file
     * @return string
     */
    public static function imageToMD5(string $file): string
    {
        $hash = md5_file($file);

        return $hash;
    }

    /**
     * Returns the perceptual hash of the given image.
     *
     * @param string $file
     * @return string pHash returned as an integer to make DB storage/comparisons easier.
     */
    public static function imageToPHash(string $file): string
    {
        $hasher = new ImageHash(new DifferenceHash());

        $hash = $hasher->hash($file);

        // MariaDB is a picky bitch about how binaries are stored and used, so convert hex to bigint.
        return base_convert($hash, 16, 10);
    }

    /**
     * Generates a thumbnail for the given image according to 
     * config/rhobooru.php settings.
     *
     * @param string $input_file
     * @return string|null Path to thumbnail
     */
    public static function makeThumbnail(string $input_file): ?string
    {
        $path_info = pathinfo($input_file);

        $width = config('rhobooru.image_processing.thumbnails.width');
        $height = config('rhobooru.image_processing.thumbnails.height');

        $image = \Image::make($input_file);

        if($image->width() <= $width && $image->height() <= $height)
        {
            return null;
        }

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $thumbnail_filename = $path_info['filename'] . '_thumbnail.' . config('rhobooru.image_processing.thumbnails.format');

        $thumbnail_quality = config('rhobooru.image_processing.thumbnails.format_quality');

        $image->save($path_info['dirname'] . '/' . $thumbnail_filename, $thumbnail_quality);

        return $path_info['dirname'] . '/' . $thumbnail_filename;
    }

    /**
     * Generates a preview for the given image acoording to
     * config/rhobooru.php settings.
     *
     * @param string $input_file
     * @return string|null Path to preview.
     */
    public static function makePreview(string $input_file): ?string
    {
        $path_info = pathinfo($input_file);

        $width = config('rhobooru.image_processing.previews.width');
        $height = config('rhobooru.image_processing.previews.height');

        $image = \Image::make($input_file);

        if($image->width() <= $width && $image->height() <= $height)
        {
            return null;
        }

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $preview_filename = $path_info['filename'] . '_preview.' . config('rhobooru.image_processing.previews.format');

        $preview_quality = config('rhobooru.image_processing.previews.format_quality');

        $image->save($path_info['dirname'] . '/' . $preview_filename, $preview_quality);

        return $path_info['dirname'] . '/' . $preview_filename;
    }
}