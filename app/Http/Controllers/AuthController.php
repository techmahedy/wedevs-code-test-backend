<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Helper\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{   
    use Message;

    public function register(RegisterRequest $request)
    {   
        $validated = $request->validated();

        try {
            if ($request->expectsJson()) {
                $user = User::create($validated);
                return $this->success('Registration Created Successfully!', $user, 'user', 200);
            }else{
                return $this->error('Requested data is not valid!!', null, 422);
            }
        } catch (Throwable $e) {
            Log::info($e);
            return $this->error('Something went wrong!', null, 422);
        }
    }
 
    public function login(LoginRequest $request)
    {
        $input = $request->validated();
        $jwt_token = auth()->guard('api')->attempt($input);
        
        if (!$jwt_token = auth()->guard('api')->attempt($input)) {
            return $this->error('Invalid Email or Password!', null, 401);
        }
 
        $user = \DB::table((new User)->getTable())
                    ->whereEmail($request->email)
                    ->first();

        return $this->successWithToken($jwt_token, $user);
    }
 
    public function logout()
    {  
        try {
            auth()->guard('api')->logout();
            return $this->success('User logged out successfully!', null, 'user', 401);
        } catch (Throwable $exception) {
            Log::info($exception);
            return $this->error('Sorry, something went wrong!', null, 401);
        }
    }
 
    public function getAuthenticatedUser(Request $request)
    {
        if( ! auth()->guard('api')->check() ){
            return $this->error('You are not authorized!!', null, 401);
        }

        return $this->success(
            '',
            auth()->guard('api')->user(),
            'user',
            200
        );
    }
}
