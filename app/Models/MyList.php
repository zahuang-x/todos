<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyList extends Model
{
    protected $table="mylists";
    protected $fillable = ['content','status'];
    public $timestamps = false;
}
