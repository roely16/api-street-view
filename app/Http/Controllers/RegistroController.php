<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Usuario;
use App\Solicitud;

class RegistroController extends Controller{

    public function registrar(Request $request){
        
        try {
            
            // * Verificar que no exista ya un usuario con el mismo correo
            $usuario = Usuario::where('email', $request->email)->first();

            if ($usuario) {
                
                // * Responder

            }

            $nuevo_usuario = Usuario::create([
                'nombre' => $request->nombre, 
                'id_dependencia' => $request->id_dependencia, 
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password)
            ]);

            // * Crear solicitu de aceptación
            $nueva_solicitud = Solicitud::create([
                'id_usuario' => $nuevo_usuario->id,
                'estado' => 1
            ]);

            // * Notificar por correo

            return response()->json($nuevo_usuario);

        } catch (\Throwable $th) {
            //throw $th;

            return response()->json($th->getMessage());

        }

    }

}

