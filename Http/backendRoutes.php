<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/icommerceauthorize'], function (Router $router) {
    $router->bind('authorizeconfig', function ($id) {
        return app('Modules\IcommerceAuthorize\Repositories\AuthorizeconfigRepository')->find($id);
    });
    $router->get('authorizeconfigs', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.index',
        'uses' => 'AuthorizeconfigController@index',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.index'
    ]);
    $router->get('authorizeconfigs/create', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.create',
        'uses' => 'AuthorizeconfigController@create',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.create'
    ]);
    $router->post('authorizeconfigs', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.store',
        'uses' => 'AuthorizeconfigController@store',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.create'
    ]);
    $router->get('authorizeconfigs/{authorizeconfig}/edit', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.edit',
        'uses' => 'AuthorizeconfigController@edit',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.edit'
    ]);

    $router->put('authorizeconfigs', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.update',
        'uses' => 'AuthorizeconfigController@update',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.edit'
    ]);

    $router->delete('authorizeconfigs/{authorizeconfig}', [
        'as' => 'admin.icommerceauthorize.authorizeconfig.destroy',
        'uses' => 'AuthorizeconfigController@destroy',
        'middleware' => 'can:icommerceauthorize.authorizeconfigs.destroy'
    ]);
// append

});
