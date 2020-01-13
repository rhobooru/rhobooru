<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionsHelper as Perms;

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
}

