<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SignupActivate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravolt\Avatar\Facade as Avatar;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function regist(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $success = false;

        if (!$validation->fails()) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'activation_token' => Str::random(30)
            ]);

            $user->notify(new SignupActivate($user));
            $success = true;
        }


        return $this->success($success);
    }

    public function acceptRegistration(Request $request)
    {
        if ($request->token) {
            $user = User::where('activation_token', $request->token)->first();

            if($user) {
                $user->activation_token = null;
                $user->active = 1;
                $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
                $avatarPath = "avatars/{$user->id}avatar.png";
                Storage::disk('public')->put($avatarPath, (string) $avatar);

                $user->avatar = $user->id.'avatar.png';
                $user->save();
            }
        }

        return redirect('/');
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6'
        ]);

        if (!$validation->fails() && !Auth::check()) {
            $request->merge(['active' => 1, 'deleted_at'=> null]);
            Auth::attempt($request->only(['email', 'password', 'active', 'deleted_at']));
            $user = $request->user();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token->save();

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }


            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        }


        return $this->success(false);
    }

    public function createResetPassword(Request $request) {

       $validation = Validator::make($request->all(), [
        'email' => 'required|email|exists:users'
       ]);
       $success = false;

       if (!$validation->fails()) {
         $passwordReset = PasswordReset::createOrUpdate(
           ['email' => $request->email],
           [
           'email' => $request->email,
           'token' =>  Str::random(30)
         ]);

         $passwordReset->notify(new PasswordResetRequest($passwordReset));
         $success = true;
       }

       return $this->success($success);
    }

    public function resetPassword(Request $request) {
      $success = false;
      $resetDataValidation = Validator::male($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
        'token' => 'required|min:30'
      ]);
      $passwordReset = PasswordReset::where([
        'email'=> $request->email,
        'token' => $request->token
        ])->first();
      $user = User::where([
        'email'=> $request->email
        ])->first();

      if ($passwordReset && !$resetDataValidation->fails() && $user) {
          $passwordReset->delete();
          $user->password = bcrypt($request->password);
          $user->notify(new PasswordResetSuccess($user));
          $success = true;
      }

      return $this->success($success);
    }

    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->success(true);
    }
}
