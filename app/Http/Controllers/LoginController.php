<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;

use App\Mail\MailPassword;
use Illuminate\Support\Facades\Mail;

use App\Usuario;
use App\Solicitud;
use App\Estado;
use App\Nivel;
use App\Bitacora;

use Carbon\Carbon;

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

            if (Crypt::decrypt($usuario->password) != $request->password) {
                
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

            // Validar si no existe una sesión activa
            $bitacora = Bitacora::where('id_usuario', $usuario->id)
                        ->whereNull('logout_at')
                        ->orderBy('id', 'desc')
                        ->first();

            if ($bitacora) {
                
                if ($bitacora->login_at) {
                
                    // El último registro es de ingreso por lo que existe una sesión activa y no se puede crear otra
                    $response = [
                        'icon' => 'info',
                        'title' => 'Sesión Activa',
                        'text' => '¿Desea cerrar la sesión anterior?',
                        'showCancelButton' => true,
                        'confirmButtonColor' => '#d33',
                        'cancelButtonColor' => '#000',
                        'confirmButtonText' => 'Cerrar Sesión!',
                        'cancelButtonText' => 'No',
                        'reverseButtons' => true,
                        'action' => 'close_session',
                        'id_bitacora' => $bitacora->id
                    ];
    
                    return response()->json($response, 400);
    
                }

            }
            
            $nivel = null;
            
            // * Si el usuario tiene un nivel
            if ($usuario->id_nivel) {
                
                $nivel = Nivel::find($usuario->id_nivel);

            }

            // Registrar en la bitácora el inicio de sesión
            $bitacora = new Bitacora();
            $bitacora->id_usuario = $usuario->id;
            $bitacora->login_at = Carbon::now();
            $bitacora->session_id = $request->tabId;
            $bitacora->save();

            $response = [
                'url' => $nivel ? $nivel->visor : null,
                'id_usuario' => $usuario->id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'tabId' => $bitacora->session_id,
                'bitacora_id' => $bitacora->id
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

    public function recover_password(Request $request){

        try {
            
            $usuario = Usuario::where('email', $request->email)->first();

            if (!$usuario) {
                
                $response = [
                    'icon' => 'info',
                    'text' => 'No existe ninguna cuenta con el correo electrónico ingresado.',
                ];

                return response()->json($response, 400);

            }

            $data = [
                'email' => $usuario->email,
                'password' => Crypt::decrypt($usuario->password)
            ];
        
            Mail::to($usuario->email)->send(new MailPassword($data));

            $response = [
                'icon' => 'success',
                'title' => 'Excelente',
                'text' => 'Se ha enviado un correo electrónico con las instrucciones para recuperar su contraseña.'
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function logout(Request $request){

        try {
            
            // Registrar en la bitácora
            $bitacora = Bitacora::find($request->bitacora_id);
            $bitacora->logout_at = Carbon::now();
            $bitacora->save();

            $response = [
                'status' => 200
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            
            $response = [
                'message' => $th->getMessage()
            ];

            return response()->json($response, 400);

        }

    }

    public function check_session(Request $request){

        try {
            
            // Validar que exista una sesión con el id brindado
            $bitacora = Bitacora::where('session_id', $request->session_id)
                        ->where('logout_at', null)
                        ->first();

            $response = [
                'status' => $bitacora ? 200 : 400
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function close_session(Request $request){

        try {
            
            $bitacora = Bitacora::find($request->id);
            $bitacora->logout_at = Carbon::now();
            $bitacora->save();

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

}

