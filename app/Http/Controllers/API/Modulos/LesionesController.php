<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB;

use App\Http\Requests;
use App\Models\Lesiones;

class LesionesController extends Controller
{
    public function index(Request $request)
    {
        try{
            $accessData = $this->getUserAccessData();
            $parametros = $request->all();
            
            $object = Lesiones::with('municipio.localidad');
            
            //Filtros, busquedas, ordenamiento
            if(isset($parametros['query']) && $parametros['query']){
                $object = $object->where(function($query)use($parametros){
                    return $query->where('folio','LIKE','%'.$parametros['query'].'%');
                                /*->orWhere('descripcion','LIKE','%'.$parametros['query'].'%')
                                ->orWhere('direccion','LIKE','%'.$parametros['query'].'%');*/
                });
            }

            /*if(!$accessData->is_superuser){
                $proyectos = $proyectos->where(function($query)use($accessData){
                                                    $query->whereIn('direccion_id',$accessData->direcciones_ids)
                                                            ->orWhereIn('id',$accessData->proyectos_ids);
                                                });
            }*/
            
            $object = $object->orderBy('updated_at','desc');

            if(isset($parametros['page'])){
                $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
                $object = $object->paginate($resultadosPorPagina);

            } else {
                $object = $object->get();
            }

            return response()->json(['data'=>$object],HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $return_data = Lesiones::find($id);

            return response()->json($return_data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            
            $validation_rules = [];
        
            $validation_error_messajes = [];

            $parametros = $request->all(); 
            if($parametros['etapa'] == 1)
            {
                $return_data = $this->GuardarLugar($parametros);
               
            }else if($parametros['etapa'] == 2)
            {

            }else if($parametros['etapa'] == 3)
            {
                
            }else if($parametros['etapa'] == 4)
            {
                
            }else if($parametros['etapa'] == 5)
            {
                
            }else if($parametros['etapa'] == 6)
            {
                
            }

            return response()->json($return_data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function GuardarLugar($parametros)
    {
        try{
            $validation_rules = [
                'fecha'=>'required',
                'hora' => 'required',
                'entidad' => 'required',
                'municipio' => 'required',
                'localidad' => 'required',
                'colonia' => 'required',
                'calle' => 'required',
                'no' => 'required',
                'latitud' => 'required',
                'longitud' => 'required'
            ];
            $validation_eror_messages = [
                'fecha.unique' => 'La Fecha es requerido',
                'hora.required' => 'La hora es requerido',
                'entidad.required' => 'La entidad  es requerido',
                'municipio.required'=>'El municipio  es requerido',
                'localidad.required' => 'La localidad  es requerido',
                'colonia.required'=> 'la colonia es requerido',
                'calle.required' => 'La calle es requerido',
                'no.required' => 'El no es requerido',
                'latitud.required' => 'La latitud es requerido',
                'longitud.required' => 'La longitud es requerido'
            ];
            $resultado = Validator::make($parametros,$validation_rules,$validation_eror_messages);

            if($resultado->passes()){
                DB::beginTransaction();
                $obj = new Lesiones();//::create($parametros);
                $obj->fecha = $parametros['fecha'];
                $obj->hora = $parametros['hora'];
                $obj->entidad_federativa_id = $parametros['entidad'];
                $obj->municipio_id = $parametros['municipio'];
                $obj->localidad_id = $parametros['localidad'];
                $obj->colonia = $parametros['colonia'];
                $obj->calle = $parametros['calle'];
                $obj->numero = $parametros['no'];
                $obj->latitud = $parametros['latitud'];
                $obj->longitud = $parametros['longitud'];

                $obj->save();
                DB::commit();
                return response()->json(['data'=>$parametros], HttpResponse::HTTP_OK);
            }else{
                return response()->json(['mensaje' => 'Error en los datos del formulario', 'validacion'=>$resultado->passes(), 'errores'=>$resultado->errors()], HttpResponse::HTTP_CONFLICT);
            }
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $return_data['data'] = [];

            return response()->json($return_data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /*private function getUserAccessData($loggedUser = null){
        if(!$loggedUser){
            $loggedUser = auth()->userOrFail();
        }
        
        $loggedUser->load('direcciones','proyectos');

        $accessData = (object)[];

        $accessData->direcciones_ids = $loggedUser->direcciones->pluck('id');
        $accessData->proyectos_ids = $loggedUser->proyectos->pluck('id');
        $accessData->is_superuser = $loggedUser->is_superuser;

        return $accessData;
    }*/
}
