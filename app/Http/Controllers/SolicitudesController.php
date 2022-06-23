<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Nivel;
use App\Estado;
use App\Solicitud;
use App\Usuario;

class SolicitudesController extends Controller{

    public function obtener_solicitudes(){

        try {
            
            $solicitudes = DB::select("select
                                        t1.id,
                                        t2.nombre,
                                        t2.email,
                                        t2.telefono,
                                        t3.nombre as dependencia,
                                        t4.nombre as estado, 
                                        t4.id as id_estado,
                                        t4.color as color,
                                        t5.nombre as nivel
                                    from solicitud t1
                                    left join usuario t2
                                    on t1.id_usuario = t2.id
                                    left join dependencia t3
                                    on t3.id = t2.id_dependencia
                                    left join estado_solicitud t4
                                    on t1.estado = t4.id
                                    left join nivel t5
                                    on t5.id = t2.id_nivel");

            $headers = [
                [
                    'text' => 'ID',
                    'value' => 'id',
                    'sortable' => false,
                    'width' => '5%'
                ],
                [
                    'text' => 'Nombre',
                    'value' => 'nombre',
                    'sortable' => false,
                    'width' => '20%'
                ],
                [
                    'text' => 'Dependencia',
                    'value' => 'dependencia',
                    'sortable' => false,
                    'width' => '20%'
                ],
                [
                    'text' => 'Teléfono',
                    'value' => 'telefono',
                    'sortable' => false,
                    'width' => '10%'
                ],
                [
                    'text' => 'Nivel',
                    'value' => 'nivel',
                    'sortable' => false,
                    'width' => '15%'
                ],
                [
                    'text' => 'Estado',
                    'value' => 'estado',
                    'sortable' => false,
                    'width' => '15%'
                ],
                [
                    'text' => 'Acción',
                    'value' => 'action',
                    'align' => 'end',
                    'sortable' => false,
                    'width' => '15%'
                ]
            ];

            $response = [
                'items' => $solicitudes,
                'headers' => $headers
            ];

            return response()->json($response, 200);

        } catch (\Throwable $th) {
            
            return response()->json($th, 400);

        }

    }

    public function detalle_solicitud(Request $request){

        try {
            
            $solicitud = DB::select("select
                                        t1.id,
                                        t2.id as id_usuario,
                                        t2.nombre,
                                        t2.email,
                                        t2.telefono,
                                        t3.nombre as dependencia,
                                        t4.nombre as estado, 
                                        t4.id as id_estado,
                                        t5.nombre as nivel,
                                        t5.id as id_nivel
                                    from solicitud t1
                                    left join usuario t2
                                    on t1.id_usuario = t2.id
                                    left join dependencia t3
                                    on t3.id = t2.id_dependencia
                                    left join estado_solicitud t4
                                    on t1.estado = t4.id
                                    left join nivel t5
                                    on t5.id = t2.id_nivel
                                    where t1.id = '$request->id'");

            if (!$solicitud) {
                
                // * Responder cuando la solicitud no se encuentra

            }

            $solicitud = $solicitud[0];
            $niveles = Nivel::all();
            $estados = Estado::all();

            $response = [
                'solicitud' => $solicitud,
                'estados' => $estados,
                'niveles' => $niveles
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function actualizar_solicitud(Request $request){

        try {
            
            // * Actualizar el estado de la solicitud
            $solicitud = Solicitud::find($request->id);

            $solicitud->estado = $request->id_estado;
            $solicitud->save();

            // * Actualizar el nivel del usuario
            $usuario = Usuario::find($request->id_usuario);
            $usuario->id_nivel = $request->id_nivel;
            $usuario->save();

            $response = [
                'message' => 'La solicitud ha sido actualizada exitosamente!',
                'color' => 'success'
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            
            return response()->json(['message' => $th->getMessage, 'color' => 'error']);

        }

    }

}