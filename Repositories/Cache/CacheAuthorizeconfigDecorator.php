<?php

namespace Modules\Icommerceauthorize\Repositories\Cache;

use Modules\Icommerceauthorize\Repositories\AuthorizeconfigRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAuthorizeconfigDecorator extends BaseCacheDecorator implements AuthorizeconfigRepository
{
    public function __construct(AuthorizeconfigRepository $authorizeconfig)
    {
        parent::__construct();
        $this->entityName = 'icommerceauthorize.authorizeconfigs';
        $this->repository = $authorizeconfig;
    }
}
