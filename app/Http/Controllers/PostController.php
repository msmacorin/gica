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
        $posts = Post::with('types')->get();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'latitude' => $post->latitude,
                'longitude' => $post->longitude,
                'icon' => $post->types->icon,
                'content' => $this->makeContent($post->occurred_at->format('d/m/Y'), $post->types->name, $post->address, $post->observation)
            ];
        }
        return json_encode($data);
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

    private function makeContent($occurred_at, $type, $address, $observation) {
        $content = '<b>' . $type . '</b><br><br>';
        $content .= trans('go.occurred_at') . ': ' . $occurred_at . '<br>';
        $content .= trans('go.address') . ': ' . $address;
        if ($observation) {
            $content .= '<br>' . trans('go.message') . ': ' . $observation;
        }

        return $content;
    }

}
