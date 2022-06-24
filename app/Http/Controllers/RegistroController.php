<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Mail\MailSolicitud;
use Illuminate\Support\Facades\Mail;

use App\Usuario;
use App\Solicitud;
use App\Dependencia;

class RegistroController extends Controller{

    public function registrar(Request $request){
        
        try {
            
            // * Verificar que no exista ya un usuario con el mismo correo
            $usuario = Usuario::where('email', $request->email)->first();

            if ($usuario) {
                
                // * Responder
                $response = [
                    'icon' => 'info',
                    'text' => 'Ya existe un usuario registrado con el mismo correo electrónico ingresado.',
                ];

                return response()->json($response, 400);

            }

            $nuevo_usuario = Usuario::create([
                'nombre' => $request->nombre, 
                'id_dependencia' => $request->id_dependencia, 
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Crypt::encrypt($request->password)
            ]);

            // * Buscar la dependencia
            $dependencia = Dependencia::find($nuevo_usuario->id_dependencia);

            $nuevo_usuario->dependencia = $dependencia->nombre;

            // * Crear solicitu de aceptación
            $nueva_solicitud = Solicitud::create([
                'id_usuario' => $nuevo_usuario->id,
                'estado' => 1
            ]);

            $nuevo_usuario->id_solicitud = $nueva_solicitud->id;

            // * Notificar por correo
            Mail::to('gerson.roely@gmail.com')->send(new MailSolicitud($nuevo_usuario));

            // * Responder sobre registro exitoso
            $response = [
                'icon' => 'success',
                'title' => 'Excelente',
                'text' => 'El registro se ha realizado de manera exitosa.  Es necesario que su cuenta sea aprobada antes de poder ingresar.'
            ];

            return response()->json($response, 200);

        } catch (\Throwable $th) {

            return response()->json($th->getMessage());

        }

    }

}

