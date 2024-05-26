<?php

namespace App\Http\Middleware;

use App\Helper\JWT;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class AuthJwtVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = $request->header('Authorization');
        
        if($jwt){
            $user = JWT::decode($jwt, 'laravel');
            
            if($user->expires_at > Carbon::now()->timestamp){
                return $next($request);
            }else{
                return response()->json([
                    'errors' => [
                        'status' => '401',
                        'title' => 'Unauthorized',
                        'detail' => 'El token jwt a expirado',
                        'field' => 'Header Authorization'
                    ] 
                ]);
            }
        }else{
            return response()->json([
                'errors' => [
                    'status' => '401',
                    'title' => 'Unauthorized',
                    'detail' => 'No estas autorizado',
                    'field' => 'Header Authorization'
                ] 
            ]);
        }
    }
}
