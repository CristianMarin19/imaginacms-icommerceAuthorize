<?php

namespace Modules\Icommerceauthorize\Repositories\Cache;

use Modules\Icommerceauthorize\Repositories\IcommerceAuthorizeRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheIcommerceAuthorizeDecorator extends BaseCacheDecorator implements IcommerceAuthorizeRepository
{
    public function __construct(IcommerceAuthorizeRepository $icommerceauthorize)
    {
        parent::__construct();
        $this->entityName = 'icommerceauthorize.icommerceauthorizes';
        $this->repository = $icommerceauthorize;
    }
}
