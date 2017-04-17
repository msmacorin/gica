<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {

    const ACTIVE = 1;
    const BLOCKED = 2;
    const REMOVED = 3;
    const DENIED = 4;
    
    protected $fillable = ['post_types_id', 'observation', 'occurred_at', 'latitude', 'longitude', 'address', 'status'];


    protected $dates = [
        'created_at', 'updated_at', 'occurred_at',
    ];
    
    public function types() {
        return $this->belongsTo('App\PostType', 'post_types_id');
    }

}
