<?php

use \App\Models\Setting;
use \App\Models\SettingGroup;
use App\Helpers\PermissionsHelper as Perms;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DefaultValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedRecordFits();
        $this->seedRecordFetches();
        $this->seedRecordTypes();
        $this->seedMediaFormats();
        $this->seedContentTypes();
        $this->seedAccessTypes();
        $this->seedFolderTypes();
        $this->seedTagAssociationTypes();
        $this->seedSettingGroups();
        $this->seedSystemSettingDefintions();
        $this->seedUserSettingDefintions();
        $this->seedSystemSettings();

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

    public function seedRecordFetches()
    {
        \App\Models\RecordFetch::create([
            'is_default' => true,
            'name' => 'Always fetch previews',
            'description' => 'Lowest data usage, lowest image quality. Always fetches resized previews and allows you to request the original, if you want.',
        ]);

        \App\Models\RecordFetch::create([
            'is_default' => false,
            'name' => 'Fetch previews and auto-upgrade',
            'description' => 'Best experience, highest data usage. Fetches resized previews and automatically upgrades it to the original image in the background.',
        ]);

        \App\Models\RecordFetch::create([
            'is_default' => false,
            'name' => 'Always fetch originals',
            'description' => 'Best image quality, slowest. Always fetches the original images but will take longer before you see anything.',
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
        // authenticated. Cannot be deleted from the app.
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
        // Used as site super-admin. Cannot be deleted from the app.
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
            'key' => 'rhobooru.users',
            'name' => 'Users',
            'description' => 'Settings related to user accounts.',
        ]);

        $media = SettingGroup::create([
            'key' => 'rhobooru.media',
            'name' => 'Media',
            'description' => 'Settings related to processing of uploaded media.',
        ]);

            $images = SettingGroup::create([
                'key' => 'rhobooru.media.images',
                'name' => 'Images',
                'description' => 'Settings related to processing of uploaded images.<br><br>
                See the `avatar` section for avatar-related settings.<br><br>
                Acceptable image formats are tagged in the `media_formats` table.',
                'setting_group_id' => $media->id,
            ]);

                $originals = SettingGroup::create([
                    'key' => 'rhobooru.media.images.originals',
                    'name' => 'Originals',
                    'description' => 'Original files are the unmodified uploads.',
                    'setting_group_id' => $images->id,
                ]);

                $thumbnails = SettingGroup::create([
                    'key' => 'rhobooru.media.images.thumbnails',
                    'name' => 'Thumbnails',
                    'description' => 'Thumbnails are the images shown on the record listing pages.<br>
                    eg. main search, folder views, tag pages',
                    'setting_group_id' => $images->id,
                ]);

                $previews = SettingGroup::create([
                    'key' => 'rhobooru.media.images.previews',
                    'name' => 'Previews',
                    'description' => 'Previews are reasonably-sized versions of images that save bandwidth without much loss in quality.<br>
                    eg. record pages',
                    'setting_group_id' => $images->id,
                ]);

            $videos = SettingGroup::create([
                'key' => 'rhobooru.media.videos',
                'name' => 'Videos',
                'description' => 'Settings related to processing of uploaded videos.<br><br>
                Acceptable video formats are tagged in the `media_formats` table.',
                'setting_group_id' => $media->id,
            ]);



        /**
         * User Setting Groups
         */

        $display = SettingGroup::create([
            'key' => 'user.display',
            'name' => 'Display',
            'description' => 'Settings related to the display of the site and data.',
        ]);

        $privacy = SettingGroup::create([
            'key' => 'user.privacy',
            'name' => 'Privacy',
            'description' => 'Settings related to your data\'s visibility.',
        ]);

        $moderation = SettingGroup::create([
            'key' => 'user.moderation',
            'name' => 'Moderation',
            'description' => 'Settings related to site and content moderation.',
        ]);

        $records = SettingGroup::create([
            'key' => 'user.records',
            'name' => 'Records',
            'description' => 'Settings related to viewing and manipulating records (images, videos, etc).',
        ]);

        $forum = SettingGroup::create([
            'key' => 'user.forum',
            'name' => 'Forum',
            'description' => 'Settings related to viewing and manipulating forums, threads, and posts.',
        ]);

            $threads = SettingGroup::create([
                'key' => 'user.forum.threads',
                'name' => 'Threads',
                'description' => 'Settings related to viewing and manipulating threads.',
                'setting_group_id' => $forum->id,
            ]);

            $posts = SettingGroup::create([
                'key' => 'user.forum.posts',
                'name' => 'Posts',
                'description' => 'Settings related to viewing and manipulating posts.',
                'setting_group_id' => $forum->id,
            ]);

        $notifications = SettingGroup::create([
            'key' => 'user.notifications',
            'name' => 'Notifications',
            'description' => 'Settings related to notifications and emails.',
        ]);

            $record_notifications = SettingGroup::create([
                'key' => 'user.notifications.records',
                'name' => 'Records',
                'description' => 'Settings related to notifications and emails about records.',
                'setting_group_id' => $notifications->id,
            ]);

            $friend_notifications = SettingGroup::create([
                'key' => 'user.notifications.friends',
                'name' => 'Friends',
                'description' => 'Settings related to notifications and emails about friends.',
                'setting_group_id' => $notifications->id,
            ]);

            $comment_notifications = SettingGroup::create([
                'key' => 'user.notifications.comments',
                'name' => 'Comments',
                'description' => 'Settings related to notifications and emails about comments and posts.',
                'setting_group_id' => $notifications->id,
            ]);

            $dm_notifications = SettingGroup::create([
                'key' => 'user.notifications.dms',
                'name' => 'DMs',
                'description' => 'Settings related to notifications and emails about DMs.',
                'setting_group_id' => $notifications->id,
            ]);

            $moderation_notifications = SettingGroup::create([
                'key' => 'user.notifications.moderation',
                'name' => 'Moderation',
                'description' => 'Settings related to notifications and emails about moderator actions.',
                'setting_group_id' => $notifications->id,
            ]);

            $following_notifications = SettingGroup::create([
                'key' => 'user.notifications.following',
                'name' => 'Following',
                'description' => 'Settings related to notifications and emails about users you follow.',
                'setting_group_id' => $notifications->id,
            ]);
    }

    public function seedSystemSettingDefintions()
    {
        $groups = SettingGroup::all();

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.users')->first()->id,
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images')->first()->id,
            'system_setting' => true,
            'key' => 'staging_path',
            'name' => 'Staging Path',
            'description' => 'The path where unprocessed files will be stored.',
            'default_value' => 'staging',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images')->first()->id,
            'system_setting' => true,
            'key' => 'determine_if_animated',
            'name' => 'Determine If Animated',
            'description' => 'If this setting is on, uploaded images will be inspected to attempt to determine if they are truly animated or not.<br><br>
            If this is off, all uploads with a MIME type tagged as an animated format in `media_formats` will be considered animated. However, `gif`s are sometimes static and erroneously marking them as animated could confuse users.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images')->first()->id,
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.originals')->first()->id,
            'system_setting' => true,
            'key' => 'storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where original images will be saved.',
            'default_value' => 'uploads/images',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where thumbnail images will be saved.',
            'default_value' => 'uploads/thumbnails',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'width',
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'height',
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'format',
            'name' => 'Format',
            'description' => 'Image format for generated thumbnails.<br><br>
            Ensure that the server has whatever gd or imagick extensions are needed to support this format.<br><br>
            eg. `webp`, `jpeg`, `png`',
            'default_value' => 'webp',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.thumbnails')->first()->id,
            'system_setting' => true,
            'key' => 'quality',
            'name' => 'Quality',
            'description' => 'If the thumbnail format supports a quality setting, this is where it\'s set. Otherwise, this will be ignored.',
            'default_value' => 80,
            'control' => 'number',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'allow_null' => true,
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.previews')->first()->id,
            'system_setting' => true,
            'key' => 'storage_path',
            'name' => 'Storage Path',
            'description' => 'The path where preview images will be saved.',
            'default_value' => 'uploads/previews',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.previews')->first()->id,
            'system_setting' => true,
            'key' => 'width',
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.previews')->first()->id,
            'system_setting' => true,
            'key' => 'height',
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
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.previews')->first()->id,
            'system_setting' => true,
            'key' => 'format',
            'name' => 'Format',
            'description' => 'Image format for generated previews.<br><br>
            Ensure that the server has whatever gd or imagick extensions are needed to support this format.<br><br>
            eg. `webp`, `jpeg`, `png`',
            'default_value' => 'webp',
            'control' => 'textbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'rhobooru.media.images.previews')->first()->id,
            'system_setting' => true,
            'key' => 'quality',
            'name' => 'Quality',
            'description' => 'If the preview format supports a quality setting, this is where it\'s set. Otherwise, this will be ignored.',
            'default_value' => 85,
            'control' => 'number',
            'minimum_value' => 0,
            'maximum_value' => 100,
            'allow_null' => true,
        ]);
    }

    public function seedUserSettingDefintions()
    {
        $groups = SettingGroup::all();

        /**
         * Display
         */

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.display')->first()->id,
            'key' => 'utc_offset',
            'name' => 'UTC Offset',
            'description' => 'Timezone offset for diplaying dates in local time.',
            'default_value' => 0,
            'control' => 'number',
            'minimum_value' => -12,
            'maximum_value' => 14,
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.display')->first()->id,
            'key' => 'date_format',
            'name' => 'Date Format',
            'description' => 'Preferred template for dates.<br><br>
            Leave blank to infer format from timezone.',
            'default_value' => true,
            'control' => 'textbox',
            'allow_null' => true,
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.display')->first()->id,
            'key' => 'enable_relative_dates',
            'name' => 'Enable Relative Dates',
            'description' => 'When on, things that have happened recently will be shown as `x minutes ago` or `x hours ago`. Older things will use a normal date format.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.display')->first()->id,
            'key' => 'dark_theme',
            'name' => 'Dark Theme',
            'description' => 'Turn on for the dark theme. Turn off for the light theme.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);


        /**
         * Privacy
         */

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'allow_friend_requests',
            'name' => 'Allow Friend Requests',
            'description' => 'Whether to allow other users to send you friend requests.<br><br>
            This will not affect your current friends or requests.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'allow_dms_from_anyone',
            'name' => 'Allow DMs From Anyone',
            'description' => 'Whether to allow other users to send you direct messages.<br><br>
            Friends and staff can always send you DMs. Blocked users can never send you DMs.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'hide_profile',
            'name' => 'Hide Profile',
            'description' => 'Whether to allow other users to see your profile.<br><br>
            Friends and staff can always view your profile. Blocked users can never view your profile.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'hide_friends_list',
            'name' => 'Hide Friends List',
            'description' => 'Whether to allow other users to see your friends list.<br><br>
            Friends and staff can always view your friends list. Blocked users can never view your friends list.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'hide_favorites',
            'name' => 'Hide Favorites',
            'description' => 'Whether to allow other users to see your favorites folder.<br><br>
            Staff can always view your favoritess. Blocked users can never view your favorites.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.privacy')->first()->id,
            'key' => 'hide_communities',
            'name' => 'Hide Communities',
            'description' => 'Whether to allow other users to see which communities you belong to.<br><br>
            Friends and staff can always view your communities. Blocked users can never view your communities.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);


        /**
         * Moderation
         */

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.moderation')->first()->id,
            'key' => 'hide_blocked_users',
            'name' => 'Hide Blocked Users',
            'description' => 'If on, all content posted by users on your block list will be completely hidden.<br>
            If off, content will be blurred or collapsed such that you can then choose to see individual content from blocked users.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);


        /**
         * Records
         */

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'record_fit',
            'name' => 'Fitting Strategy',
            'description' => 'The default way that records should be scaled when viewed.',
            'default_value' => \App\Models\RecordFit::default()->first()->id,
            'control' => 'select',
            'references_model' => \App\Models\RecordFit::class,
            'references_value' => 'id',
            'references_text' => 'name',
            'references_description' => 'description',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'record_fetching_strategy',
            'name' => 'Fetching Strategy',
            'description' => 'How the versions of a record should be presented.',
            'default_value' => \App\Models\RecordFetch::default()->first()->id,
            'control' => 'select',
            'references_model' => \App\Models\RecordFetch::class,
            'references_value' => 'id',
            'references_text' => 'name',
            'references_description' => 'description',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'warn_on_preview_download',
            'name' => 'Warn When Downloading Preview',
            'description' => 'If you should be warned when it looks like you\'re trying to download a preview instead of an original.<br><br>
            Originals can always be downloaded with the `Download` button in the record view page.<br><br>
            If you do a lot of right-click/long-press actions on images, you\'ll probably want this off.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'record_infinite_scroll',
            'name' => 'Infinite Scroll',
            'description' => 'Turn on to show the next page of results automatically. Turn off to use traditional page controls.',
            'default_value' => false,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'nested_record_comments',
            'name' => 'Nested Comments',
            'description' => 'If comments below records should be shown in a threaded view based on replies.',
            'default_value' => true,
            'control' => 'checkbox',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'minimum_record_comment_score',
            'name' => 'Minimum Comment Score',
            'description' => 'Comments with a score below this will be collapsed.',
            'default_value' => -10,
            'control' => 'number',
        ]);

        Setting::create([
            'setting_group_id' => $groups->where('key', 'user.records')->first()->id,
            'key' => 'minimum_record_score',
            'name' => 'Minimum Record Score',
            'description' => 'Records with a score below this will not show up in normal searches.<br><br>
            Low-scoring records can still be found by searching their ID, MD5, or pHash or by specifying a score range in your search.',
            'default_value' => -10,
            'control' => 'number',
        ]);


        /**
         * Forum
         */

            /**
             * Threads
             */

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.forum.threads')->first()->id,
                'key' => 'thread_infinite_scroll',
                'name' => 'Infinite Scroll',
                'description' => 'Turn on to show the next page of results automatically. Turn off to use traditional page controls.',
                'default_value' => false,
                'control' => 'checkbox',
            ]);


            /**
             * Posts
             */

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.forum.posts')->first()->id,
                'key' => 'post_infinite_scroll',
                'name' => 'Infinite Scroll',
                'description' => 'Turn on to show the next page of results automatically. Turn off to use traditional page controls.',
                'default_value' => false,
                'control' => 'checkbox',
            ]);

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.forum.posts')->first()->id,
                'key' => 'post_nested_view',
                'name' => 'Nested View',
                'description' => 'If posts should be shown in a threaded view based on replies.',
                'default_value' => true,
                'control' => 'checkbox',
            ]);

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.forum.posts')->first()->id,
                'key' => 'post_minimum_score',
                'name' => 'Minimum Score',
                'description' => 'Posts with a score below this will be collapsed.',
                'default_value' => -10,
                'control' => 'number',
            ]);


        /**
         * Notifications
         */

            /**
             * Records
             */

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.notifications.records')->first()->id,
                'key' => 'notifications_record_approval_email',
                'name' => 'Approval - Email',
                'description' => 'Receive an email when your records are approved.',
                'default_value' => false,
                'control' => 'checkbox',
            ]);

            Setting::create([
                'setting_group_id' => $groups->where('key', 'user.notifications.records')->first()->id,
                'key' => 'notifications_record_approval_push',
                'name' => 'Approval - Push',
                'description' => 'Receive a push notification when your records are approved.',
                'default_value' => false,
                'control' => 'checkbox',
            ]);
    }

    public function seedSystemSettings()
    {
        $settings = Setting::where('system_setting', true)->get();

        foreach ($settings as $setting) {
            SystemSetting::create([
                'setting_id' => $setting->id,
                'value' => $setting->default_value,
            ]);
        }

        SystemSetting::persistAll();
    }
}

