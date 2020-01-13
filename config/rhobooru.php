<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    |
    | Here are settings related to user accounts.
    |
    */

    'users' => [

        /*
         * The `id` value from the `users` table for the
         * anonymous account. 
         * 
         * This account should be created automatically during 
         * installation but if, for any reason, it changes, set 
         * this value to the `id` value where 
         * `users`.`anonymous_account` = true, clear the config cache, 
         * and restart the system.
         */
        'anonymous_user_id' => 1,

    ],



    /*
    |--------------------------------------------------------------------------
    | Image Processing
    |--------------------------------------------------------------------------
    |
    | Here are settings related to processing of uploaded images.
    | 
    | See the `avatar` section for avatar-related settings.
    |
    | Acceptable image formats are tagged in the `media_formats` table.
    |
    */

    'image_processing' => [

        /*
         * Original files are the unmodified uploads.
         */
        'originals' => [

            /*
             * The path where original images will be saved.
             */
            'storage_path' => 'public/uploads/images/',

        ],

        /*
         * Thumbnails are the images shown on the record listing pages.
         * eg. main search, folder views, tag pages
         */
        'thumbnails' => [

            /*
             * The path where thumbnail images will be saved.
             */
            'storage_path' => 'public/uploads/thumbnails/',

            /*
             * Resolution, in pixels, for image-type record thumbnails.
             * 
             * Images larger than this size in either dimension will be
             * scaled down to fit within this bounding box, keeping the
             * original aspect ratio.
             * 
             * Images smaller than this in both dimensions will not be
             * scaled. Instead, the original image will be served.
             * 
             * The original files will not be altered.
             */
            'width'   => 200,
            'height'  => 200,
    
            /*
             * Image format for generated thumbnails.
             * 
             * Ensure that the server has whatever gd or imagick
             * extensions are needed to support this format.
             * 
             * eg. `webp`, `jpeg`, `png`
             */
            'format'  => 'webp',

            /*
             * If the image format chosen above supports a quality
             * setting, this is where it's set. Otherwise, this will
             * be ignored.
             */
            'format_quality' => 80,

        ],

        /*
         * Previews are reasonably-sized versions of images
         * that save bandwidth without much loss in quality.
         * eg. record pages
         */
        'previews' => [

            /*
             * The path where preview images will be saved.
             */
            'storage_path' => 'public/uploads/previews/',

            /*
             * Resolution, in pixels, for image-type record previews.
             * 
             * Images larger than this size in either dimension will be
             * scaled down to fit within this bounding box, keeping the
             * original aspect ratio.
             * 
             * Images smaller than this in both dimensions will not be
             * scaled. Instead, the original image will be served.
             * 
             * The original files will not be altered.
             */
            'width'   => 1200,
            'height'  => 1200,
    
            /*
             * Image format for generated thumbnails.
             * 
             * Ensure that the server has whatever gd or imagick
             * extensions are needed to support this format.
             * 
             * eg. `webp`, `jpeg`, `png`
             */
            'format'  => 'webp',

            /*
             * If the image format chosen above supports a quality
             * setting, this is where it's set. Otherwise, this will
             * be ignored.
             */
            'format_quality' => 85,

        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Animated Image Processing
    |--------------------------------------------------------------------------
    |
    | Here are settings related to processing of uploaded animated images.
    |
    | Animated image formats are tagged in the `media_formats` table.
    |
    */

    'animated_image_processing' => [

        /*
         * If this setting is on, uploaded images will be inspected
         * to attempt to determine if they are truly animated or not.
         * 
         * For instance, if this is off, all `gif` uploads will be
         * considered animated. However, `gif`s are sometimes static
         * and marking them as animated could confuse users.
         */
        'determine_if_animated' => true,

    ],



    /*
    |--------------------------------------------------------------------------
    | Video Processing
    |--------------------------------------------------------------------------
    |
    | Here are settings related to processing of uploaded videos.
    |
    | Acceptable video formats are tagged in the `media_formats` table.
    |
    */

    'video_processing' => [

        /*
         * Determines if a thumbnail will be generated for
         * the video. 
         * 
         * Video thumbnails are shown where ever image thumbnails
         * are also shown, such as the main search and tag pages.
         * 
         * If turned off, a generic placeholder thumbnail will
         * be shown instead.
         */
        'generate_thumbnail' => true,

        /*
         * The percentage into the video where the thumbnail
         * should be grabbed.
         * 
         * For instance, a setting of `0.5` will grab the 
         * thumbnail from 50% of the way into the video.
         */
        'thumbnail_position' => 0.1,

        /*
         * Determines if a preview will be generated for
         * the video.
         * 
         * Video previews are short, animated image formats
         * showing segments of the video. They are shown when
         * the user hovers over a video thumbnail.
         */
        'generate_preview' => true,

    ],

];
