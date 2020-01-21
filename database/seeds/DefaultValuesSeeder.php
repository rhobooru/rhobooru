<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionsHelper as Perms;
use \App\Models\SettingGroup;
use \App\Models\Setting;

class DefaultValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedSiteThemes();
        $this->seedDateFormats();
        $this->seedRecordFits();
        $this->seedRecordTypes();
        $this->seedMediaFormats();
        $this->seedContentTypes();
        $this->seedAccessTypes();
        $this->seedFolderTypes();
        $this->seedTagAssociationTypes();
        $this->seedSettingGroups();
        $this->seedSettings();

        // Seed permissions, then roles, then users.
        $this->seedRolesAndPermissions();
        $this->seedUsers();
    }

    public function seedRecordTypes()
    {
        \App\Models\RecordType::create([
            'requires_player_controls' => false,
            'name' => 'Static Image',
            'description' => 'Static image formats (eg. jpg, single-frame png, single-frame gif)',
        ]);

        \App\Models\RecordType::create([
            'requires_player_controls' => true,
            'name' => 'Animated Image',
            'description' => 'Animated image formats (eg. multi-frame png, multi-frame gif)',
        ]);

        \App\Models\RecordType::create([
            'requires_player_controls' => true,
            'name' => 'Video',
            'description' => 'Video formats (eg. webm, mkv, mp4)',
        ]);
    }

    public function seedMediaFormats()
    {
        $imageType = 1;
        $animatedImageType = 2;
        $videoType = 3;

        \App\Models\MediaFormat::create([
            'extension' => 'jpg',
            'mime' => 'image/jpeg',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'jpeg',
            'mime' => 'image/jpeg',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'jpe',
            'mime' => 'image/jpeg',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'png',
            'mime' => 'image/png',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'bmp',
            'mime' => 'image/bmp',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'tif',
            'mime' => 'image/tiff',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'tiff',
            'mime' => 'image/tiff',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'webp',
            'mime' => 'image/webp',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'svg',
            'mime' => 'image/svg+xml',
            'record_type_id' => $imageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => false,
        ]);



        \App\Models\MediaFormat::create([
            'extension' => 'gif',
            'mime' => 'image/gif',
            'record_type_id' => $animatedImageType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);



        \App\Models\MediaFormat::create([
            'extension' => 'mpeg',
            'mime' => 'video/mpeg',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'mp4',
            'mime' => 'video/mp4',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'quicktime',
            'mime' => 'video/quicktime',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'vp8',
            'mime' => 'video/vp8',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'ogg',
            'mime' => 'video/ogg',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'webm',
            'mime' => 'video/webm',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'mkv',
            'mime' => 'video/x-matroska',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'flv',
            'mime' => 'video/x-flv',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'avi',
            'mime' => 'video/x-msvideo',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'wmv',
            'mime' => 'video/x-ms-wmv',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);

        \App\Models\MediaFormat::create([
            'extension' => 'f4v',
            'mime' => 'video/x-f4v',
            'record_type_id' => $videoType,
            'accepted_for_upload' => true,
            'can_produce_thumbnails' => true,
        ]);
    }

    public function seedContentTypes()
    {
        \App\Models\ContentRating::create([
            'order' => 0,
            'available_to_anonymous' => true,
            'name' => 'Safe',
            'short_name' => 'S',
            'description' => 'Safe for public, work, or family environments',
        ]);

        \App\Models\ContentRating::create([
            'order' => 1,
            'available_to_anonymous' => true,
            'name' => 'Questionable',
            'short_name' => 'Q',
            'description' => 'Potentially unsafe for public, work, or family environments',
        ]);

        \App\Models\ContentRating::create([
            'order' => 2,
            'available_to_anonymous' => false,
            'name' => 'Explicit',
            'short_name' => 'E',
            'description' => 'Unsafe for public, work, or family environments',
        ]);
    }

    public function seedSiteThemes()
    {
        \App\Models\SiteTheme::create([
            'is_default' => true,
            'name' => 'Dark',
        ]);

        \App\Models\SiteTheme::create([
            'is_default' => false,
            'name' => 'Light',
        ]);
    }

    public function seedDateFormats()
    {
        \App\Models\DateFormat::create([
            'is_default' => true,
            'format' => 'yyyy-MM-dd',
        ]);

        \App\Models\DateFormat::create([
            'format' => 'MM/dd/yyyy',
        ]);

        \App\Models\DateFormat::create([
            'format' => 'MMM dd, yyyy',
        ]);

        \App\Models\DateFormat::create([
            'format' => 'dd MMM, yyyy',
        ]);

        \App\Models\DateFormat::create([
            'format' => 'dd MMMM, yyyy',
        ]);
    }

    public function seedRecordFits()
    {
        \App\Models\RecordFit::create([
            'is_default' => true,
            'name' => 'Auto Fit',
            'description' => 'For normal aspect ratios, will fit to both width and height. For tall media, will fit to width. For wide media, will fit to height.',
        ]);

        \App\Models\RecordFit::create([
            'is_default' => false,
            'name' => 'Fit Both',
            'description' => 'Constrains media to both the screen width and height.',
        ]);

        \App\Models\RecordFit::create([
            'is_default' => false,
            'name' => 'Fit Width',
            'description' => 'Constrains media to the screen width.',
        ]);

        \App\Models\RecordFit::create([
            'is_default' => false,
            'name' => 'Fit Height',
            'description' => 'Constrains media to the screen height.',
        ]);

        \App\Models\RecordFit::create([
            'is_default' => false,
            'name' => 'No Fit',
            'description' => 'Displays media at its native size.',
        ]);
    }

    public function seedAccessTypes()
    {
        \App\Models\AccessType::create([
            'static_name' => \App\Models\AccessType::PUBLIC_ACCESS,
            'name' => 'Public',
            'description' => 'Available to everyone. Included in search or browse pages, if applicable.',
        ]);

        \App\Models\AccessType::create([
            'static_name' => \App\Models\AccessType::UNLISTED_ACCESS,
            'name' => 'Unlisted',
            'description' => 'Available to anyone with the link. Excluded from search or browse pages.',
        ]);

        \App\Models\AccessType::create([
            'static_name' => \App\Models\AccessType::FRIENDS_ACCESS,
            'name' => 'Friends-Only',
            'description' => 'Available only to users on your friends list. Included in search or browse pages for your friends, if applicable.',
        ]);

        \App\Models\AccessType::create([
            'static_name' => \App\Models\AccessType::PRIVATE_ACCESS,
            'name' => 'Private',
            'description' => 'Available only to you.',
        ]);
    }

    public function seedFolderTypes()
    {
        \App\Models\FolderType::create([
            'static_name' => \App\Models\FolderType::GENERIC_FOLDER_TYPE,
            'can_be_managed_manually' => true,
            'name' => 'Generic',
            'description' => 'A loose collection of items in no particular order',
        ]);

        \App\Models\FolderType::create([
            'static_name' => \App\Models\FolderType::BOOK_FOLDER_TYPE,
            'can_be_managed_manually' => true,
            'name' => 'Book',
            'description' => 'An ordered collection of pages',
        ]);

        \App\Models\FolderType::create([
            'static_name' => \App\Models\FolderType::FAVORITES_FOLDER_TYPE,
            'can_be_managed_manually' => false,
            'name' => 'Favorites',
            'description' => 'Special user folder for housing that user\'s favorited records. Cannot be created or deleted manually.',
        ]);

        \App\Models\FolderType::create([
            'static_name' => \App\Models\FolderType::QUICK_LIST_FOLDER_TYPE,
            'can_be_managed_manually' => false,
            'name' => 'Quick List',
            'description' => 'Special user folder for housing that user\'s quick-listed records. Cannot be created or deleted manually.',
        ]);
    }

    public function seedTagAssociationTypes()
    {
        \App\Models\TagAssociationType::create([
            'name' => 'Associated With',
            'description' => 'Tags that are similar in concept',
        ]);

        \App\Models\TagAssociationType::create([
            'name' => 'Compare To',
            'description' => 'Tags that are disimilar in concept',
        ]);

        \App\Models\TagAssociationType::create([
            'name' => 'Not To Be Confused With',
            'description' => 'Tags that may be easily confused with each other',
        ]);
    }

    public function seedUsers()
    {
        // Create anonymous user.
        //
        // Used for audits and permissions when users aren't. 
        // authenticated. Cannot be deleted from the system.
        $user = \App\Models\User::create([
            'username' => 'anonymous',
            'password' => Hash::make(Str::random(255)),
        ])->givePermissionTo([
            'tag.view any',
            'record.view any',
        ]);
    
        $user->system_account = true;
        $user->anonymous_account = true;
        $user->save();

        // Create system user.
        //
        // Used for non-import background tasks such as 
        // stat recalculations and data pruning.
        //
        // Should never be logged into manually.
        $user = \App\Models\User::create([
            'username' => 'System',
            'password' => Hash::make(Str::random(255)),
        ])->givePermissionTo(Permission::all());
    
        $user->system_account = true;
        $user->save();

        // Create automation user.
        //
        // Used by crawlers and background jobs for record importing.
        // Used for interfacing with stand-alone rhobot instances.
        //
        // Will want to keep these tasks under a seperate user in the logs
        // for easier cleanup/disaster recovery.
        $user = \App\Models\User::create([
            'username' => 'rhobot',
            'password' => Hash::make(Str::random(255)),
        ])->assignRole('Automation');
    
        $user->system_account = true;
        $user->save();

        // Create admin user.
        //
        // Used as site super-admin. Cannot be deleted from the system.
        //
        // Should only be used when necessary. Further regular-admin accounts
        // should be made for daily use.
        \App\Models\User::create([
            'username' => 'Admin',
            'password' => Hash::make('password'),
            'avatar' => 'admin.png',
        ])->assignRole('Admin');
    }

    public function seedRolesAndPermissions()
    {
        // create permissions
        Perms::enterAllPermissions();

        // The automation role is given to 1st class bots.
        Role::create([
            'name' => 'Automation'
        ])->givePermissionTo(Permission::all());

        // The admin role is for site administrators that can
        // manage virtually everything on the site.
        Role::create([
            'name' => 'Admin'
        ])->givePermissionTo(Permission::all());

        // The moderator role is for users that can 
        // review other users' actions for approval
        // or denial.
        Role::create([
            'name' => 'Moderator'
        ])->givePermissionTo([
            'tag.create', 
        ]);

        // The tag curator role is for users with extra
        // power to manage the global tag list and
        // the tags on records.
        //
        // They can also receive notifications when
        // action is requested or needed for tags
        // (eg. alias requests).
        Role::create([
            'name' => 'Tag Curator'
        ])->givePermissionTo([
            'tag.create', 
        ]);

        // The translator role is for users with extra
        // power to manage translations around the site,
        // including notes and book titles.
        //
        // They can also receive notifications when
        // action is requested or needed for translations
        // (eg. translation requests).
        Role::create(['name' => 'Translator']);

        // The user role is the basic role for registered users.
        Role::create(['name' => 'User']);
    }

    public function seedSettingGroups()
    {
        /**
         * System Setting Groups
         */

        $users = SettingGroup::create([
            'name' => 'Users',
            'description' => 'Settings related to user accounts.',
        ]);

        $media = SettingGroup::create([
            'name' => 'Media',
            'description' => 'Settings related to processing of uploaded media.',
        ]);

            $images = SettingGroup::create([
                'name' => 'Images',
                'description' => 'Settings related to processing of uploaded images.<br><br>
                See the `avatar` section for avatar-related settings.<br><br>
                Acceptable image formats are tagged in the `media_formats` table.',
                'setting_group_id' => $media->id,
            ]);

                $originals = SettingGroup::create([
                    'name' => 'Originals',
                    'description' => 'Original files are the unmodified uploads.',
                    'setting_group_id' => $images->id,
                ]);

                $thumbnails = SettingGroup::create([
                    'name' => 'Thumbnails',
                    'description' => 'Thumbnails are the images shown on the record listing pages.<br>
                    eg. main search, folder views, tag pages',
                    'setting_group_id' => $images->id,
                ]);

                $previews = SettingGroup::create([
                    'name' => 'Previews',
                    'description' => 'Previews are reasonably-sized versions of images that save bandwidth without much loss in quality.<br>
                    eg. record pages',
                    'setting_group_id' => $images->id,
                ]);

            $videos = SettingGroup::create([
                'name' => 'Videos',
                'description' => 'Settings related to processing of uploaded videos.<br><br>
                Acceptable video formats are tagged in the `media_formats` table.',
                'setting_group_id' => $media->id,
            ]);



        /**
         * User Setting Groups
         */
    }

    public function seedSettings()
    {
        /**
         * System Setting Groups
         */
        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Users')->first()->id,
            'system_setting' => true,
            'key' => 'anonymous_user_id',
            'name' => 'Anonymous User',
            'description' => 'The dummy account for anonymous users.<br><br>
            This account should be created automatically during installation but if, for any reason, it changes, pick the new dummy account here.',
            'default_value' => 1,
            'control' => 'select',
            'references_model' => \App\Models\User::class,
            'references_value' => 'id',
            'references_text' => 'username',
            'references_method' => 'withoutGlobalScopes()->systemAccount()'
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Images')->first()->id,
            'system_setting' => true,
            'key' => 'staging_path',
            'name' => 'Staging Path',
            'description' => 'The path where unprocessed files will be stored.',
            'default_value' => 'staging',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Images')->first()->id,
            'system_setting' => true,
            'key' => 'determine_if_animated',
            'name' => 'Determine If Animated',
            'description' => 'If this setting is on, uploaded images will be inspected to attempt to determine if they are truly animated or not.<br><br>
            If this is off, all uploads with a MIME type tagged as an animated format in `media_formats` will be considered animated. However, `gif`s are sometimes static and erroneously marking them as animated could confuse users.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Images')->first()->id,
            'system_setting' => true,
            'key' => 'max_file_size',
            'name' => 'Max File Size',
            'description' => 'The maximum size (in bytes) for image uploads.<br><br>
            This cannot be larger than `upload_max_filesize` and `post_max_size` in php.ini/.htaccess/vhost.conf/etc',
            'default_value' => App\Helpers\EnvironmentHelper::getMaxUploadSize(),
            'control' => 'number',
            'minimum_value' => 0,
            'maximum_value' => App\Helpers\EnvironmentHelper::getMaxUploadSize(),
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Originals')->first()->id,
            'system_setting' => true,
            'key' => 'original_storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where original images will be saved.',
            'default_value' => 'uploads/images',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'thumbnail_storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where thumbnail images will be saved.',
            'default_value' => 'uploads/thumbnails',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'thumbnail_width',
            'name' => 'Width',
            'description' => 'Width, in pixels, for image-type record thumbnails.<br><br>
            Images larger than this size will be scaled down to fit, keeping the original aspect ratio.<br><br>            
            Images smaller than this in both dimensions will not be scaled. Instead, the original image will be served.<br><br>            
            The original files will not be altered.',
            'default_value' => 200,
            'control' => 'number',
            'minimum_value' => 0,
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'thumbnail_height',
            'name' => 'Height',
            'description' => 'Height, in pixels, for image-type record thumbnails.<br><br>
            Images larger than this size will be scaled down to fit, keeping the original aspect ratio.<br><br>            
            Images smaller than this in both dimensions will not be scaled. Instead, the original image will be served.<br><br>            
            The original files will not be altered.',
            'default_value' => 200,
            'control' => 'number',
            'minimum_value' => 0,
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'thumbnail_format',
            'name' => 'Format',
            'description' => 'Image format for generated thumbnails.<br><br>
            Ensure that the server has whatever gd or imagick extensions are needed to support this format.<br><br>
            eg. `webp`, `jpeg`, `png`',
            'default_value' => 'webp',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'thumbnail_quality',
            'name' => 'Quality',
            'description' => 'If the thumbnail format supports a quality setting, this is where it\'s set. Otherwise, this will be ignored.',
            'default_value' => 80,
            'control' => 'number',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'allow_null' => true,
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Previews')->first()->id,
            'system_setting' => true,
            'key' => 'preview_storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where preview images will be saved.',
            'default_value' => 'uploads/previews',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Previews')->first()->id,
            'system_setting' => true,
            'key' => 'preview_width',
            'name' => 'Width',
            'description' => 'Width, in pixels, for image-type record previews.<br><br>
            Images larger than this size will be scaled down to fit, keeping the original aspect ratio.<br><br>            
            Images smaller than this in both dimensions will not be scaled. Instead, the original image will be served.<br><br>            
            The original files will not be altered.',
            'default_value' => 1200,
            'control' => 'number',
            'minimum_value' => 0,
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Previews')->first()->id,
            'system_setting' => true,
            'key' => 'preview_height',
            'name' => 'Height',
            'description' => 'Height, in pixels, for image-type record previews.<br><br>
            Images larger than this size will be scaled down to fit, keeping the original aspect ratio.<br><br>            
            Images smaller than this in both dimensions will not be scaled. Instead, the original image will be served.<br><br>            
            The original files will not be altered.',
            'default_value' => 1200,
            'control' => 'number',
            'minimum_value' => 0,
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Previews')->first()->id,
            'system_setting' => true,
            'key' => 'preview_format',
            'name' => 'Format',
            'description' => 'Image format for generated previews.<br><br>
            Ensure that the server has whatever gd or imagick extensions are needed to support this format.<br><br>
            eg. `webp`, `jpeg`, `png`',
            'default_value' => 'webp',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => SettingGroup::where('name', 'Previews')->first()->id,
            'system_setting' => true,
            'key' => 'preview_quality',
            'name' => 'Quality',
            'description' => 'If the preview format supports a quality setting, this is where it\'s set. Otherwise, this will be ignored.',
            'default_value' => 85,
            'control' => 'number',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'allow_null' => true,
        ]);


        /**
         * User Setting Groups
         */
    }
}

