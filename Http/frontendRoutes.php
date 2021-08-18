<?php

use Illuminate\Routing\Router;

    $router->group(['prefix'=>'icommerceauthorize'],function (Router $router){
        $locale = LaravelLocalization::setLocale() ?: App::getLocale();

        $router->get('/{eUrl}', [
            'as' => 'icommerceauthorize',
            'uses' => 'PublicController@index',
        ]);

        $router->get('/process-payment/{orderID}/{transactionID}/{oval}/{odes}', [
            'as' => 'icommerceauthorize.processPayment',
            'uses' => 'PublicController@processPayment',
        ]);
       

    });