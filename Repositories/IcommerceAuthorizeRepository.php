<?php

namespace Modules\Icommerceauthorize\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface IcommerceAuthorizeRepository extends BaseRepository
{
    public function encriptUrl($orderID,$transactionID,$currencyID);

    public function decriptUrl($eUrl);

}
