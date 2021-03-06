<?php

namespace App\Providers;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Banner\Banner;
use App\Entity\User\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Entity\Ticket\Ticket;
use Laravel\Horizon\Horizon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerPermissions();
        Passport::routes();
        Horizon::auth(function () {
            return Gate::allows('horizon');
        });
    }

    private function registerPermissions()
    {
        Gate::define('horizon', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('admin-panel', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });
        Gate::define('manage-regions', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-adverts', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('manage-adverts-categories', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('show-advert', function (User $user, Advert $advert) {
            return $user->isAdmin() || $user->isModerator() || $advert->user_id === $user->id;
        });

        Gate::define('manage-own-advert', function (User $user, Advert $advert) {
            return $advert->user_id === $user->id;
        });

        Gate::define('manage-banners', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('manage-own-banner', function (User $user, Banner $banner) {
            return $banner->user_id === $user->id;
        });

        Gate::define('manage-pages', function(User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-tickets', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        Gate::define('manage-own-ticket', function (User $user, Ticket $ticket) {
            return $ticket->user_id === $user->id;
        });
    }
}
