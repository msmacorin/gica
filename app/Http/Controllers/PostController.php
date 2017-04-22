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

    public function getPosts(Request $request) {
        $params = $request->only('status');
        $posts = Post::with('types')
                ->where('status', $params['status'])
                ->orderBy('created_at', 'DESC')
                ->get();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->id,
                'type' => $post->types->name,
                'occurred_at' => $post->occurred_at->format('d/m/Y'),
                'address' => $post->address,
                'status' => $post->status,
            ];
        }

        return view('admin_table', ['posts' => $data]);
    }

    public function postPosts(Request $request) {
        try {
            $params = $request->all();
            Post::where('id', $params['id'])
                    ->update(['status' => $params['status'],]);
            return response('', \Illuminate\Http\Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response('', \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getNearby(Request $request) {
        $params = $request->only('latitude', 'longitude');
        $ids = Post::getByDistance($params['latitude'], $params['longitude'], 5);
        if (empty($ids)) {
            return response('', \Illuminate\Http\Response::HTTP_NO_CONTENT);
        }
        $ids = array_column($ids, 'id');
        $posts = Post::with('types')
                ->whereIn('id', $ids)
                ->where('status', Post::ACTIVE)
                ->get();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'latitude' => $post->latitude,
                'longitude' => $post->longitude,
                'icon' => $post->types->icon,
                'content' => $this->makeContent($post->occurred_at->format('d/m/Y'), $post->types->name, $post->address, $post->observation)
            ];
        }
        return response()->json($data, \Illuminate\Http\Response::HTTP_OK);
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
