<?php

namespace Modules\Icommerceauthorize\Http\Controllers\Api;

// Requests & Response
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Icommerce\Http\Controllers\Api\OrderApiController;
use Modules\Icommerce\Http\Controllers\Api\TransactionApiController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Repositories
use Modules\Icommerceauthorize\Repositories\IcommerceAuthorizeRepository;

use Modules\Icommerce\Repositories\PaymentMethodRepository;
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;


class IcommerceAuthorizeApiController extends BaseApiController
{

    private $icommerceauthorize;
    private $paymentMethod;
    private $order;
    private $orderController;
    private $transaction;
    private $transactionController;
    private $currency;

    public function __construct(

        IcommerceAuthorizeRepository $icommerceauthorize,
        PaymentMethodRepository $paymentMethod,
        OrderRepository $order,
        OrderApiController $orderController,
        TransactionRepository $transaction,
        TransactionApiController $transactionController,
        CurrencyRepository $currency
    ){
        $this->icommerceauthorize = $icommerceauthorize;
        $this->paymentMethod = $paymentMethod;
        $this->order = $order;
        $this->orderController = $orderController;
        $this->transaction = $transaction;
        $this->transactionController = $transactionController;
        $this->currency = $currency;

    }
    
    /**
     * Init data
     * @param Requests request
     * @param Requests orderid
     * @return route
     */
    public function init(Request $request){

        
        try {

            $orderID = $request->orderID;
            
            \Log::info('Module Icommerceauthorize: Init-ID:'.$orderID);

            $paymentName = config('asgard.icommerceauthorize.config.paymentName');

            // Configuration
            $attribute = array('name' => $paymentName);
            $paymentMethod = $this->paymentMethod->findByAttributes($attribute);

            // Order
            $order = $this->order->find($orderID);
            $statusOrder = 1; // Processing

            // get currency active
            $currency = $this->currency->getActive();

            // Create Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->create(new Request([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $statusOrder
                ]))
            );

            // Encri Base 64
            $eOrderID = base64_encode($order->id);
            $eTransactionID = base64_encode($transaction->id);
            $eCurrencyID = base64_encode($currency->id);

            $redirectRoute = route('icommerceauthorize',[$eOrderID,$eTransactionID,$eCurrencyID]);

            // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute,
                "external" => true
            ]];


        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
        }
       
        return response()->json($response, $status ?? 200);
        
    }
    
    /**
     * Response Api Method
     * @param Requests request
     * @return route 
     */
    public function response(Request $request){

        try {

            \Log::info('Module Icommerceauthorize: Response - '.time());

            $response = $request->response;
            $orderID = $request->orderID;
            $transactionID = $request->transactionID;
            $paymentMethodID = $request->paymentMethodID;

            // Order
            $order = $this->order->find($orderID);

            if ($response != null) {
                // Check to see if the API request was successfully received and acted upon
                if ($response->getMessages()->getResultCode() == "Ok") {
                    // Since the API request was successful, look for a transaction response
                    // and parse it to display the results of authorizing the card
                    $tresponse = $response->getTransactionResponse();
                
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        
                        $newstatusOrder = 13; // Status Order Processed
                        $message = "ok";

                    } else {

                        if ($tresponse->getErrors() != null) {
                            $message = " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "- Error Message : " . $tresponse->getErrors()[0]->getErrorText();
                            \Log::error($message);
                        }

                        $newstatusOrder = 7; // Status Order Failed
                    
                    }
                    // Or, print errors if the API request wasn't successful
                } else {
                    
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $message = " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "- Error Message : " . $tresponse->getErrors()[0]->getErrorText();
                    } else {
                        $message = " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "- Error Message : " . $response->getMessages()->getMessage()[0]->getText();
                    }

                    \Log::error($message);
                    $newstatusOrder = 7; // Status Order Failed
                    
                }      
            } else {

                $message = "Error - Response is NULL";
                \Log::error($message);
                $newstatusOrder = 7; // Status Order Failed
            }

            $external_status = $message;
           
            // Update Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->update($transactionID,new Request([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethodID,
                    'amount' => $order->total,
                    'status' => $newstatusOrder,
                    'external_status' => $external_status
                ]))
            );
            

            // Update Order Process 
            $orderUP = $this->validateResponseApi(
                $this->orderController->update($order->id,new Request([
                    'order_id' => $order->id,
                    'status_id' => $newstatusOrder,
                ]))
            );

             // Check order
            //if (!empty($order))
                //$redirectRoute = route('icommerce.order.showorder', [$order->id, $order->key]);
            //else
                $redirectRoute = route('homepage');

             // Response
            $response = [ 'data' => [
                "redirectRoute" => $redirectRoute
            ]];
          
        } catch (\Exception $e) {
             
            $orderID = $request->orderID;
            $transactionID = $request->transactionID;
            $paymentMethodID = $request->paymentMethodID;

            if(!empty($transactionID)){

                $newstatusOrder = 3; // Canceled

                // Update Transaction
                $transactionUP = $this->validateResponseApi(
                    $this->transactionController->update($transactionID,new Request([
                        'status' => $newstatusOrder,
                        'external_status' => "canceled",
                        'external_code' => $e->getCode()
                    ]))
                );

                // Update Order Process 
                $orderUP = $this->validateResponseApi(
                    $this->orderController->update($orderID,new Request([
                        'status_id' => $newstatusOrder,
                    ]))
                );
            }

            //Message Error
             $status = 500;

             $response = [
               'errors' => $e->getMessage(),
               'code' => $e->getCode()
             ];
 
             //Log Error
             \Log::error('Module Icommerceauthorize: Message: '.$e->getMessage());
             \Log::error('Module Icommerceauthorize: Code: '.$e->getCode());
        }

        return response()->json($response, $status ?? 200);

    }

}