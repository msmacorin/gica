<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {

    protected $fillable = ['post_types_id', 'observation', 'occurred_at', 'latitude', 'longitude', 'address', 'status'];


    protected $dates = [
        'created_at', 'updated_at', 'occurred_at',
    ];
    
    public function types() {
        return $this->belongsTo('App\PostType', 'post_types_id');
    }

}
