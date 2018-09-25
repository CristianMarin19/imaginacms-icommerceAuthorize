<?php

namespace Modules\Icommerceauthorize\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Icommerceauthorize\Events\Handlers\RegisterIcommerceAuthorizeSidebar;

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
        $this->publishConfig('icommerceauthorize', 'permissions');

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
            'Modules\Icommerceauthorize\Repositories\AuthorizeconfigRepository',
            function () {
                $repository = new \Modules\Icommerceauthorize\Repositories\Eloquent\EloquentAuthorizeconfigRepository(new \Modules\Icommerceauthorize\Entities\Authorizeconfig());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommerceauthorize\Repositories\Cache\CacheAuthorizeconfigDecorator($repository);
            }
        );
// add bindings

    }
}
