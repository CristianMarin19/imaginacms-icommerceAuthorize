<?php

namespace Modules\Icommerceauthorize\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommerceauthorizeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $options['mainimage'] = null;
        $options['api_login'] = "";
        $options['transaction_key'] = "";
        $options['client_key'] = "";
        $options['mode'] = "sandbox";
       
        
        $params = array(
            'title' => trans('icommerceauthorize::icommerceauthorizes.single'),
            'description' => trans('icommerceauthorize::icommerceauthorizes.description'),
            'name' => config('asgard.icommerceauthorize.config.paymentName'),
            'status' => 0,
            'options' => $options
        );

        PaymentMethod::create($params);
    }
}
