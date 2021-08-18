<?php

namespace Modules\Icommerceauthorize\Repositories\Eloquent;

use Modules\Icommerceauthorize\Repositories\IcommerceAuthorizeRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentIcommerceAuthorizeRepository extends EloquentBaseRepository implements IcommerceAuthorizeRepository
{

     
     /**
     * Decript url to get data
     *
     * @param  $eUrl
     * @return array
     */
    public function decriptUrl($eUrl){

        $decrip = base64_decode($eUrl);
        $infor = explode('-',$decrip);
        
        return  $infor;

    }

}
