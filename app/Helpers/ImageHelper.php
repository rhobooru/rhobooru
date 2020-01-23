<?php

namespace App\Helpers;

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
     *
     * @return string|null Path to thumbnail
     */
    public static function makeThumbnail(string $input_file): ?string
    {
        $path_info = pathinfo($input_file);

        $width = config('rhobooru.media.images.thumbnails.width');
        $height = config('rhobooru.media.images.thumbnails.height');

        $image = self::fitImage($input_file, $width, $height);

        if ($image === null) {
            return null;
        }

        $thumbnail_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_thumbnail.' . config('rhobooru.media.images.thumbnails.format');

        $thumbnail_quality = config('rhobooru.media.images.thumbnails.format_quality');

        $image->save($thumbnail_path, $thumbnail_quality);

        return $thumbnail_path;
    }

    /**
     * Generates a preview for the given image acoording to
     * config/rhobooru.php settings.
     *
     * @param string $input_file
     *
     * @return string|null Path to preview.
     */
    public static function makePreview(string $input_file): ?string
    {
        $path_info = pathinfo($input_file);

        $width = config('rhobooru.media.images.previews.width');
        $height = config('rhobooru.media.images.previews.height');

        $image = self::fitImage($input_file, $width, $height);

        if ($image === null) {
            return null;
        }

        $preview_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_preview.' . config('rhobooru.media.images.previews.format');

        $preview_quality = config('rhobooru.media.images.previews.format_quality');

        $image->save($preview_path, $preview_quality);

        return $preview_path;
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
    public static function fitImage(string $file, int $max_width, int $max_height): ?\Intervention\Image\Image
    {
        $image = Image::make($file);

        if ($image->width() <= $max_width && $image->height() <= $max_height) {
            return null;
        }

        $image->resize($max_width, $max_height, static function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image;
    }
}
