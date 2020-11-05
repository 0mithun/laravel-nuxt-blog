<?php

namespace App\Providers;

use App\Repositories\Contracts\CommentContract;
use App\Repositories\Contracts\DesignContract;
use App\Repositories\Contracts\InvitationContract;
use App\Repositories\Contracts\TeamContract;
use App\Repositories\Contracts\UserContract;
use App\Repositories\Eloquent\CommentRepository;
use App\Repositories\Eloquent\DesignRepository;
use App\Repositories\Eloquent\InvitationRepository;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->bind(DesignContract::class, DesignRepository::class);
        $this->app->bind(UserContract::class, UserRepository::class);
        $this->app->bind(CommentContract::class, CommentRepository::class);
        $this->app->bind(TeamContract::class,TeamRepository::class);
        $this->app->bind(InvitationContract::class, InvitationRepository::class);
    }
}
