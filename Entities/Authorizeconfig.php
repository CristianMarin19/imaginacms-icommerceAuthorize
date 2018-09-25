<?php

namespace Modules\Icommerceauthorize\Entities;

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

        $this->description = setting('icommerceauthorize::description');
        $this->api_login = setting('icommerceauthorize::api_login');
        $this->transaction_key = setting('icommerceauthorize::transaction_key');
        $this->client_key = setting('icommerceauthorize::client_key');
        $this->url_action = setting('icommerceauthorize::url_action');
    	$this->image = setting('icommerceauthorize::image');
        $this->status = setting('icommerceauthorize::status');

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
