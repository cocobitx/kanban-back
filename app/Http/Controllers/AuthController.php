<?php

namespace App\Http\Controllers;

use App\Helper\JWT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function signup(Request $req)
    {
        try {
            $data = Validator::make($req->all(),[
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' =>'required|string|confirmed',
            ]);    
    
            if($data->fails()){
                $errors = $data->errors()->toArray();
                $formatedErrors = [];
    
                foreach($errors as $field => $message){
                    array_push($formatedErrors,[
                        'status' => '422',
                        'title' => 'Unprocessable Entity',
                        'detail' => $message[0],
                        'field' => $field
                    ]);
                }
    
                return response()->json([
                    'errors' => $formatedErrors,
                    'meta' => [
                        'status' => '422',
                        'message' => 'Se ah producido un error de validación!'
                    ]
                ],422);
            }else{
    
                $user = User::create([
                    'name' => $req->name,
                    'email' => $req->email,
                    'password' => Hash::make($req->password),
                    'picture' => ''
                ]);
    
                return response()->json([
                    'data' => [
                        'type' => 'users',
                        'id' => $user->id,
                        'attributes' => [
                           'name' => $user->name,
                           'email' => $user->email,
                        ]
                    ],
                    'meta' => [
                        'status' => '201',
                        'message' => 'El Usuario a sido creado correctamente!'
                    ]
                ],201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '500',
                        'title' => 'Server error',
                        'detail' => 'Se presento un error en el servidor'
                    ]
                ],
                'meta' => [
                    'status' => '500',
                    'message' => 'Se presento un error en el servidor'
                ]
            ],500); 
        }
    }

    public function signin(Request $req)
    {
        $data = Validator::make($req->all(),[
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if($data->fails()){
            $errors = $data->errors()->toArray();
            $formatedErrors = [];

            foreach($errors as $field => $message){
                array_push($formatedErrors,[
                    'status' => '422',
                    'title' => 'Unprocessable Entity',
                    'detail' => $message[0],
                    'field' => $field
                ]);
            }

            return response()->json([
                'errors' => $formatedErrors,
                'meta' => [
                    'status' => '422',
                    'message' => 'Se ah producido un error de validación!'
                ]
            ], 422);
        }else{
            $user = User::where('email','=',$req->email)->first();
            if(!empty($user)){
                if(Hash::check($req->password, $user->password)){
                    $token = JWT::encode([
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'expires_at' => Carbon::now()->addDays(1)->timestamp
                    ],'laravel');

                    User::where('email','=',$req->email)->update([
                        'remember_token' => sha1($token)
                    ]);
                    
                    return response()->json([
                        'data' => [
                            'type' => 'authenticate',
                            'attributes' => [
                                'token' => $token,
                            ]
                        ],
                        'meta' => [
                            'status' => '201',
                            'message' => 'El Usuario a sido creado correctamente!'
                        ]
                    ]);
                }else{
                    return response()->json([
                        'errors' => [
                            'status' => '401',
                            'title' => 'Unauthorized',
                            'detail' => 'El correo electronico o contraseña no coinciden',
                        ],
                        'meta' => [
                            'status' => '401',
                            'message' => 'Se ah producido un error de validación!'
                        ]
                    ],401);
                }
            }else{
                return response()->json([
                    'errors' => [
                        'status' => '401',
                        'title' => 'Unauthorized',
                        'detail' => 'El correo electronico o contraseña no coinciden',
                    ],
                    'meta' => [
                        'status' => '401',
                        'message' => 'Se ah producido un error de validación!'
                    ]
                ]);
            }
        }
    }
}
