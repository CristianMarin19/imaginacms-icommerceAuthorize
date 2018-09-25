<?php

namespace Modules\Icommerceauthorize\Http\Controllers;

use Mockery\CountValidator\Exception;


use Modules\Core\Http\Controllers\BasePublicController;
use Route;
use Session;

use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;
use Modules\Icommerce\Repositories\ProductRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\Order_ProductRepository;
use Modules\Setting\Contracts\Setting;
use Illuminate\Http\Request as Requests;
use Illuminate\Support\Facades\Log;

use Modules\Icommerceauthorize\Entities\Authorizeconfig;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PublicController extends BasePublicController
{
  
    private $order;
    private $setting;
    private $user;
    protected $auth;
    
    public function __construct(Setting $setting, Authentication $auth, UserRepository $user,  OrderRepository $order)
    {

        $this->setting = $setting;
        $this->auth = $auth;
        $this->user = $user;
        $this->order = $order;

    }

    /**
     * Show Index
     * @param Requests request
     * @return  
     */
    public function index(Requests $request)
    {
        
        if($request->session()->exists('orderID')) {

            $orderID = session('orderID');
            $order = $this->order->find($orderID);

            $config = new Authorizeconfig();
            $config = $config->getData();

            if($config->url_action==0)
                $acceptJS = "https://jstest.authorize.net/v3/AcceptUI.js";
            else
                $acceptJS = "https://js.authorize.net/v3/AcceptUI.js";
                
            $apiLogin = $config->api_login;
            $clientKey = $config->client_key;

            $tpl = 'icommerceauthorize::frontend.index';

            return view($tpl, compact('acceptJS','apiLogin','clientKey','order'));

        
        }else{
            return redirect()->route('homepage');
        }
        
    }

     /**
     * Send Information
     * @param Requests request
     * @return redirect
     */
    public function send($oval,$odes,Requests $request2){
        
      
       if($request2->session()->exists('orderID')) {

        $orderID = session('orderID');
        $order = $this->order->find($orderID);

        $config = new Authorizeconfig();
        $config = $config->getData();

        try{

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

            $merchantAuthentication->setName($config->api_login);
            $merchantAuthentication->setTransactionKey($config->transaction_key);

            // Set the transaction's refId
            $refId = $orderID."-".time();
            
            $restDescription = "Order:{$orderID} - {$order->email}";

            // Create the payment object for a payment nonce
            $opaqueData = new AnetAPI\OpaqueDataType();
            $opaqueData->setDataDescriptor($odes);
            $opaqueData->setDataValue($oval);


            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setOpaqueData($opaqueData);

            // Create order information
            $orderInfor = new AnetAPI\OrderType();
            $orderInfor->setInvoiceNumber($refId);
            $orderInfor->setDescription($restDescription);

             // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($order->payment_firstname);
            $customerAddress->setLastName($order->payment_lastname);
            $customerAddress->setCompany($order->payment_company);
            $customerAddress->setAddress($order->payment_address_1);
            $customerAddress->setCity($order->payment_city);
            $customerAddress->setState($order->payment_zone);
            $customerAddress->setZip($order->payment_postcode);
            $customerAddress->setCountry($order->payment_country);

            // Set the customer's identifying information
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId($order->user_id);
            $customerData->setEmail($order->email);

            // Add values for transaction settings
            $duplicateWindowSetting = new AnetAPI\SettingType();
            $duplicateWindowSetting->setSettingName("duplicateWindow");
            $duplicateWindowSetting->setSettingValue("60");

           
            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction"); 
            $transactionRequestType->setAmount($order->total);
            $transactionRequestType->setOrder($orderInfor);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
           
             // Assemble the complete transaction request
            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setTransactionRequest($transactionRequestType);

             // Create the controller and get the response
            $controller = new AnetController\CreateTransactionController($request);
            
            if($config->url_action==0)
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            else
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            
            return $this->response($response,$request2,$order);


        }catch(Exception $e){
            Log::info('Authorize Error:  Exception'.time());
             //echo $e->getMessage();
        }

        }else{
            return redirect()->route('homepage');
        }
        
    }


    /**
     * Check Response
     * @param $response,$request,$order
     * @return redirect
     */
    public function response($response,$request,$order){
        
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    
                    $msjTheme = "icommerce::email.success_order";
                    $msjSubject = trans('icommerce::common.emailSubject.complete')."- Order:".$order->id;
                    $msjIntro = trans('icommerce::common.emailIntro.complete');
                    $state = 1;

                } else {

                    if ($tresponse->getErrors() != null) {
                        $message = " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "- Error Message : " . $tresponse->getErrors()[0]->getErrorText();
                        Log::error($message);
                    }

                    $msjTheme = "icommerce::email.error_order";
                    $msjSubject = trans('icommerce::common.emailSubject.failed')."- Order:".$order->id;
                    $msjIntro = trans('icommerce::common.emailIntro.failed');
                    $state = 6;
                   
                }
                // Or, print errors if the API request wasn't successful
            } else {
                
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message = " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "- Error Message : " . $tresponse->getErrors()[0]->getErrorText();
                } else {
                    $message = " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "- Error Message : " . $response->getMessages()->getMessage()[0]->getText();
                }

                Log::error($message);

                $msjTheme = "icommerce::email.error_order";
                $msjSubject = trans('icommerce::common.emailSubject.failed')."- Order:".$order->id;
                $msjIntro = trans('icommerce::common.emailIntro.failed');
                $state = 6;
                
            }      
        } else {

            $message = "Error - Response is NULL";
            Log::error($message);

            $msjTheme = "icommerce::email.error_order";
            $msjSubject = trans('icommerce::common.emailSubject.failed')."- Order:".$order->id;
            $msjIntro = trans('icommerce::common.emailIntro.failed');
            $state = 6;
        }

        if(isset($state)){
            $success_process = icommerce_executePostOrder($order->id,$state,$request);
        }

        $order = $this->order->find($order->id);

        $products=[];

        foreach ($order->products as $product) {
            array_push($products,[
                "title" => $product->title,
                "sku" => $product->sku,
                "quantity" => $product->pivot->quantity,
                "price" => $product->pivot->price,
                "total" => $product->pivot->total,
            ]);
        }

        $userFirstname = "{$order->first_name} {$order->last_name}";

        $email_from = $this->setting->get('icommerce::from-email');
        $email_to = explode(',',$this->setting->get('icommerce::form-emails'));
        $sender  = $this->setting->get('core::site-name');

        $content=[
            'order'=>$order,
            'products' => $products,
            'user' => $userFirstname
        ];

        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme,'email_to' => $order->email,'subject' => $msjSubject, 'sender'=>$sender,'data' => array('title' => $msjSubject,'intro'=> $msjIntro,'content'=>$content)]);
                        
        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme,'email_to' => $email_to,'subject' => $msjSubject, 'sender'=>$sender,'data' => array('title' => $msjSubject,'intro'=> $msjIntro,'content'=>$content)]);

        return $this->reedirectCustomer($order);
    }


     /**
     * Reedirect Customer After all Proccess
     * @param $order
     * @return reedirect
     */
    public function reedirectCustomer($order){

        $user = $this->auth->user();

        if (isset($user) && !empty($user))
            if (!empty($order))
                return redirect()->route('icommerce.orders.show', [$order->id]);
            else
                return redirect()->route('homepage')
                  ->withSuccess(trans('icommerce::common.order_success'));
        else
            if (!empty($order))
                return redirect()->route('icommerce.order.showorder', [$order->id, $order->key]);
            else
                return redirect()->route('homepage')
                  ->withSuccess(trans('icommerce::common.order_success'));
    }

}