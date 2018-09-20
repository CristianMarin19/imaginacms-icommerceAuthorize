<?php

namespace Modules\IcommerceAuthorize\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\IcommerceAuthorize\Events\Handlers\RegisterIcommerceAuthorizeSidebar;

class IcommerceAuthorizeServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIcommerceAuthorizeSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('authorizeconfigs', array_dot(trans('icommerceauthorize::authorizeconfigs')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('icommerceAuthorize', 'permissions');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\IcommerceAuthorize\Repositories\AuthorizeconfigRepository',
            function () {
                $repository = new \Modules\IcommerceAuthorize\Repositories\Eloquent\EloquentAuthorizeconfigRepository(new \Modules\IcommerceAuthorize\Entities\Authorizeconfig());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\IcommerceAuthorize\Repositories\Cache\CacheAuthorizeconfigDecorator($repository);
            }
        );
// add bindings

    }
}
