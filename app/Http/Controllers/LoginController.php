<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\Usuario;
use App\Solicitud;
use App\Estado;
use App\Nivel;

class LoginController extends Controller{

    public function login(Request $request){
        
        try {
            
            $usuario = Usuario::where('email', $request->email)->first();

            if (!$usuario) {
                
                // * Crendenciales incorrectas
                $response = [
                    'icon' => 'error',
                    'text' => 'El correo electrónico o la contraseña son incorrectos.'
                ];

                return response()->json($response, 400);

            }

            if (!Hash::check($request->password, $usuario->password)) {
                
                // * Credenciales incorrectas
                $response = [
                    'icon' => 'error',
                    'text' => 'El correo electrónico o la contraseña son incorrectos.'
                ];

                return response()->json($response, 400);

            }

            // * Validar si la cuenta ya fue aprobada
            $solicitud = Solicitud::where('id_usuario', $usuario->id)->first();

            if ($solicitud->estado != 2) {
                
                $estado = Estado::find($solicitud->estado);

                $response = [
                    'icon' => 'info',
                    'text' => 'La solicitud para habilitación de usuario se encuentra ' . $estado->nombre
                ];

                return response()->json($response, 400);

            }

            $nivel = null;
            
            // * Si el usuario tiene un nivel
            if ($usuario->id_nivel) {
                
                $nivel = Nivel::find($usuario->id_nivel);

            }

            $response = [
                'url' => $nivel ? $nivel->visor : null,
                'id_usuario' => $usuario->id,
                'nombre' => $usuario->nombre
            ];

            return response()->json($response, 200);

        } catch (\Throwable $th) {

            $response = [
                'icon' => 'error',
                'text' => $th->getMessage()
            ];

            return response()->json($response, 400);

        }

    }

}

