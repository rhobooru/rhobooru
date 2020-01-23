<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('timestampUsers', fn(bool $nullable = false) => $this->addTimestampUsers($nullable));

        Blueprint::macro('softDeletesUser', fn() => $this->addSoftDeletesUser());
    }

    /**
     * Adds *_by_user_id columns to a table.
     *
     * @param bool $nullable If the created_by_user_id field should be nullable.
     *
     * @return void
     */
    private function addTimestampUsers(bool $nullable = false)
    {
        if ($nullable === true) {
            $this->unsignedbigInteger('created_by_user_id')->nullable();
        } else {
            $this->unsignedbigInteger('created_by_user_id');
        }

        $this->unsignedbigInteger('updated_by_user_id')->nullable();

        $this->foreign('created_by_user_id')->references('id')->on('users');
        $this->foreign('updated_by_user_id')->references('id')->on('users');

        $this->index('created_by_user_id');
        $this->index('updated_by_user_id');
    }

    /**
     * Adds deleted_by_user_id column to a table.
     *
     * @return void
     */
    private function addSoftDeletesUser()
    {
        $this->unsignedbigInteger('deleted_by_user_id')->nullable();

        $this->foreign('deleted_by_user_id')->references('id')->on('users');

        $this->index('deleted_by_user_id');
    }
}
