<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Carbon\Carbon;

class PostController extends Controller {

    public function getView(Request $request) {
        $latlon = $request->only('latitude', 'longitude');
        return view('post', ['data' => $latlon,]);
    }

    public function getNearby(Request $request) {
        
    }

    public function postAdd(Request $request) {
        try {
            $data = $request->except('_token');
            Post::create([
                'post_types_id' => $data['post_type'], 
                'occurred_at' => Carbon::createFromFormat('d/m/Y', $data['occurred_at']),
                'address' => $data['address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'observation' => isset($data['observation']) ? $data['observation'] : null,
                'status' => Post::BLOCKED,
            ]);
            
            return redirect('/');
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
