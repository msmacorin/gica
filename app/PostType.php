<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostType extends Model {
    
    protected $fillable = ['name', 'icon_class'];
}
