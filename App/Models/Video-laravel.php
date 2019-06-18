<?php

namespace App\Models;


use  Illuminate\Database\Eloquent\Model;

class Videolaravel extends Model
{
    protected $table = 'video';

    protected $fillable = ['name','cat_id','image','url','type','content','uploader','status'];

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

}
