<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB, \File, \Store;

use App\Http\Requests;
use App\Models\Lesiones;
use App\Models\RelTipoAccidente;
use App\Models\RelVehiculos;
use App\Models\RelVictimasLesionados;
use App\Models\RelCausaAccidente;
use App\Models\RelCausaConductor;
use App\Models\RelCausaConductorDetalles;
use App\Models\RelCausaPeaton;
use App\Models\RelCondicionCamino;
use App\Models\RelFallaVehiculo;
use App\Models\RelAgente;
use App\Models\RelCausaPasajero;
use App\Models\RelFotografias;
use App\Models\RelDocumentos;
use App\Models\RelLesionParte;
use App\Models\RelLesionParteTipo;
use Image;

class LesionesController extends Controller
{
    public function index(Request $request)
    {
        try{
            //$accessData = $this->getUserAccessData();
            $parametros = $request->all();
            
            $object = Lesiones::with("municipio","localidad");
            
            //Filtros, busquedas, ordenamiento
            if(isset($parametros['query']) && $parametros['query']){
                $object = $object->where(function($query)use($parametros){
                    return $query->where('id','LIKE','%'.$parametros['query'].'%')
                                ->orWhere('colonia','LIKE','%'.$parametros['query'].'%')
                                ->orWhere('calle','LIKE','%'.$parametros['query'].'%');
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
            $return_data = Lesiones::with("tipoAccidente", "vehiculo.tipo", "vehiculo.marca", "vehiculo.estado", "causaAccidente", 
                                            "causaConductor", "causaConductorDetalle", "causaPeaton", "causaPasajero", "fallaVehiculo", 
                                            "condicionCamino", "agentes", "victima.LesionParte.lesionVictima","victima.CluesHospitalizacion")->find($id);

            return response()->json($return_data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function getVehiculos(Request $request)
    {
        try{
            $parametros = $request->all();
            $return_data = RelVehiculos::with("tipo","marca")->where("lesiones_id", $parametros['lesion_id'])->get();

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
    public function update(Request $request, $id)
    {
        try{
           
            $parametros = $request->all(); 
            $return_data = array();
            if($parametros['etapa'] == 1)
            {
                $return_data = $this->GuardarLugar($parametros, $id);
                
            }else if($parametros['etapa'] == 2)
            {
                $return_data = $this->GuardarZona($parametros, $id);
            }else if($parametros['etapa'] == 3)
            {
                $return_data = $this->GuardarTipo($parametros, $id);
            }else if($parametros['etapa'] == 4)
            {
                $return_data = $this->GuardarCausa($parametros, $id);
            }else if($parametros['etapa'] == 5)
            {
                
                $return_data = $this->GuardarVictima($parametros, $id);
            }else if($parametros['etapa'] == 6)
            {
                
            }
            if($return_data['estatus'] == false)
            {
                unset($return_data['estatus']);
                return response()->json(["error" => $return_data], HttpResponse::HTTP_CONFLICT);
            }

            return response()->json($return_data,HttpResponse::HTTP_OK);
        }catch(\Exception $e){

            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function GuardarLugar($parametros, $edicion = 0)
    {
        try{
            $mensajes = [
                'required'      => "required"
            ];
            
            $reglas = [
                'fecha'=>'required',
                'hora' => 'required',
                'entidad' => 'required',
                'municipio' => 'required',
                'localidad' => 'required',
                'colonia' => 'required',
                'calle' => 'required',
                'no' => 'required',
                'cp' => 'required',
                'latitud' => 'required',
                'longitud' => 'required'
            ];
            $respuesta = 0;

            
            $resultado = Validator::make($parametros,$reglas,$mensajes);

            if($resultado->passes()){
                DB::beginTransaction();
                if($edicion == 0)
                {
                    $obj = new Lesiones();
                }else
                {
                    $obj = Lesiones::find($edicion);
                }

                /*Cambiar tildes */
                $parametros['colonia'] = $this->Tildes($parametros['colonia']);
                $parametros['calle'] = $this->Tildes($parametros['calle']);
                $parametros['no'] = $this->Tildes($parametros['no']);          
                /* */

                $loggedUser = auth()->userOrFail();
                $obj->fecha                     = $parametros['fecha'];
                $obj->hora                      = $parametros['hora'];
                $obj->entidad_federativa_id     = $parametros['entidad'];
                $obj->municipio_id              = $parametros['municipio'];
                $obj->localidad_id              = $parametros['localidad']['id'];
                $obj->colonia                   = strtoupper($parametros['colonia']);
                $obj->calle                     = strtoupper($parametros['calle']);
                $obj->numero                    = strtoupper($parametros['no']);
                $obj->cp                        = $parametros['cp'];
                $obj->latitud                   = $parametros['latitud'];
                $obj->longitud                  = $parametros['longitud'];
                $obj->user_id                   = $loggedUser->id;

                $obj->save();
                DB::commit();
                return ['estatus'=>true, "data"=>$obj];
            }else{
                DB::rollback();
                return ['estatus'=>false, "data"=>$resultado->errors()];
            }
        }catch(\Exception $e){
            DB::rollback();
            return ['estatus'=>false, "data"=>$e->getMessage()];
        }
    }

    public function Tildes($palabra)
    {
        $palabra = strtoupper($palabra);
        $palabra = str_replace("Á", "A", $palabra);
        $palabra = str_replace("É", "E", $palabra);
        $palabra = str_replace("Í", "I", $palabra);
        $palabra = str_replace("Ó", "O", $palabra);
        $palabra = str_replace("Ú", "U", $palabra);
        return $palabra;
    }

    public function GuardarZona($parametros, $edicion = 0)
    {
        try{
            $mensajes = [
                'required'      => "required"
            ];
            
            $reglas = [
                'zona'=>'required',
                //'calle1' => 'required',
                //'calle2' => 'required',
                'via' => 'required'
            ];
            $respuesta = 0;
            $resultado = Validator::make($parametros,$reglas,$mensajes);

            if($resultado->passes()){
                DB::beginTransaction();
                $obj = Lesiones::find($edicion);
               
                /*Cambiar tildes */
                $parametros['calle1'] = $this->Tildes($parametros['calle1']);
                $parametros['calle2'] = $this->Tildes($parametros['calle2']);
                $parametros['referencia'] = $this->Tildes($parametros['referencia']);          
                $parametros['otro_camino'] = $this->Tildes($parametros['otro_camino']);          
                $parametros['otro_tipo_via'] = $this->Tildes($parametros['otro_tipo_via']);          
                /* */

                $obj->zona_id = $parametros['zona'];
                $obj->estatal_id = $parametros['carretera'];
                $obj->interseccion_id = $parametros['interseccion'];
                $obj->calle1 = strtoupper($parametros['calle1']);
                $obj->calle2 = strtoupper($parametros['calle2']);
                $obj->punto_referencia = strtoupper($parametros['referencia']);
                $obj->tipo_camino = $parametros['tipo_camino'];
                $obj->otro_tipo_camino = $parametros['otro_camino'];
                $obj->via_id = $parametros['via'];
                $obj->tipo_pavimentado = $parametros['tipo_pavimentado'];
                $obj->tipo_via_id = $parametros['tipo_via'];
                $obj->otro_tipo_via = strtoupper($parametros['otro_tipo_via']);

                $obj->save();
                DB::commit();
                return ['estatus'=>true, "data"=>$obj];
            }else{
                return ['estatus'=>false, "data"=>$resultado->errors()];
            }
        }catch(\Exception $e){
            DB::rollback();
            return ['estatus'=>false, "data"=>$e->getMessage()];
        }
    }

    public function GuardarTipo($parametros, $edicion = 0)
    {
        try{
            $obj = Lesiones::find($edicion);
            if($parametros['tipoAccidente_12'] == true)
            {
                $obj->otro_tipo_accidente = $parametros['otro_tipo_accidente'];
                $obj->save();
            }
            $resultado = Array();
            $resultadoVehiculos = Array();
            $data = Array();
            RelTipoAccidente::where("lesiones_id",$edicion)->forceDelete();
            foreach ($parametros as $key => $value) {
                if($value == true)
                {
                    $entero = str_replace("tipoAccidente_", "", $key);
                    if(is_numeric($entero))
                    {
                        $resultado[] =  new RelTipoAccidente(['rel_tipo_accidente_id' => $entero]);
                    }
                    
                }
            }

            RelVehiculos::where("lesiones_id",$edicion)->forceDelete();
            foreach ($parametros['vehiculos'] as $key => $value) {
                $resultadoVehiculos[] =  new RelVehiculos($value);
            }

            $obj->tipoAccidente()->saveMany($resultado);
            $obj->vehiculo()->saveMany($resultadoVehiculos);
            
            $data[] = $resultado;
            $data[] = $resultadoVehiculos;
            return ['estatus'=>true, "data"=>$data];
        
        }catch(\Exception $e){
            DB::rollback();
            return ['estatus'=>false, "data"=>$e->getMessage()];
        }
    }

    public function GuardarVictima($parametros, $edicion = 0)
    {
        try{
            
            $obj = Lesiones::find($edicion);
            $resultado = Array();
            
            RelVictimasLesionados::where("lesiones_id",$edicion)->forceDelete();
            foreach ($parametros['victimas'] as $key => $value) {
                //$resultado[] =  new RelVictimasLesionados($value);
                $value['lesiones_id'] = $edicion;
                $registro =$value;
                $registro['lesiones_id']=$obj->id;
                if($value['hospitalizacion'] == 2)
                {
                    $registro['municipio_hospitalizacion'] = null;
                    $registro['clues'] = null;
                }
                $lesiones = RelVictimasLesionados::create($registro);
                //Lesiones Nuevo
                
                foreach ($value['lesiones'] as $key2 => $value2) {
                    //$resultado[] =  new RelVictimasLesionados($value);
                    $value2['rel_victimas_lesionados_id'] = $lesiones->id;
                    
                    $obj_lesion = RelLesionParte::create($value2);
                    
                    $arreglo_lesion = [];
                    if($value2['op_1'] == true)
                    {
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>1]);
                    }
                    if($value2['op_2'] == true)
                    {
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>2]);
                    }
                    if($value2['op_3'] == true){
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>3]);
                    }
                    if($value2['op_4'] == true){
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>4]);
                    }
                    if($value2['op_5'] == true){
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>5]);
                    }
                    if($value2['op_6'] == true){
                        $arreglo_lesion[] = new RelLesionParteTipo(['opcion'=>6]);
                    }

                    $obj_lesion->lesionVictima()->saveMany($arreglo_lesion);
                }
            }
            
            //$obj->victima()->saveMany($resultado);
            

            $data[] = $resultado;
            return ['estatus'=>true, "data"=>$data];
        
        }catch(\Exception $e){
            DB::rollback();
            return ['estatus'=>false, "data"=>$e->getMessage()];
        }
    }

