@php
	
	$configuration = icommerceauthorize_get_configuration();
	$options = array('required' =>'required');
	
	$cStatus = $configuration->status;
		
	$formID = uniqid("form_id");
	
@endphp


{!! Form::open(['route' => ['admin.icommerceauthorize.authorizeconfig.update'], 'method' => 'put','name' => $formID]) !!}


<div class="col-xs-12 col-sm-9">

	{!! Form::normalInput('description','*'.trans('icommerceauthorize::authorizeconfigs.table.description'), $errors,$configuration,$options) !!}
	
	{!! Form::normalInput('api_login', '* Api Login', $errors,$configuration,$options) !!}

	{!! Form::normalInput('transaction_key', '* Transaction Key', $errors,$configuration,$options) !!}

	{!! Form::normalInput('client_key', '* Public Key Client', $errors,$configuration,$options) !!}

	<div class="form-group">
		<label for="url_action">*Mode</label>
		<select class="form-control" id="url_action" name="url_action" required>
			<option value="0" @if(!empty($configuration) && $configuration->url_action==0) selected @endif>SANDBOX</option>
			<option value="1" @if(!empty($configuration) && $configuration->url_action==1) selected @endif>PRODUCTION</option>
		</select>
	</div>

	<div class="form-group">
		<div>
			<label class="checkbox-inline">
				<input name="status" type="checkbox" @if($cStatus==1) checked @endif>{{trans('icommerceauthorize::authorizeconfigs.table.activate')}}
			</label>
		</div>   
	</div>

	
</div>

<div class="col-sm-3">
	
	@include('icommerceauthorize::admin.authorizeconfigs.partials.featured-img',['crop' => 0,'name' => 'mainimage','action' => 'create'])
	
</div>
   	
	
 <div class="clearfix"></div>   

    <div class="box-footer">
    <button type="submit" class="btn btn-primary btn-flat">{{ trans('icommerceauthorize::authorizeconfigs.button.save configuration') }}</button>
    </div>



{!! Form::close() !!}