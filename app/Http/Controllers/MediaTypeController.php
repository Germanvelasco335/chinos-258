<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\MediaType;

class MediaTypeController extends Controller
{
    public function showmass(){
        return view("media-types.insert-mass");
    }

    public function storemass(Request $r){
        
        //Arreglo de mediatypes repetidos en bd
        $repetidos=[];

        //Reglas de validacion
        $reglas = [
            'media-types' => 'required|mimes:csv,txt'
        ];

        //Crear validador
        $validador = Validator::make($r->all()  , $reglas);

        //Validar
        if ($validador->fails()) {
            //return $validador->errors()->first('media-types');
            //enviar mensaje de error de validacion a la vista
            return redirect('media-types/insert')->withErrors($validador);
        }else{
            //Transladar el archivo cargado a Storage
            $r->file("media-types")->storeAs('media-types', $r->file("media-types")->getClientOriginalName());
            
            //Ruta de larchivo en Storage;
            $ruta =  base_path(). '\storage\app\media-types\\'. $r->file('media-types')->getClientOriginalName();

            //Abrir el archivo almacenado para lectura:
            if( ($puntero = fopen($ruta , 'r')) !== false){
                //Variable a contar las veces que se insertan
                $contador = 0;
                //Recorrer cadaa linea del csv: fgetcsv, utilizando el puntero que representa el archivo
                while ( ($linea = fgetcsv($puntero)) !==false ){

                        //Buscar el media type $linea[0]
                        $conteo = MediaType::where('Name' , '=' , $linea[0])->get()->count();

                        //Si no hay registros en mendia type que se llamen igual
                        if( $conteo == 0){

                            //Crear objeto MediaType
                            $m = new MediaType();
                            //Asigno el nombre del MediaTyped
                            $m->Name = $linea[0];
                            //Gravo en sqlite el nuevo MediaTyped
                            $m->save();
                            //Aumenta en 1 el contador
                            $contador++;

                        }else{ //Si hay registros del mediatype
                               //Agregar una casilla al arreglo repetidos
                               $repetidos[] = $linea[0]; 


                        }

                        
                }

                //Todo: poner mensaje de grabacion de carga masiva
                //en la vista
                //Si hubo repetidos
                if ( count( $repetidos) == 0) {

                    return redirect('media-types/insert')->with('exito' , 
                                                            "Carga masiva de medios realizada, Registros ingresados: $contador ");
                    
                }else{

                    return redirect('media-types/insert')->with('exito' , 
                                                            "Carga masiva con las siguientes excepciones")
                                                         ->with("repetidos" , $repetidos);   

                }
                
                
            }
        
            
        }

        
    }
}