    public function GuardarCausa($parametros, $edicion = 0)
    {
        try{
            $obj = Lesiones::find($edicion);
            RelCausaAccidente::where("lesiones_id",$edicion)->forceDelete();
            $resultado = Array();
            $valores = Array();
            $causa = Array();
            foreach ($parametros as $key => $value) {
                if($value == true)
                {
                    if(is_numeric(str_replace("causas_", "", $key)))
                    {
                        $valores[] = (int)str_replace("causas_", "", $key);
                    }
                }
                
            }
            
            //Primera Etapa
            
            foreach ($valores as $key => $value) {
                RelCausaAccidente::create(["rel_causa_accidente_id" => $value, "lesiones_id"=>$edicion]);
                switch ($value) {
                    case 1:
                        RelCausaConductor::where("lesiones_id",$edicion)->forceDelete();
                        RelCausaConductorDetalles::where("lesiones_id",$edicion)->forceDelete();
                        $conductor = Array();
                        foreach ($parametros['conductor'] as $key => $value) {
                            if($value == true)
                            {
                                if(is_numeric(str_replace("tipo_", "", $key)))
                                {
                                    $entero = (int)str_replace("tipo_", "", $key);
                                    if(is_numeric($entero))
                                    {
                                        $conductor[] =  new RelCausaConductor(['rel_causa_conductor_id' => $entero]);
                                    }
                                    if($entero == 13)
                                    {
                                        /*Cambiar tildes */
                                        $parametros['conductor']['otro'] = $this->Tildes($parametros['conductor']['otro']);
                                              
                                        /* */
                                        $obj->otro_causa_conductor = $parametros['conductor']['otro'];
                                    }
                                }
                            }
                        }
                        $obj->causaConductor()->saveMany($conductor);
                        $data = $parametros['conductor'];
                        $registro = ["sexo_id" => $data['sexo'], "alcoholico" => $data["aliento_alcoholico"], "cinturon" => $data['cinturon_seguridad'], "edad" => $data['edad'], "lesiones_id" => $edicion ];
                        RelCausaConductorDetalles::create($registro);
                    break;
                    case 2:
                        RelCausaPeaton::where("lesiones_id",$edicion)->forceDelete();
                        $peaton = Array();
                        foreach ($parametros['peaton'] as $key => $value) {
                            if($value == true)
                            {
                                if(is_numeric(str_replace("tipo_", "", $key)))
                                {
                                    $entero = (int)str_replace("tipo_", "", $key);
                                    if(is_numeric($entero))
                                    {
                                        $peaton[] =  new RelCausaPeaton(['rel_causa_peaton_id' => $entero]);
                                    }
                                    if($entero == 6)
                                    {
                                        $obj->otro_causa_peaton = $parametros['peaton']['descripcion_otro'];
                                    }
                                }
                            }
                        }
                        $obj->causaPeaton()->saveMany($peaton);
                    break;
                    case 3:
                        //RelCausaPasajero::where("lesiones_id",$edicion)->forceDelete();
                        RelCausaPasajero::create(['causa_pasajero' => $parametros['pasajero']['causa_pasajero'], 'lesiones_id' => $edicion]);
                    break;
                    case 4:
                        RelFallaVehiculo::where("lesiones_id",$edicion)->forceDelete();
                        $falla = Array();
                        foreach ($parametros['falla'] as $key => $value) {
                            if($value == true)
                            {
                                if(is_numeric(str_replace("tipo_", "", $key)))
                                {
                                    $entero = (int)str_replace("tipo_", "", $key);
                                    if(is_numeric($entero))
                                    {
                                        $falla[] =  new RelFallaVehiculo(['rel_falla_vehiculo_id' => $entero]);
                                    }
                                   
                                    if($entero == 6)
                                    {
                                         /*Cambiar tildes */
                                         $parametros['falla']['descripcion_otro'] = $this->Tildes($parametros['falla']['descripcion_otro']);
                                              
                                         /* */
                                        $obj->otro_falla_accidente = $parametros['falla']['descripcion_otro'];
                                    }
                                }
                            }
                        }
                        $obj->fallaVehiculo()->saveMany($falla);
                    break;
                    case 5:
                        RelCondicionCamino::where("lesiones_id",$edicion)->forceDelete();
                        $camino = Array();
                        foreach ($parametros['camino'] as $key => $value) {
                            if($value == true)
                            {
                                if(is_numeric(str_replace("tipo_", "", $key)))
                                {
                                    $entero = (int)str_replace("tipo_", "", $key);
                                    
                                    if(is_numeric($entero))
                                    {
                                        $camino[] =  new RelCondicionCamino(['rel_condicion_camino_id' => $entero]);
                                    }
                                   
                                    if($entero == 6)
                                    {
                                          /*Cambiar tildes */
                                          $parametros['camino']['descripcion_otro'] = $this->Tildes($parametros['camino']['descripcion_otro']);
                                              
                                          /* */
                                        $obj->otro_tipo_camino = $parametros['camino']['descripcion_otro'];
                                    }
                                }
                            }
                        }
                        $obj->condicionCamino()->saveMany($camino);
                    break;
                    case 6:
                        RelAgente::where("lesiones_id",$edicion)->forceDelete();
                        $agentes = Array();
                        foreach ($parametros['agentes'] as $key => $value) {
                            if($value == true)
                            {
                                if(is_numeric(str_replace("tipo_", "", $key)))
                                {
                                    $entero = (int)str_replace("tipo_", "", $key);
                                    if(is_numeric($entero))
                                    {
                                        $agentes[] =  new RelAgente(['rel_agente_natural_id' => $entero]);
                                    }
                                    
                                    if($entero == 6)
                                    {
                                        /*Cambiar tildes */
                                        $parametros['agentes']['descripcion_otro'] = $this->Tildes($parametros['agentes']['descripcion_otro']);
                                              
                                        /* */
                                        $obj->otro_falla_accidente = $parametros['agentes']['descripcion_otro'];
                                    }
                                }
                            }
                        }
                        $obj->agentes()->saveMany($agentes);
                    break;
                }
            }
            $obj->save();
            //$obj->causaAccidente()->saveMany($causa);

            $data[] = $resultado;
            return ['estatus'=>true, "data"=>$causa];
        
        }catch(\Exception $e){
            DB::rollback();
            return ['estatus'=>false, "data"=>$e->getMessage()];
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

    public function getImagenes($id)
    {
        try{
            $obj = RelFotografias::where("lesiones_id",$id)->get();
            
            $index = 0;
            foreach ($obj as $key => $value) {
                $obj[$index]->imagen = base64_encode(\Storage::get('public\\fotos\\'.$id."\\".$value->nombre_imagen.".jpg"));  
                $index++;
            }
            

            return response()->json(['data'=>$obj], HttpResponse::HTTP_OK);
         }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function delImagenes($id, Request $request)
    {
        try{
            $parametros = $request->all();
            $obj = RelFotografias::where("lesiones_id",$id)->where("nombre_imagen", $parametros['nombre'])->forceDelete();
            
            //return response()->json(['data'=>$parametros], HttpResponse::HTTP_OK);
            unlink(storage_path("app\\public\\fotos\\".$id."\\".$parametros['nombre'].".jpg"));
            //return response()->json(['data'=>"correcto"], HttpResponse::HTTP_OK);
            return response()->json(['data'=>$obj], HttpResponse::HTTP_OK);
         }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function getDocumentos($id)
    {
        try{
            $obj = RelDocumentos::where("lesiones_id",$id)->get();
            return response()->json(['data'=>$obj], HttpResponse::HTTP_OK);
         }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
    
    public function delDocumentos($id, Request $request)
    {
        try{
            $parametros = $request->all();
            $obj = RelDocumentos::find($parametros['identificador']);

            unlink(storage_path("app\\public\\documentos\\".$id."\\".$obj->nombre));
            $obj = $obj->forceDelete();
            
            return response()->json(['data'=>"correcto"], HttpResponse::HTTP_OK);
         }catch(\Exception $e){
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function fotografias(Request $request)
    {
        try{
            ini_set('memory_limit', '-1');
            $parametros = $request->all();
            
            $x= 0;
            while($request->hasFile('archivo_'.$x))
            {
                $nombre = \Str::random(10);
                $obj = new RelFotografias();
                $obj->nombre_imagen = $nombre;
                $obj->lesiones_id = $parametros["id"];
                $obj->save();

                $image = $request->file('archivo_'.$x);
                
                $filePath = storage_path("app\\public\\fotos\\".$parametros["id"]);
                if(!is_dir($filePath))
                {
                    mkdir($filePath, 0777, true);
                }

                $img = Image::make($image->path());
                $img->resize(null, 600, function ($const) {
                    $const->aspectRatio();
                })->save($filePath.'\\'.$nombre.".jpg");

                //$request->file('archivo_'.$x)->storeAs("public/fotos/".$parametros["id"], $nombre.".jpg");
                $x++;
            }

            if($x > 0)
            {
                return response()->json(['error'=>"Se subio correctamente las imagenes"], HttpResponse::HTTP_OK);
            }else{
                return response()->json(['error'=>"No se encuentra ninguna imagen"], HttpResponse::HTTP_CONFLICT);
            
            }
            
        }catch(\Exception $e){

            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    public function documentos(Request $request)
    {
        try{
            ini_set('memory_limit', '-1');
            $parametros = $request->all();
            
            $x= 0;
            while($request->hasFile('archivo_'.$x))
            {
                $nombre = \Str::random(10);
                $obj = new RelDocumentos();
                $obj->nombre = $nombre.".pdf";
                $obj->lesiones_id = $parametros["id"];
                $obj->save();

                $request->file('archivo_'.$x)->storeAs("public\\documentos\\".$parametros["id"],$nombre.'.pdf');
                $x++;
            }

            if($x > 0)
            {
                return response()->json(['error'=>"Se subio correctamente los documentos"], HttpResponse::HTTP_OK);
            }else{
                return response()->json(['error'=>"No se encuentra ninguna documentos"], HttpResponse::HTTP_CONFLICT);
            
            }
            
        }catch(\Exception $e){

            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }

    
    public function downloadDocumentos(Request $request, $id)
    {
        ini_set('memory_limit', '-1');
        try{  
        $obj = RelDocumentos::find($id);
        return \Storage::download("public//documentos//".$obj->lesiones_id."//".$obj->nombre);
        
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error'=>['message'=>$e->getMessage(),'line'=>$e->getLine()]], HttpResponse::HTTP_CONFLICT);
        }
    }
}
