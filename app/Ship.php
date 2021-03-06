<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','price'];
    protected $table = "ships";
}
