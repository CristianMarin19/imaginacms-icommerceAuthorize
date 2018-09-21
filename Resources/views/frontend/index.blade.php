@extends('layouts.master')


@section('title')
     Authorize.net | @parent
@stop


@section('content')
<div class="icommerce_authorize icommerce_authorize_index">
  <div class="container">


    <div class="row my-5">

       <h2 class="text-center mx-auto">Authorize.net</h2>

    </div>

    <div class="row my-5 justify-content-center">

       @include('icommerceauthorize::frontend.partials.modal')

    </div>


  </div>
</div>
@stop