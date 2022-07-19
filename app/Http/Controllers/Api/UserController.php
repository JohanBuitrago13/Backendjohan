<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=> 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'

        ]);

        $user = new User();
        $user-> name = $request->name;
        $user-> email = $request->email;
        $user-> password = Hash::make($request->password);
        $user-> save();

        return response()->  json([
            "status" => 1,
            "msg" => "Registro de usuario exitoso",
        ]);
    }

    public function login(Request $request){

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", "=", $request->email)->first();

        if (isset($user->id)) {
            if(Hash::check($request->password, $user->password)){
                //creacion de token
                $token = $user->createToken("auth_token")->plainTextToken;
                //si todo esta bien
                return response()->json([
                    "status" => 1,
                    "msg" => "Usuario logueado con exito",
                    "access_token" => $token
                ]);
            }else{
                return response()->json([
                    "status" => 0,
                    "msg" => "Contraseña incorrecta",
                ], 404);
            }
        }else{
            return response()->json([
                "status" => 0,
                "msg" => "Usuario no registrado",
            ], 404);
        }
    }

    public function userProfile(){
        $information = auth()->user();
        return response()->json([
            "status" => 0,
            "msg" => "Acerca del perfil de usuario",
            "data"=> $information
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => 1,
            "msg" => "Cierre de sesión",
        ]);

    }
}
