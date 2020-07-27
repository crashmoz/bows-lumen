<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use App\Account;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    #####################################   ADDITIONAL FOR JWT    ##################################
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['loginJwt']]);
    // }

    public function loginJwt(Request $request) {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['username', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            # code...
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // $user = auth()->user();
        // $account = Account::where('username', $user->username)->firstOrFail();
        // $account->api_token = $token;
        // $account->save();
        return $this->respondWithToken($token);
        // return response($account);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutJwt()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    ################################################################################################

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = Account::where('username', $request->username)->firstOrFail();
        $status = 'error';
        $message = '';
        $data = null;
        $code = 401;
        if ($user) 
        {
            // Jika hasil hash dari password yang diinput user sama dengan password di database user maka
            if (Hash::check($request->password, $user->password)) 
            {
                # Generate token
                $user->generateToken();
                $status = 'success';
                $message = 'Login sukses';
                # Tampilkan data user menggunakan method toArray
                $data = $user->toArray();
                $code = 200;
            }
            else
            {
                $message = 'Login gagal, password salah';
            }
        }
        else
        {
            $message = 'Login gagal, username salah';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'fullname' => 'required|string|max:200',
            'email' => 'required|string|email|max:200|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        }
        else
        {
            $user = \App\User::create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            if ($user) 
            {
                # Auth::login($user);
                $user->generateToken();
                $status = 'success';
                $message = 'register successfully';
                $data = $user->toArray();
                $code = 200;
            }
            else
            {
                $message = 'register failed';
            }
        }

        return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ], $code);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'logout berhasil',
            'data' => null
        ], 200);
    }
}