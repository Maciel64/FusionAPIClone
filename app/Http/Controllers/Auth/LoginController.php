<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\AuthenticateLoginTokenRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactor;
use Illuminate\Support\Carbon;

class LoginController extends Controller
{
  /**
   * Authenticate a user.
   * 
   * @group Authentication
   * 
   * @bodyParam email string required The email of the user. Example:
   * @bodyParam password string required The password of the user. Example:
   * 
   * @response {
   *     "status": true,
   *     "message": "Login successful",
   *     "data": {
   *         "uuid": "eb0e1d49-3a9f-4761-b9da-cda58afbca3b",
   *         "name": "Admin",
   *         "email": "admin@fusion.com",
   *         "photo": null,
   *         "address": null,
   *         "contact": null
   *     }
   * }
   * 
   * @param AuthenticateRequest $request
   * @return \Illuminate\Http\Response
   */
  // public function authenticate(AuthenticateRequest $request)
  // {
  //   $credentials = $request->validate([
  //     'email' => 'required|email',
  //     'password' => 'required|string',
  //   ]);

  //   $credentials = $request->validated();
  //   $user = User::where('email', $credentials['email'])->first();

  //   if (!$user) return response()->json(['Status' => false, 'Message' => 'Usuário não encontrado. Verifique se o e-mail está correto.'], 401);
  //   // if(!$user->email_verified_at) return $this->response('auth',false,true, 401);
  //   if (!auth()->attempt($credentials)) return response()->json(['Status' => false, 'Message' => 'Email ou senha inválidos.'], 401);
  //   if ($user->account_active == 0) return response()->json(['Status' => false, 'Message' => 'Usuário desativado.'], 401);

  //   $user->last_access = date('Y-m-d H:i:s');
  //   $token = $user->createToken($user->uuid);
  //   $response = [...$user->toArray(), 'token' => $token->plainTextToken];
  //   Auth::login($user->fresh());
  //   return $this->response('auth.login', $response);
  // }

  /**
   * Log the user out of the application.
   *
   * @group Authentication
   * 
   * @response {
   *    "message": "Logged Out"
   * }
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function logout()
  {
    Auth::logout();
    return $this->response('auth.logout', true);
  }

  public function authenticateTwoFactor(AuthenticateRequest $request)
  {

    $credentials = $request->validated();
    $user = User::where('email', $credentials['email'])->first();
    if ($user->account_active == 0)
      return response()->json(['Status' => false, 
                              'Message' => 'Sua conta foi suspensa temporariamente por motivos de segurança. Entre em contato conosco para saber mais detalhes.'
                              ], 401);
    if (!$user)
      return response()->json(['Status' => false, 'Message' => 'Usuário não encontrado. Verifique se o e-mail está correto.'], 404);
    if (!auth()->attempt($credentials))
      return response()->json(['Status' => false, 'Message' => 'Verifique se o e-mail e senha estão corretos.'], 401);


    $user->login_token_date = date('Y-m-d H:i:s');
    $user->login_token = rand(100000, 999999);
    $user->save();
    //rotina do e-mail
    $name = $user->name;
    $token = $user->login_token;

    TwoFactor::sendMail(
      $user->email,
      'Fusion Clinic - Código de Verificação para Login',
      'emails.twoFactor',
      compact('name', 'token'),
    );

    return response()->json([
      'status' => true,
      'message' => "Email enviado com o token."
    ]);


    // $user->last_access = date('Y-m-d H:i:s');
    // $token = $user->createToken($user->uuid);
    // $response = [...$user->toArray(), 'token' => $token->plainTextToken];
    // Auth::login($user->fresh());
    return $this->response('auth.login', $response);
  }

  public function authenticateLoginToken(AuthenticateLoginTokenRequest $request)
  {
    $credentials = $request->validated();

    $user = User::where('login_token', $credentials['login_token'])
      ->where('email', $credentials['email'])->first();

    if (!$user)
      return $this->response()->json(['Status' => false, 'Message' => 'Usuário não encontrado.'], 404);
    if ($user->login_token != $credentials['login_token'])
      return response()->json(['status' => false, 'message' => 'Token inválido.'], 401);

    $loginTokenDate = Carbon::parse($user->login_token_date);
    $currentDate = Carbon::now();
    if ($loginTokenDate->diffInMinutes($currentDate) > 15) {
      return response()->json(['status' => false, 'message' => 'Token de login expirado.'],498);
    }

    $user->last_access = date('Y-m-d H:i:s');
    $user->login_token_date = null;
    $user->login_token = null;
    $user->save();
    $token = $user->createToken($user->uuid);
    $response = [...$user->toArray(), 'token' => $token->plainTextToken];
    Auth::login($user->fresh());
    return $this->response('auth.login', $response);
  }
}
