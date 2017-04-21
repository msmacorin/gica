<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getByDistance($lat, $lng, $distance) {
        $query = 'SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') )';
        $query .= ' * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') )';
        $query .= ' + sin( radians(' . $lat . ') ) * sin( radians(latitude) ) ) ) AS distance';
        $query .= ' FROM posts HAVING distance < ' . $distance . ' ORDER BY distance';
        $results = DB::select(DB::raw($query));
        return $results;
    }

}
