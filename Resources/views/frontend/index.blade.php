@extends('icommerceauthorize::frontend.layouts.master')

@section('title')
  Authorize.net | @parent
@stop


@section('content')
<div class="icommerce_authorize_index">
    <div class="container">
  
      <div class="row my-5 justify-content-center">
  
         @include('icommerceauthorize::frontend.partials.modal')
  
      </div>
  
  
    </div>
</div>
@stop


{{--
<style>

  .container {
    max-width: 1140px;
    margin:auto;
  }

  .row{
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
  }

  .text-center {
    text-align: center !important;
  }

  .ml-auto, .mx-auto {
    margin-left: auto !important;
  }

  .mr-auto, .mx-auto {
    margin-right: auto !important;
  }

  .justify-content-center {
    -webkit-box-pack: center !important;
    -ms-flex-pack: center !important;
    justify-content: center !important;
  }

  h2, .h2 {
    font-size: 2rem;
  }

  h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
    margin-bottom: 0.5rem;
    font-family: inherit;
    font-weight: 500;
    line-height: 1.2;
    color: inherit;
  } 

  p {
    margin-top: 0;
    margin-bottom: 1rem;
  }

  .d-none {
    display: none !important;
  }

  .fa-pulse {
    -webkit-animation: fa-spin 1s infinite steps(8);
    animation: fa-spin 1s infinite steps(8);
  }

  .fa-3x {
    font-size: 3em;
  }

  .fa {
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
        font-size: 14px;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  .btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
  }

  .btn-success:hover {
    color: #fff;
    background-color: #218838;
    border-color: #1e7e34;
  }

  .btn:hover, .btn:focus {
    text-decoration: none;
  }

  .btn-lg, .btn-group-lg > .btn {
    padding: 0.5rem 1rem;
    font-size: 1.25rem;
    line-height: 1.5;
    border-radius: 0.3rem;
  }

  .btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    border-top-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    border-left-color: transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
  }

  .btn:not(:disabled):not(.disabled) {
    cursor: pointer;
  }

</style>
--}}