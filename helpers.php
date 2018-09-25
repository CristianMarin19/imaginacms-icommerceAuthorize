<?php


use Modules\Icommerceauthorize\Entities\Authorizeconfig;


if (! function_exists('icommerceauthorize_get_configuration')) {

    function icommerceauthorize_get_configuration()
    {

    	$configuration = new Authorizeconfig();
    	return $configuration->getData();

    }

}

if (! function_exists('icommerceauthorize_get_entity')) {

	function icommerceauthorize_get_entity()
    {
    	$entity = new Authorizeconfig;
    	return $entity;	
    }

}
