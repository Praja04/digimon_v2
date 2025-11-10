<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::loginUsingId($user->id);

            switch ($user->role) {
                case 'Head Of Dapartement':
                    return response()->json(['redirect' => route('users.index')]);
                case 'Supervisor':
                    return response()->json(['redirect' => route('users.index')]);
                case 'Foreman':
                    return response()->json(['redirect' => route('colors.index')]);
                case 'Analis Kimia':
                    return response()->json(['redirect' => route('gga.menu')]);
                case 'Analis Mikro':
                    return response()->json(['redirect' => route('analisa.blending-awal.menu')]);
                case 'Analis RM':
                    return response()->json(['redirect' => route('users.index')]);
                case 'Analis Field':
                    return response()->json(['redirect' => route('analisa.blending-awal.menu')]);
                case 'Operator':
                    return response()->json(['redirect' => route('productionbatch.index')]);
                default:
                    return response()->json(['redirect' => route('users.index')]);
            }
        } else {
            return response()->json(['errors' => [
                'password' => ['Kata sandi salah.']
            ]], 422);
        }
    }
}
