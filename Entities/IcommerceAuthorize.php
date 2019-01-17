<?php

namespace Modules\Icommerceauthorize\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class IcommerceAuthorize extends Model
{
    use Translatable;

    protected $table = 'icommerceauthorize__icommerceauthorizes';
    public $translatedAttributes = [];
    protected $fillable = [];
}
