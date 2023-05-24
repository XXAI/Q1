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
use App\Models\Catalogos\TipoVehiculos;
use App\Models\Catalogos\Clues;

class CatalogosController extends Controller
{
    public function getCatalogos(Request $request){
        try{
            $parametros = $request->all();
            $user = $this->getUserAccessData();
            $data = [];
            foreach ($parametros as $clave => $valor)
            {
                if($clave == 'Estados')
                {
                    
                    $data['Entidades'] = ($parametros[$clave] != 0)? Entidades::find($parametros[$clave]):Entidades::all();
                }else if($clave == 'Municipio')
                {
                    if($parametros[$clave] != 0)
                    {
                        $data['Municipio'] = Municipios::find($parametros[$clave]);
                    }else{
                        if($user->is_superuser)
                        {
                            $data['Municipio'] =Municipios::all();
                        }else{
                            $data['Municipio'] =Municipios::where("id",$user->catalogo_municipio_id)->get();
                        }
                    }
                   
                }else if($clave == 'TipoVehiculo')
                {
                    $data['TipoVehiculo'] = ($parametros[$clave] != 0)? TipoVehiculos::find($parametros[$clave]):TipoVehiculos::all();
                }else if($clave == 'Vehiculo')
                {
                    $data['Vehiculo'] = ($parametros[$clave] != 0)? Vehiculos::find($parametros[$clave]):Vehiculos::all();
                }
            }
            
            return response()->json(['data'=>$data],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function getCatalogoLocalidad(Request $request){
        try{
            $parametros = $request->all();
            $data = Localidades::where("catalogo_municipios_id",$parametros['municipio_id'])->orderBy("descripcion");
            if(isset($parametros['query']) && $parametros['query']){
                $data = $data->where(function($query)use($parametros){
                    return $query->where('descripcion','LIKE','%'.$parametros['query'].'%');
                });
            }

            $data = $data->get();

            return response()->json($data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function getCatalogoClues(Request $request){
        try{
            $parametros = $request->all();
            $data = clues::where("catalogo_municipio_id",$parametros['municipio_id'])->orderBy("descripcion");
            if(isset($parametros['query']) && $parametros['query']){
                $data = $data->where(function($query)use($parametros){
                    return $query->where('descripcion','LIKE','%'.$parametros['query'].'%');
                });
            }

            $data = $data->get();

            return response()->json($data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /*
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

    private function getUserAccessData($loggedUser = null){
        if(!$loggedUser){
            $loggedUser = auth()->userOrFail();
        }
    
        return $loggedUser;
    }
}
