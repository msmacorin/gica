<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class ContactController extends Controller {

    public function send(Request $request) {
        try {
            $data = $request->except('_token');
            Mail::send('email_contact', $data, function($message) use ($data) {
               $message->from($data['email'], $data['name']);
               $message->subject(trans('go.contact') . ' - ' . config('constants.SITE_NAME'));
               $message->to(config('constants.ADMIN_EMAIL'));
            });
            return Response::json(array('message' => trans('go.email_success')), \Illuminate\Http\Response::HTTP_OK);
        } catch (\ErrorException $e) {
        } catch (Exception $e) {
        }
        if ($e) {
            return Response::json(array('message' => trans('go.email_error')), \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
