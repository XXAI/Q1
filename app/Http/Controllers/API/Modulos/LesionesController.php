<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Controllers\Controller;

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

            if($parametros['parte'] == 1)
            {
                $validation_rules = [
                    'id'=>'required|unique:permissions',
                    'description' => 'required',
                    'group' => 'required'
                ];
                $validation_error_messajes = [
                    'id.required' => 'El ID es requerido',
                    'id.unique' => 'El ID debe ser único',
                    'description.required' => 'La descripción es requerida',
                    'group.required' => 'El grupo es requerido'
                ];
            }else if($parametros['parte'] == 2)
            {

            }else if($parametros['parte'] == 3)
            {
                
            }else if($parametros['parte'] == 4)
            {
                
            }else if($parametros['parte'] == 5)
            {
                
            }else if($parametros['parte'] == 6)
            {
                
            }

            
            $resultado = Validator::make($parametros,$validation_rules,$validation_error_messajes);
            $return_data['data'] = [];

            return response()->json($return_data,HttpResponse::HTTP_OK);
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
