<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Solicitud extends Model{
    
    protected $table = 'solicitud';

    protected $fillable = ['id_usuario', 'estado'];

    public function usuario(){
        
    }

}