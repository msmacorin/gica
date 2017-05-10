<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller {

    public function getUsers(Request $request) {
        $param = $request->only('status');
        $users = User::with('socialAccount')
                ->where('status', $param['status'])
                ->orderBy('created_at', 'DESC')
                ->get();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'link' => count($user->socialAccount) > 0 ? $user->socialAccount[0]->provider_profile : null,
            ];
        }

        return view('admin-table-users', ['data' => $data]);
    }

    public function postUsers(Request $request) {
        try {
            $params = $request->all();
            User::where('id', $params['id'])
                    ->update(['status' => $params['status'],]);
            return response('', \Illuminate\Http\Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response('', \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
