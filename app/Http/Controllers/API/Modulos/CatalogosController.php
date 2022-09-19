<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Controllers\Controller;

use App\Http\Requests;

use DB;

use App\Models\Catalogos\Entidades;
use App\Models\Catalogos\Localidades;
use App\Models\Catalogos\Municipios;
use App\Models\Catalogos\Vehiculos;

class CatalogosController extends Controller
{
    public function getCatalogos(Request $request){
        try{
            $parametros = $request->all();
            
            $data = [];
            foreach ($parametros as $clave => $valor)
            {
                if($clave == 'Estados')
                {
                    
                    $data['Entidades'] = ($parametros[$clave] != 0)? Entidades::find($parametros[$clave]):Entidades::all();
                }else if($clave == 'Municipio')
                {
                    $data['Municipio'] = ($parametros[$clave] != 0)? Municipios::find($parametros[$clave]):Municipios::all();
                }else if($clave == 'TipoVehiculo')
                {
                    $data['TipoVehiculo'] = ($parametros[$clave] != 0)? Vehiculos::find($parametros[$clave]):Vehiculos::all();
                    //$data['Municipio'] = $this->getMunicipio()['data'];
                }
            }
            
            return response()->json(['data'=>$data],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /*public function getEntidades(){
        try{
            
            $data = Entidades::orderBy("descripcion")->all();

            return response()->json($data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function getMunicipio(){
        try{
            
            $data = Municipios::orderBy("descripcion")->all();

            return response()->json(['data'=>$data],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function getLocalidad(Request $request, $id){
        try{
            $parametros = $request->all();
            $data = Localidades::orderBy("descripcion")->where("catalogo_municipio_id", $id);

            if($parametros['localidad'])
            {
                $data =$data->whereRaw("descripcion like '%",$parametros['']."%");
            }

            $data = $data->all();
            
            return response()->json(['data'=>$data],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }*/
}
