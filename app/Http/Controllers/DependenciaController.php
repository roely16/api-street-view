<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Dependencia;

class DependenciaController extends Controller{

    public function obtener_dependencias(){
        
        try {
            
            $dependencias = Dependencia::all();

            return response()->json($dependencias, 200);

        } catch (\Throwable $th) {
            
            

        }

    }

}

