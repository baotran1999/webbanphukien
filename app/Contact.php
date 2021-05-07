<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
   protected $fillable = ['address','email','phone','title','content','username'];
   protected $table = "contacts";
}
