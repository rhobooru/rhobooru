<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('real', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function ($query) {
                $query->where('system_account', false)
                      ->orWhere('anonymous_account', true);
            });
        });
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    /**
     * Get the user's folders.
     */
    public function folders()
    {
        return $this->hasMany('App\Models\Folder', 'created_by_user_id');
    }

    /**
     * Get the user's records.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record', 'created_by_user_id');
    }

    /**
     * Get the user's favorites folder.
     */
    public function favoritesFolder()
    {
        return $this->hasOne('App\Models\Folder', 'created_by_user_id')->favorites();
    }

    /**
     * Get the user's quick list folder.
     */
    public function quickListFolder()
    {
        return $this->hasOne('App\Models\Folder', 'created_by_user_id')->quickList();
    }

    /**
     * Get the user's generic folders.
     */
    public function genericFolders()
    {
        return $this->folders()->generic();
    }

    /**
     * Get the user's book folders.
     */
    public function bookFolders()
    {
        return $this->folders()->books();
    }

    /**
     * Restrict a query to the anonymous user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsAnonymous($query)
    {
        return $query->where('anonymous_account', true);
    }

    /**
     * Restrict a query to users that aren't the anonymous user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotAnonymous($query)
    {
        // Since this field needs to be unique, the default value
        // is null, not false. So do a whereNot to avoid problems
        // with clausal combining with orWhere.
        return $query->whereNot('anonymous_account', true);
    }









    /**
     * Creates all the related models for a user (eg. profile and folders).
     */
    public function createUserRelationships()
    {
        // Create user's profile.
        $this->createUserProfile();

        // Create user's favorites folder.
        $this->createUserFavoritesFolder();

        // Create user's quick list folder.
        $this->createUserQuickListFolder();
    }

    /**
     * Creates the user's profile, if it doesn't exist.
     * 
     * @return  \App\Models\Profile
     */
    public function createUserProfile()
    {
        if($this->profile()->exists())
            return $this->profile;

        return \App\Models\Profile::create([
            'user_id' => $this->id,
            'site_theme_id' => \App\Models\SiteTheme::default()->first()->id,
            'date_format_id' => \App\Models\DateFormat::default()->first()->id,
            'record_fit_id' => \App\Models\RecordFit::default()->first()->id,
            'maximum_content_rating_id' => \App\Models\ContentRating::maximum()->first()->id,
        ]);
    }

    /**
     * Creates the user's favorites folder, if it doesn't exist.
     * 
     * @return  \App\Models\Folder
     */
    public function createUserFavoritesFolder()
    {
        if($this->favoritesFolder()->exists())
            return $this->favoritesFolder->first();

        return \App\Models\Folder::create([
            'created_by_user_id' => $this->id,
            'updated_by_user_id' => $this->id,
            'folder_type_id' => \App\Models\FolderType::favorites()->first()->id,
            'access_type_id' => \App\Models\AccessType::public()->first()->id,
            'name' => 'Favorites',
        ]);
    }

    /**
     * Creates the user's quick list folder, if it doesn't exist.
     * 
     * @return  \App\Models\Folder
     */
    public function createUserQuickListFolder()
    {
        if($this->quickListFolder()->exists())
            return $this->quickListFolder->first();

        return \App\Models\Folder::create([
            'created_by_user_id' => $this->id,
            'updated_by_user_id' => $this->id,
            'folder_type_id' => \App\Models\FolderType::quickList()->first()->id,
            'access_type_id' => \App\Models\AccessType::private()->first()->id,
            'name' => 'Quick List',
        ]);
    }

    /**
     * Soft deletes the soft-deleteable related models for a user (eg. folders).
     */
    public function softDeleteUserRelationships()
    {
        // Delete the user's special folders.
        $this->favoritesFolder()->delete();
        $this->quickListFolder()->delete();

        // Delete the user's restricted-access folders.
        $this->genericFolders()->private()->delete();
        $this->bookFolders()->private()->delete();
        $this->genericFolders()->friends()->delete();
        $this->bookFolders()->friends()->delete();

        // Leave the user's public and unlisted folders since those
        // might still be used by others.
    }

    /**
     * Force deletes all the related models for a user (eg. profile and folders).
     */
    public function forceDeleteUserRelationships()
    {
        // Delete the user's profile.
        $this->profile()->delete();

        // Delete the user's folders.
        $this->folders()->forceDelete();
    }

    /**
     * Restores the soft-deleteable related models for a user (eg. folders).
     */
    public function restoreUserRelationships()
    {
        // Restore the user's special folders.
        $this->favoritesFolder()->restore();
        $this->quickListFolder()->restore();

        // Restore the user's restricted-access folders.
        $this->genericFolders()->private()->restore();
        $this->bookFolders()->private()->restore();
        $this->genericFolders()->friends()->restore();
        $this->bookFolders()->friends()->restore();

        // Leave the user's public and unlisted folders since those
        // aren't soft-deleted by the user's deleted status.
    }

    /**
     * Returns the anonymous user account.
     *
     * @return App\Models\User
     */
    public static function anonymous(): User
    {
        return User::withoutGlobalScopes()
            ->findOrFail(config('rhobooru.users.anonymous_user_id'));
    }

    /**
     * Get all user's permissions.
     *
     * @return Object|null
     */
    public function getAllPermissionsAttribute(): ?Object
    {
        return $this->getAllPermissions();
    }

    /**
     * Get a url to the user's avatar.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if(!$this->avatar){
            return null;
        }

        return asset('storage/avatars/' . $this->avatar);
    }
}
