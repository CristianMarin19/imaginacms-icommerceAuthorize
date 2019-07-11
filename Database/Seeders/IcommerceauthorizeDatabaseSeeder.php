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

        $options['init'] = "Modules\Icommerceauthorize\Http\Controllers\Api\IcommerceAuthorizeApiController";
        $options['mainimage'] = null;
        $options['apilogin'] = "";
        $options['transactionkey'] = "";
        $options['clientkey'] = "";
        $options['mode'] = "sandbox";

        $titleTrans = 'icommerceauthorize::icommerceauthorizes.single';
        $descriptionTrans = 'icommerceauthorize::icommerceauthorizes.description';

        foreach (['en', 'es'] as $locale) {
            if($locale=='en'){
                $params = array(
                    'title' => trans($titleTrans),
                    'description' => trans($descriptionTrans),
                    'name' => config('asgard.icommerceauthorize.config.paymentName'),
                    'status' => 0,
                    'options' => $options
                );
                $paymentMethod = PaymentMethod::create($params);
                
            }else{
                $title = trans($titleTrans,[],$locale);
                $description = trans($descriptionTrans,[],$locale);
                $paymentMethod->translateOrNew($locale)->title = $title;
                $paymentMethod->translateOrNew($locale)->description = $description;
                $paymentMethod->save();
            }
        }// Foreach

    }
}
