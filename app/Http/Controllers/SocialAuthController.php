<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\SocialAccounts;
use App\User;

class SocialAuthController extends Controller {

    public function facebookRedirect() {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback() {
        $providerUser = Socialite::driver('facebook')->user();
        $account = SocialAccounts::whereProvider('facebook')
                ->whereProviderUserId($providerUser->getId())
                ->first();

        $page = 'auth.user-welcome';
        $message = trans('go.welcome_message_blocked');
        if ($account) {
            $user = $account->user;
            if ($user->status == User::ACTIVE) {
                $page = 'home';
            } else if ($user->status == User::DENIED) {
                $message = trans('go.welcome_message_denied');
            }
        } else {
            $account = new SocialAccounts([
                'provider_user_id' => $providerUser->getId(),
                'provider_profile' => $providerUser['link'], // nao é um metodo
                'provider' => 'facebook'
            ]);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                            'email' => $providerUser->getEmail(),
                            'name' => $providerUser->getName(),
                            'password' => hash('ripemd160', $providerUser->getEmail()),
                            'administrator' => false,
                            'status' => User::BLOCKED,
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            $data = [
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'link' => $providerUser['link'],
            ];
            Mail::send('email_user', $data, function($message) use ($user) {
                $message->from($user['email'], $user['name']);
                $message->subject(trans('go.new_user') . ' - ' . config('constants.SITE_NAME'));
                $message->to(config('constants.ADMIN_EMAIL'));
            });
        }

        if ($user->status == User::ACTIVE) {
            auth()->login($user, true);
        }
        return view($page, ['message' => $message]);
    }
}
