<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller{
    public function login(Request $request){
        $credenciais = $request->all(['email','password']);

        $token  = auth('api')->attempt($credenciais);

        if($token){ // usuaario auth com sucesso
            return response()->json(['token', $token]);
        }

        return response()->json(['erro', 'Credenciais invalidas'],403); // 403 proibido,login invalido
    }

    public function logout(){
        auth('api')->logout();

        return response()->json(['sucess', 'Logout realizado com sucesso']);
    }

    public function refresh(){
        $token = auth('api')->refresh(); // cliente encaminho um jwt valido

        return response()->json(['token', $token]);
    }

    public function me(){
        return response()->json(auth()->user());
    }
}
