<?php

namespace Modules\IcommerceAuthorize\Entities;

class Authorizeconfig
{
  
    private $description;
    private $api_login;
    private $transaction_key;
    private $client_key;
    private $url_action;
    private $image;
    private $status;

    public function __construct()
    {

        $this->description = setting('icommerceAuthorize::description');
        $this->api_login = setting('icommerceAuthorize::api_login');
        $this->transaction_key = setting('icommerceAuthorize::transaction_key');
        $this->client_key = setting('icommerceAuthorize::client_key');
        $this->url_action = setting('icommerceAuthorize::url_action');
    	$this->image = setting('icommerceAuthorize::image');
        $this->status = setting('icommerceAuthorize::status');

    }

    public function getData()
    {
        return (object) [
            'description' => $this->description,
            'api_login' => $this->api_login,
            'transaction_key' => $this->transaction_key,
            'client_key' => $this->client_key,
            'url_action' => $this->url_action,
            'image' => url($this->image),
            'status' => $this->status
        ];
    }


}
