<?php

namespace Modules\Icommerceauthorize\Repositories\Eloquent;

use Modules\Icommerceauthorize\Repositories\IcommerceAuthorizeRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentIcommerceAuthorizeRepository extends EloquentBaseRepository implements IcommerceAuthorizeRepository
{

     /**
     * Update Payment Method
     *
     * @param $model
     * @param $data
     * @return 
     */
    public function update($model, $data){

        //Get data
        $requestimage = $data['mainimage'];
        $requestApilogin = $data['api_login'];
        $requestTransactionkey = $data['transaction_key'];
        $requestClientkey = $data['client_key'];
        $requestmode = $data['mode'];

        // Delete attributes
        unset($data['mainimage']);
        unset($data['api_login']);
        unset($data['transaction_key']);
        unset($data['client_key']);
        unset($data['mode']);

        // Image
        if(($requestimage==NULL) || (!empty($requestimage)) ){
            $requestimage = $this->saveImage($requestimage,"assets/icommerceauthorize/1.jpg");
        }
        $options['mainimage'] = $requestimage;

        // Extra data
        $options['api_login'] = $requestApilogin;
        $options['transaction_key'] = $requestTransactionkey;
        $options['client_key'] = $requestClientkey;
        $options['mode'] = $requestmode;

        // Extra data in Options
        $data['options'] = $options;

        $model->update($data);

        return $model;

    }

    /**
     * Save Image
     *
     * @param  $value
     * @param  $destination
     * @return 
     */
    public function saveImage($value,$destination_path)
    {

        $disk = "publicmedia";

        //Defined return.
        if(ends_with($value,'.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if(config('asgard.iblog.config.watermark.activated')){
                $image->insert(config('asgard.iblog.config.watermark.url'), config('asgard.iblog.config.watermark.position'), config('asgard.iblog.config.watermark.x'), config('asgard.iblog.config.watermark.y'));
            }
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg','80'));


            // Save Thumbs
            \Storage::disk($disk)->put(
                str_replace('.jpg','_mediumThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.mediumthumbsize.width'),config('asgard.iblog.config.mediumthumbsize.height'))->stream('jpg','80')
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg','_smallThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.smallthumbsize.width'),config('asgard.iblog.config.smallthumbsize.height'))->stream('jpg','80')
            );

            // 3. Return the path
            return $destination_path;
        }

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }
    }


     /**
     * Encript url to reedirect
     *
     * @param  $orderID
     * @param  $transactionID
     * @param  $currencyID
     * @return $url
     */
    public function encriptUrl($orderID,$transactionID,$currencyID){

        $url = "{$orderID}-{$transactionID}-{$currencyID}-".time();
        $encrip = base64_encode($url);

        return  $encrip;

    }

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
