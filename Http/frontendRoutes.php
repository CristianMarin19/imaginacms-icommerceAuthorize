<?php

use Illuminate\Routing\Router;

    $router->group(['prefix'=>'icommerceauthorize'],function (Router $router){
        $locale = LaravelLocalization::setLocale() ?: App::getLocale();

        $router->get('/{eorderID}/{etransactionID}/{ecurrencyID}', [
            'as' => 'icommerceauthorize',
            'uses' => 'PublicController@index',
        ]);

        $router->get('/send/{orderID}/{transactionID}/{oval}/{odes}', [
            'as' => 'icommerceauthorize.send',
            'uses' => 'PublicController@send',
        ]);
       

    });