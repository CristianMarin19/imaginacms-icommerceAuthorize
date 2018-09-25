<?php

namespace Modules\Icommerceauthorize\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Icommerceauthorize\Entities\Authorizeconfig;
use Modules\Icommerceauthorize\Http\Requests\CreateAuthorizeconfigRequest;
use Modules\Icommerceauthorize\Http\Requests\UpdateAuthorizeconfigRequest;
use Modules\Icommerceauthorize\Repositories\AuthorizeconfigRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Setting\Repositories\SettingRepository;

class AuthorizeconfigController extends AdminBaseController
{
    /**
     * @var AuthorizeconfigRepository
     */
    private $authorizeconfig;

    public function __construct(AuthorizeconfigRepository $authorizeconfig,SettingRepository $setting)
    {
        parent::__construct();

        $this->authorizeconfig = $authorizeconfig;
        $this->setting=$setting;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$authorizeconfigs = $this->authorizeconfig->all();

        return view('icommerceauthorize::admin.authorizeconfigs.index', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('icommerceauthorize::admin.authorizeconfigs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAuthorizeconfigRequest $request
     * @return Response
     */
    public function store(CreateAuthorizeconfigRequest $request)
    {
        $this->authorizeconfig->create($request->all());

        return redirect()->route('admin.icommerceauthorize.authorizeconfig.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('icommerceauthorize::authorizeconfigs.title.authorizeconfigs')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Authorizeconfig $authorizeconfig
     * @return Response
     */
    public function edit(Authorizeconfig $authorizeconfig)
    {
        return view('icommerceauthorize::admin.authorizeconfigs.edit', compact('authorizeconfig'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Authorizeconfig $authorizeconfig
     * @param  UpdateAuthorizeconfigRequest $request
     * @return Response
     */
    public function update(Authorizeconfig $authorizeconfig, UpdateAuthorizeconfigRequest $request)
    {

       
        if($request->status=='on')
            $request['status'] = "1";
        else
            $request['status'] = "0";

        $data = $request->all();
        $token =$data['_token'];
        $requestimage =$data['mainimage'];

        unset($data['_token']);
        unset($data['mainimage']);
        unset($data['_method']);
        unset($data['locale']);

        $newData['_token'] = $token;//Add token first

        if(($requestimage==NULL) || (!empty($requestimage)) )
            $requestimage = $this->saveImage($requestimage,"assets/icommerceauthorize/1.jpg");

        foreach ($data as $key => $val)
            $newData['icommerceauthorize::'.$key ] = $val;

        $newData['icommerceauthorize::image'] = $requestimage;

        $s = $this->setting->createOrUpdate($newData);

        return redirect()->route('admin.icommerce.payment.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('icommerceauthorize::authorizeconfigs.title.authorizeconfigs')]));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Authorizeconfig $authorizeconfig
     * @return Response
     */
    public function destroy(Authorizeconfig $authorizeconfig)
    {
        $this->authorizeconfig->destroy($authorizeconfig);

        return redirect()->route('admin.icommerceauthorize.authorizeconfig.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('icommerceauthorize::authorizeconfigs.title.authorizeconfigs')]));
    }


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


}
