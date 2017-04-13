<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller {
    
    public function getView(Request $request) {
        $latlon = $request->only('latitude', 'longitude');
        return view('post', ['data' => $latlon,]);
    }
    
    public function getNearby(Request $request) {
    }
    
    public function postAdd(Request $request) {
    }
    
}
