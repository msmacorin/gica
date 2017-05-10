<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialAccounts extends Model {

    protected $fillable = ['user_id', 'provider_user_id', 'provider_profile', 'provider'];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
