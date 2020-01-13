<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class BlueprintMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('timestampUsers', function (bool $nullable = false) { 
            if($nullable === true)
            {
                $this->unsignedbigInteger('created_by_user_id')->nullable();
            }
            else
            {
                $this->unsignedbigInteger('created_by_user_id');
            }

            $this->unsignedbigInteger('updated_by_user_id')->nullable();

            $this->foreign('created_by_user_id')->references('id')->on('users');
            $this->foreign('updated_by_user_id')->references('id')->on('users');

            $this->index('created_by_user_id');
            $this->index('updated_by_user_id');
        });

        Blueprint::macro('softDeletesUser', function () { 
            $this->unsignedbigInteger('deleted_by_user_id')->nullable();

            $this->foreign('deleted_by_user_id')->references('id')->on('users');

            $this->index('deleted_by_user_id');
        });
    }
}
