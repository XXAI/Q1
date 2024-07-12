<?php

namespace App\Http\Controllers\API\Modulos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use \Validator,\Hash, \Response, \DB, \File, \Store;
use App\Exports\DevReportExport;

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
use App\Models\User;

class ReporteLesionesController  extends Controller
{
    public function reporte(Request $request)
    {
       
    ini_set('memory_limit', '-1');
    $parametros = $request->all();
    $anio = $parametros['anio_reporte'];
    /*$obj = Lesiones::join("catalogo_entidades as b", "lesiones.entidad_federativa_id", "b.id")
                        ->leftjoin("catalogo_municipios as c", "lesiones.municipio_id", "c.id")
                        //->leftjoin("catalogo_localidades as d", "lesiones.localidad_id", "d.id")
            ->select("lesiones.id",
            DB::raw("concat('CHIS-',lesiones.id) as folio"),
            "lesiones.fecha as fecha_incidente",
            "lesiones.hora as hora_incidente",
            "lesiones.created_at as fecha_hora_captura",
            "b.descripcion AS entidad",
            "c.descripcion AS municipio",
            "lesiones.localidad_id AS localidad",
            "lesiones.colonia",
            "lesiones.calle",
            "lesiones.numero",
            "lesiones.cp",
            "lesiones.latitud",
            "lesiones.longitud",
            DB::RAW("IF(lesiones.zona_id = 1, 'ZONA URBANA', 'ZONA SUBURBANA') AS zona"),
            DB::RAW("IF(lesiones.estatal_id = 1, 'SI', 'NO') AS carretera_estatal"),
            DB::RAW("IF(lesiones.interseccion_id = 1, 'SI', 'NO') AS interseccion"),
            "lesiones.calle1 AS entre_calle",
            "lesiones.calle2 AS y_calle",
            "lesiones.punto_referencia",
            DB::RAW("IF(lesiones.via_id = 1, 'PAVIMENTADA', 'NO PAVIMENTADA') AS via"),
            DB::RAW("IF(lesiones.via_id = 1, IF(lesiones.tipo_pavimentado=1, 'ASFALTO','CONCRETO'), IF(lesiones.tipo_via_id = 1, 'TERRACERÍA', IF(lesiones.tipo_via_id = 2, 'EMPEDRADO', 'OTRO'))) AS tipo_via"),
            "lesiones.otro_tipo_via",
            DB::RAW("IF(lesiones.tipo_camino IS NULL ,'',IF(lesiones.tipo_camino = 1,'CAMINO RURAL', IF(lesiones.tipo_camino = 2, 'CARRETERA ESTATAL', 'OTRO'))) AS tipo_camino"),
            "lesiones.otro_tipo_camino")
            ->whereBetween("fecha", [$anio.'-01-01',$anio.'-12-31'])
            ->orderBy("fecha", "asc");*/

        $obj = Lesiones::with("entidad","municipio", "tipoAccidente", "vehiculo.marca", "vehiculo.estado", "vehiculo.tipo", "causaConductor", "causaConductorDetalle", "causaPeaton", "causaPasajero", "fallaVehiculo", "condicionCamino", "agentes", "victimaNoFatal.CluesHospitalizacion", "victimaNoFatal.municipio", "victimaNoFatal.vehiculo.marca", "victimaNoFatal.vehiculo.tipo", "defuncion.vehiculo.marca", "defuncion.vehiculo.tipo", "defuncion.municipio", "defuncion.CluesHospitalizacion")->whereBetween("fecha", [$anio.'-01-01',$anio.'-12-31'])
        //->where("id", 7498)
        ->orderBy("fecha", "asc");
           
        //Calculo de accidentes
        $cantidad_tipo_accidente = $this->getCantidadTipoAccidente($anio);
        //Calculo de Vehiculos
        $cantidad_vehiculos = $this->getVehiculos($anio);
        
        //Calculo de causas
        $cantidad_causa_conductor = $this->getConductor($anio);
        $cantidad_causa_peaton = $this->getPeaton($anio);
        $cantidad_causa_pasajero = $this->getPasajero($anio);
        $cantidad_causa_falla = $this->getFalla($anio);
        $cantidad_causa_camino = $this->getCamino($anio);
        $cantidad_causa_natural = $this->getNatural($anio);
        //

        //Calculo de vitimas
        $cantidad_victimas_lesion = $this->getVictimaLesion($anio);
        $cantidad_victimas_defunsion = $this->getVictimaDefunsion($anio);
        
        $obj_totales = (object) array(
            'cantidad_tipo_accidente' => $cantidad_tipo_accidente,
            'cantidad_vehiculos' => $cantidad_vehiculos,
            'cantidad_causa_conductor' => $cantidad_causa_conductor,
            'cantidad_causa_peaton' => $cantidad_causa_peaton,
            'cantidad_causa_pasajero' => $cantidad_causa_pasajero,
            'cantidad_causa_falla' => $cantidad_causa_falla,
            'cantidad_causa_camino' => $cantidad_causa_camino,
            'cantidad_causa_natural' => $cantidad_causa_natural,
            'cantidad_victimas_lesion' => $cantidad_victimas_lesion,
            'cantidad_victimas_defunsion' => $cantidad_victimas_defunsion,
        );
        //Agregamos los campos a la consulta
        //Tipo Accidente
        /*for ($i=1; $i <= $cantidad_tipo_accidente ; $i++) { 
            $obj = $obj->addSelect(DB::raw("'' as tipo_accidente_".$i));
        }
        $obj = $obj->addSelect(DB::raw("'' as otro_tipo_accidente"));


        ///1
        $obj = $obj->addSelect(DB::raw("'--' as cantidad_vehiculos"));
        
        for ($i=1; $i <= $cantidad_vehiculos; $i++) { 
            $obj = $obj->addSelect(DB::raw("'' as tipo_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as otro_tipo_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as marca_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as otro_marca_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as uso_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as tipo_uso_vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as puesto_disposicion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as placa_pais_".$i));
            $obj = $obj->addSelect(DB::raw("'' as no_ocupantes_".$i));
            $obj = $obj->addSelect(DB::raw("'' as color_".$i));
            $obj = $obj->addSelect(DB::raw("'' as modelo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as con_placas_".$i));
            $obj = $obj->addSelect(DB::raw("'' as entidad_pais_".$i));
            $obj = $obj->addSelect(DB::raw("'' as no_placa_".$i));
        }
        //
        //2
        $obj = $obj->addSelect(DB::raw("'' as causa_conductor"));
        for ($i=1; $i <= $cantidad_causa_conductor; $i++) {
            $obj = $obj->addSelect(DB::raw("'' as conductor_".$i));
         }
         $obj = $obj->addSelect(DB::raw("'' as otro_causa_conductor"));
        //

        //3

        $obj = $obj->addSelect(DB::raw("'' as causa_peaton"));
        for ($i=1; $i <= $cantidad_causa_peaton; $i++) {
            $obj = $obj->addSelect(DB::raw("'' as peaton_".$i));
         }
         $obj = $obj->addSelect(DB::raw("'' as otro_causa_peaton"));
           
        //

        //4
        $obj = $obj->addSelect(DB::raw("'' as causa_pasajero"));
        $obj = $obj->addSelect(DB::raw("'' as causa_pasajero_descripcion"));
        //

        //5
        $obj = $obj->addSelect(DB::raw("'' as causa_falla_vehiculo"));
        for ($i=1; $i <= $cantidad_causa_falla; $i++) {
            $obj = $obj->addSelect(DB::raw("'' as falla_vehiculo_".$i));
         }
         $obj = $obj->addSelect(DB::raw("'' as otro_causa_falla_vehiculo"));

        //

        //6
        $obj = $obj->addSelect(DB::raw("'' as causa_condicion_camino"));
        for ($i=1; $i <= $cantidad_causa_camino; $i++) {
            $obj = $obj->addSelect(DB::raw("'' as condicion_camino_".$i));
         }
         $obj = $obj->addSelect(DB::raw("'' as otro_causa_condicion_camino"));
        //

        //7
        $obj = $obj->addSelect(DB::raw("'' as causa_agente_natural"));
        for ($i=1; $i <= $cantidad_causa_camino; $i++) {
            $obj = $obj->addSelect(DB::raw("'' as agente_natural_".$i));
         }
         $obj = $obj->addSelect(DB::raw("'' as otro_agente_natural"));

        //

        //8
        $obj = $obj->addSelect(DB::raw("'' as cantidad_lesionados"));
        for ($i=1; $i <= $cantidad_victimas_lesion; $i++) { 
            $obj = $obj->addSelect(DB::raw("'' as anonimo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as nombre_".$i));
            $obj = $obj->addSelect(DB::raw("'' as edad_".$i));
            $obj = $obj->addSelect(DB::raw("'' as silla_infantil_".$i));
            $obj = $obj->addSelect(DB::raw("'' as sexo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as embarazada_".$i));
            $obj = $obj->addSelect(DB::raw("'' as pre_hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as no_ambulancia_".$i));
            $obj = $obj->addSelect(DB::raw("'' as prestador_servicio_".$i));

            $obj = $obj->addSelect(DB::raw("'' as otro_prestador_".$i));
            $obj = $obj->addSelect(DB::raw("'' as nivel_conciencia_".$i));
            $obj = $obj->addSelect(DB::raw("'' as pulso_".$i));
            $obj = $obj->addSelect(DB::raw("'' as color_piel_".$i));
            $obj = $obj->addSelect(DB::raw("'' as prioridad_traslado_".$i));
            $obj = $obj->addSelect(DB::raw("'' as negativa_traslado_".$i));
            $obj = $obj->addSelect(DB::raw("'' as especifique_negativa_".$i));
            
            $obj = $obj->addSelect(DB::raw("'' as diagnostico_".$i));
            $obj = $obj->addSelect(DB::raw("'' as hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as municipio_hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as clues_".$i));
            $obj = $obj->addSelect(DB::raw("'' as casco_".$i));
            $obj = $obj->addSelect(DB::raw("'' as ubicacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as vehiculo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as placa_pais_".$i));
        }
        //
        //9
        $obj = $obj->addSelect(DB::raw("'' as cantidad_defunciones"));
        for ($i=1; $i <= $cantidad_victimas_defunsion; $i++) { 
            $obj = $obj->addSelect(DB::raw("'' as acta_certificacion_id_".$i));
            $obj = $obj->addSelect(DB::raw("'' as no_acta_certificacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as anonimo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as nombre_".$i));
            $obj = $obj->addSelect(DB::raw("'' as edad_".$i));
            $obj = $obj->addSelect(DB::raw("'' as silla_infantil_".$i));
            $obj = $obj->addSelect(DB::raw("'' as sexo_".$i));
            $obj = $obj->addSelect(DB::raw("'' as embarazada_".$i));
            $obj = $obj->addSelect(DB::raw("'' as pre_hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as no_ambulancia_".$i));
            $obj = $obj->addSelect(DB::raw("'' as prestador_servicio_".$i));
            $obj = $obj->addSelect(DB::raw("'' as otro_prestador_".$i));
            $obj = $obj->addSelect(DB::raw("'' as nivel_conciencia_".$i));
            $obj = $obj->addSelect(DB::raw("'' as pulso_".$i));
            $obj = $obj->addSelect(DB::raw("'' as color_piel_".$i));
            $obj = $obj->addSelect(DB::raw("'' as prioridad_traslado_".$i));
            $obj = $obj->addSelect(DB::raw("'' as negativa_traslado_".$i));
            $obj = $obj->addSelect(DB::raw("'' as especifique_negativa_".$i));
            $obj = $obj->addSelect(DB::raw("'' as diagnostico_".$i));
            $obj = $obj->addSelect(DB::raw("'' as hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as municipio_hospitalizacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as clues_".$i));
            $obj = $obj->addSelect(DB::raw("'' as casco_".$i));
            $obj = $obj->addSelect(DB::raw("'' as ubicacion_".$i));
            $obj = $obj->addSelect(DB::raw("'' as vehiculo_".$i));
            
        }*/
        //
        $obj = $obj->get();
        //Apartado Arreglos
        $arreglo_accidente = ['','Colisión con vehículo automotor',
                                'Atropellamiento',
                                'Colisión con animal',
                                'Colisión con objeto fijo',
                                'Volcadura',
                                'Caída de pasaje',
                                'Salida de camino',
                                'Incendio',
                                'Colisión con ferrocarril',
                                'Colisión con motocicleta',
                                'Colisión con ciclista',
                                'Otro'];      
                               
        $arreglo_uso        = ['PARTICULAR', 'PUBLICO', 'VEHICULO PASAJEROS', 'VEHICULO SEGUN TIPO DE CARGA'];
        $arreglo_tipo_uso   = ['PASAJERO', 'CARGA', 'RURAL MIXTO DE CARGA Y PASAJE', 'ESPECIAL', 
                                'COLECTIVO URBANO', 'COLECTIVO SUBURBANO', 'COLECTIVO INTERMUNICIPAL', 'COLECTIVO FORANEO', 
                                'TAXI','BAJO TONELAJE', 'ALTO TONELAJE', 'PAQUETERIA', 'MATERIALES PARA LA CONSTRUCCION A GRANEL', 'ESPECIALIZADA']; 

        $arreglo_conductor   = ['','Iba a exceso de velocidad', 'No guardó distancia', 'No respetó las señales viales', 'No cedió el paso', 
                                'Utilizaba el teléfono móvil', 'Deslumbramiento', 'Dormitando', 'Rebasó indebidamente', 
                                'No respetó el semáforo','Invadió el carril contrario', 'Viró indebidamente', 'Presumible estado de ebriedad', 'Otro']; 
        
        $arreglo_peaton   = ['','Cruzó la calle inapropiadamente', 'Subía o bajaba del vehículo', 'No respetó el semáforo', 'Presumible estado de ebriedad', 'Empujaba o trabajaba en el vehículo', 'Otro']; 
        $arreglo_falla   = ['','Llantas', 'Ejes', 'Frenos', 'Transmisión', 'Dirección', 'Motor', 'Suspensión', 'Sobrecarga', 'Luces', 'Exceso de dimensiones', 'Otro']; 
        $arreglo_camino   = ['','Mala condición de la vía', 'Falta de señales', 'Objetos en el camino', 'Camino mojado o encharcado (vía mojada)', 'Vía resbalosa /presenta un riesgo de resbalar fácilmente/provoca falta de adherencia del vehículo', 'Obstrucción de la vía por animales', 'Otro']; 
        $arreglo_agente   = ['','Llovizna','Neblina', 'Lluvia', 'Humo', 'Aguacero', 'Tolvanera', 'Nieve', 'Vientos fuertes', 'Granizo', 'Otro']; 
        
        $arreglo_usos = [$arreglo_uso, $arreglo_tipo_uso];   
               
        //$consulta_tipo_accidente =  RelTipoAccidente::whereRaw("lesiones_id in (select id from lesiones where fecha between '".$anio."-01-01' and '".$anio."-12-31')")->select("rel_tipo_accidente_id", "lesiones_id")->get();
        //Fin apartado arreglos                        
        /*foreach ($obj as $key => $value) {
            //Consulta Otros
            $aux_otro = Lesiones::where("id",$value['id'])->first();
            //
            //Tipo de Accidente
            $obj[$key] = $this->ConsultaTipoAccidentes($value['id'], $cantidad_tipo_accidente, $arreglo_accidente, $obj[$key], $aux_otro);
            //Fin tipo accidente
            
            //Inicio Vehiculos
            $obj[$key] = $this->ConsultaVehiculos($value['id'], $cantidad_vehiculos, $arreglo_usos, $obj[$key]);
            //Fin vehiculos
            
            //Inicio Causas
            $obj[$key] = $this->ConsultaConductor($value['id'], $cantidad_causa_conductor, $arreglo_conductor, $obj[$key], $aux_otro);
            $obj[$key] = $this->ConsultaPeaton($value['id'], $cantidad_causa_peaton, $arreglo_peaton, $obj[$key], $aux_otro);
            $obj[$key] = $this->ConsultaPasajero($value['id'], $cantidad_causa_pasajero, $obj[$key]);
            $obj[$key] = $this->ConsultaFalla($value['id'], $cantidad_causa_falla, $arreglo_falla, $obj[$key], $aux_otro);
            $obj[$key] = $this->ConsultaCamino($value['id'], $cantidad_causa_camino, $arreglo_camino, $obj[$key], $aux_otro);
            $obj[$key] = $this->ConsultaAgente($value['id'], $cantidad_causa_natural, $arreglo_agente, $obj[$key], $aux_otro);
            
            //Fin Causas

            //Inicio Victimas
            $obj[$key] = $this->ConsultaVictimaLesion($value['id'], $cantidad_victimas_lesion, [], $obj[$key], []);
            $obj[$key] = $this->ConsultaVictimaDefunsion($value['id'], $cantidad_victimas_defunsion, [], $obj[$key], []);
            
            //Fin Victimas
           
        }*/    
        
        //$columnas = array_keys(collect($obj[0])->toArray());
        //return $columnas;
        //return $obj;
        $filename = 'reporte-general';
        return response()->json(['data'=>$obj, "totales"=> $obj_totales],HttpResponse::HTTP_OK);
        //return (new DevReportExport($obj,$columnas))->download($filename.'.xlsx');
    }

    private function getCantidadTipoAccidente($anio)
    {
        $cantidad = RelTipoAccidente::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    
    private function getVehiculos($anio)
    {
        $cantidad = RelVehiculos::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }

    private function getConductor($anio)
    {
        $cantidad = RelCausaConductor::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    private function getPeaton($anio)
    {
        $cantidad = RelCausaPeaton::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();
        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    private function getPasajero($anio)
    {
        $cantidad = RelCausaPasajero::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    
    private function getCamino($anio)
    {
        $cantidad = RelCondicionCamino::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }

    private function getNatural($anio)
    {
        $cantidad = RelAgente::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    private function getFalla($anio)
    {
        $cantidad = RelFallaVehiculo::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    private function getVictimaLesion($anio)
    {
        $cantidad = RelVictimasLesionados::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->where("tipo_id", 1)
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }
    private function getVictimaDefunsion($anio)
    {
        $cantidad = RelVictimasLesionados::whereRAW("lesiones_id in (SELECT id FROM lesiones WHERE deleted_at IS NULL and fecha between '".$anio."-01-01' and '".$anio."-12-31')")
        ->where("tipo_id", 2)
        ->groupBy("lesiones_id")
        ->orderByRaw("count(*) DESC")
        ->select(DB::RAW("count(*) as cantidad"))
        ->first();

        return (isset($cantidad) == null?0:$cantidad['cantidad']);
    }

    private function ConsultaTipoAccidentes($id, $cantidad, $arreglo, $obj, $otro)
    {
        $aux =  RelTipoAccidente::where("lesiones_id", $id)->select("rel_tipo_accidente_id")->get();
        $bandera_otro = 0;
        $resultado = [];
        $cantidad = count($aux);
        for ($i=1; $i <= $cantidad; $i++) { 
            $obj['tipo_accidente_'.$i] = strtoupper($arreglo[$aux[($i - 1)]['rel_tipo_accidente_id']]);
            if($aux[($i - 1)]['rel_tipo_accidente_id'] == 12)
            {
                $bandera_otro = 1;
            }
        }
        /*for ($i=1; $i <= $cantidad; $i++) { 
            $obj['tipo_accidente_'.$i] = "";
            if(isset($aux[($i - 1)]))
            {
                $obj['tipo_accidente_'.$i] = strtoupper($arreglo[$aux[($i - 1)]['rel_tipo_accidente_id']]);
                if($aux[($i - 1)]['rel_tipo_accidente_id'] == 12)
                {
                    $bandera_otro = 1;
                }
            }
        }
        $obj['otro_tipo_accidente'] = "";*/
        if($bandera_otro == 1)
        {
            if(isset($otro['otro_tipo_accidente']))
            {
                $obj['otro_tipo_accidente'] = strtoupper($otro['otro_tipo_accidente']);
            }   
        }
        return $obj;
    }

    private function ConsultaVehiculos($id, $cantidad, $arreglo, $obj)
    {
        $aux = RelVehiculos::join("catalogo_vehiculos", "catalogo_vehiculos.id", "rel_vehiculos.catalogo_tipo_vehiculo_id")
        ->leftJoin("catalogo_marcas", "catalogo_marcas.id", "rel_vehiculos.marca_id")
        ->leftJoin("catalogo_entidades", "catalogo_entidades.id", "rel_vehiculos.entidad_placas")
        ->where("lesiones_id", $id)
        ->select(DB::RAW("(select count(*) from rel_vehiculos where deleted_at is null and lesiones_id=".$id.") as no_vehiculos"),
        "catalogo_vehiculos.descripcion as tipo_vehiculo",
        "otro_tipo_vehiculo",
        "catalogo_marcas.descripcion as marca",
        "otra_marca",
        "uso_vehiculo", 
        "tipo_uso_vehiculo_id",
        "puesto_disposicion", 
        "placa_pais", 
        "no_ocupantes",
        "color",
        "modelo",
        "con_placas",
        "catalogo_entidades.descripcion as entidad",
        "no_placa")
        ->get();

        $obj['cantidad_vehiculos'] = "--";
        if(count($aux) > 0)
        {
            $obj['cantidad_vehiculos'] = $aux[0]['no_vehiculos'];
        }
        $cantidad = count($aux);
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            
            $obj["tipo_vehiculo_".$i] = (isset($aux[$indice])?$aux[$indice]["tipo_vehiculo"]:"");
            $obj["otro_tipo_vehiculo_".$i] = (isset($aux[$indice])?$aux[$indice]["otro_tipo_vehiculo"]:"");
            $obj["marca_vehiculo_".$i] = (isset($aux[$indice])?$aux[$indice]["marca"]:"");
            $obj["otro_marca_vehiculo_".$i] = (isset($aux[$indice])?$aux[$indice]["otra_marca"]:"");
            
            $uso = "";
            $tipo_uso = "";
            if(isset($aux[$indice]) && intval($aux[$indice]["uso_vehiculo"]) > 0)
            {
                $uso = $arreglo[0][($aux[$indice]["uso_vehiculo"] - 1)];
                if(intval($aux[$indice]["tipo_uso_vehiculo_id"]) > 0)
                {
                    $tipo_uso = $arreglo[1][($aux[$indice]["tipo_uso_vehiculo_id"] - 1)];
                }
            }
            $obj["uso_vehiculo_".$i] = $uso;
            $obj["tipo_uso_vehiculo_".$i] = $tipo_uso;//(isset($aux[$indice])?$aux[$indice]["tipo_uso_vehiculo_id"]:"");
            
            $obj["puesto_disposicion_".$i] = (isset($aux[$indice])?($aux[$indice]["puesto_disposicion"] == 1)?"SI":"NO":"");
            $obj["placa_pais_".$i] = (isset($aux[$indice])?($aux[$indice]["placa_pais"] == 1)?"SI":"NO":"");
            $obj["no_ocupantes_".$i] = (isset($aux[$indice])?$aux[$indice]["no_ocupantes"]:"");
            $obj["color_".$i] = (isset($aux[$indice])?$aux[$indice]["color"]:"");
            $obj["modelo_".$i] = (isset($aux[$indice])?$aux[$indice]["modelo"]:"");
            $obj["con_placas_".$i] = (isset($aux[$indice])?($aux[$indice]["con_placas"] == 1)?"SI":"NO":"");
            $obj["entidad_pais_".$i] = (isset($aux[$indice])?$aux[$indice]["entidad"]:"");
            $obj["no_placa_".$i] = (isset($aux[$indice])?$aux[$indice]["no_placa"]:"");   
        }
        return $obj;
    }
    private function ConsultaConductor($id, $cantidad, $arreglo, $obj, $otro)
    {
        $arreglo1 = ['', 'HOMBRE', 'MUJER', 'SE FUGÓ', 'SE IGNORA'];
        $arreglo2 = ['', 'SI', 'NO', 'SE IGNORA'];
        $bandera = 0;
        $aux = RelCausaConductor::where("lesiones_id", $id)
            ->select("rel_causa_conductor_id")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_conductor'] = "NO";
        }else{
            $obj['causa_conductor'] = "SI";
        }
        $cantidad = count($aux);  
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            $obj["conductor_".$i] = "";
            if(isset($aux[$indice]) != null)
            {
                $obj["conductor_".$i] = strtoupper($arreglo[$aux[$indice]["rel_causa_conductor_id"]]);  
                if($aux[$indice]["rel_causa_conductor_id"] == 13)
                {
                    $bandera = 1;
                }
            }
             
        }
        $obj['otro_causa_conductor'] = "";
        if($bandera == 1)
        {
            if(isset($otro['otro_causa_conductor']))
            {
                $obj['otro_causa_conductor'] = strtoupper($otro['otro_causa_conductor']);
            }      
        }
        $data = RelCausaConductorDetalles::where("lesiones_id", $id)->first();
        
        $obj['conductor_sexo']          = ($data != null?$arreglo1[$data['sexo_id']]:"");
        $obj['conductor_alcoholico']    = ($data == null?"":$arreglo2[$data['alcoholico']]);
        $obj['conductor_cinturon']      = ($data == null?"":$arreglo2[$data['cinturon']]);
        $obj['conductor_edad']          = ($data == null?"":($data['edad']=""?"SE IGNORA": $data['edad']));
        
        return $obj;
    }
    private function ConsultaPeaton($id, $cantidad, $arreglo, $obj, $otro)
    {
        $bandera = 0;
        $aux = RelCausaPeaton::where("lesiones_id", $id)
            ->select("rel_causa_peaton_id")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_peaton'] = "NO";
        }else{
            $obj['causa_peaton'] = "SI";
        }
        $cantidad = count($aux);  
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            $obj["peaton_".$i] = "";
            if(isset($aux[$indice]) != null)
            {
                $obj["peaton_".$i] = (isset($aux[$indice])?strtoupper($arreglo[$aux[$indice]["rel_causa_peaton_id"]]):"");  
                if($aux[$indice]["rel_causa_peaton_id"] == 6)
                {
                    $bandera = 1;
                } 
            }
            
        }
        $obj['otro_causa_peaton'] = "";
        if($bandera == 1)
        {
            if(isset($otro['otro_causa_peaton']))
            {
                $obj['otro_causa_peaton'] = strtoupper($otro['otro_causa_peaton']);
            }      
        }
        return $obj;
    }
    private function ConsultaPasajero($id, $cantidad, $obj)
    {
        $bandera = 0;
        $aux = RelCausaPasajero::where("lesiones_id", $id)
            ->select("causa_pasajero")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_pasajero'] = "NO";
            $obj['causa_pasajero_descripcion'] = "";
        }else{
            $obj['causa_pasajero'] = "SI";
            $obj['causa_pasajero_descripcion'] = strtoupper($aux[0]['causa_pasajero']);
        }
        
        return $obj;
    }
    private function ConsultaFalla($id, $cantidad, $arreglo, $obj, $otro)
    {
        $bandera = 0;
        $aux = RelFallaVehiculo::where("lesiones_id", $id)
            ->select("rel_falla_vehiculo_id")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_falla_vehiculo'] = "NO";
        }else{
            $obj['causa_falla_vehiculo'] = "SI";
        }
        $cantidad = count($aux);  
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            $obj["falla_vehiculo_".$i] = "";
            if(isset($aux[$indice]))
            {
                //echo $indice." - ".$aux[$indice];
                //print_r($arreglo);
                $obj["falla_vehiculo_".$i] = (isset($aux[$indice])?strtoupper($arreglo[($aux[$indice]["rel_falla_vehiculo_id"])]):"");  
                if($aux[$indice]["rel_falla_vehiculo_id"] == 11)
                {
                    $bandera = 1;
                } 
            }
            
        }
        $obj['otro_causa_falla_vehiculo'] = "";
        if($bandera == 1)
        {
            if(isset($otro['otro_falla_accidente']))
            {
                $obj['otro_causa_falla_vehiculo'] = strtoupper($otro['otro_falla_accidente']);
            }      
        }
        return $obj;
    }
    private function ConsultaCamino($id, $cantidad, $arreglo, $obj, $otro)
    {
        $bandera = 0;
        $aux = RelCondicionCamino::where("lesiones_id", $id)
            ->select("rel_condicion_camino_id")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_condicion_camino'] = "NO";
        }else{
            $obj['causa_condicion_camino'] = "SI";
        }
        $cantidad = count($aux);  
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            $obj["condicion_camino_".$i] = "";
            if(isset($aux[$indice]) != null)
            {
                $obj["condicion_camino_".$i] = (isset($aux[$indice])?strtoupper($arreglo[$aux[$indice]["rel_condicion_camino_id"]]):"");  
                if($aux[$indice]["rel_condicion_camino_id"] == 11)
                {
                    $bandera = 1;
                } 
            }
            
        }
        $obj['otro_causa_condicion_camino'] ="";
        if($bandera == 1)
        {
            if(isset($otro['otro_condicion']))
            {
                $obj['otro_causa_condicion_camino'] = strtoupper($otro['otro_condicion']);
            }      
        }
        return $obj;
    }
    private function ConsultaAgente($id, $cantidad, $arreglo, $obj, $otro)
    {
        $bandera = 0;
        $aux = RelAgente::where("lesiones_id", $id)
            ->select("rel_agente_natural_id")
            ->get();

        if(count($aux) == 0)
        {
            $obj['causa_agente_natural'] = "NO";
        }else{
            $obj['causa_agente_natural'] = "SI";
        }
        $cantidad = count($aux);  
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
            $obj["agente_natural_".$i] = "";
            if(isset($aux[$indice]) != null)
            {
                $obj["agente_natural_".$i] = (isset($aux[$indice])?strtoupper($arreglo[$aux[$indice]["rel_agente_natural_id"]]):"");  
                if($aux[$indice]["rel_agente_natural_id"] == 10)
                {
                    $bandera = 1;
                } 
            }
            
        }
        $obj['otro_agente_natural'] = "";
        if($bandera == 1)
        {
            if(isset($otro['otro_agente_camino']))
            {
                $obj['otro_agente_natural'] = strtoupper($otro['otro_agente_camino']);
            }      
        }
        return $obj;
    }

    private function ConsultaVictimaLesion($id, $cantidad, $arreglo, $obj)
    {
        $arreglo_prestador = [
            '', 'SSA', 'CRUZ ROJA', 'PROTECCIÓN CIVIL', 'SEDENA', 'IMSS','ISSSTE', 'ISSSTECH', 'ERUM', 'BOMBEROS', 'OTROS'
        ];

        $arreglo_nivel = [
            '', 'CONCIENTE', 'RESPUESTA A ESTÍMULOS VERBALES', 'RESPUESTA A ESTÍMULO DOLOROSO', 'INCONCIENTE'
        ];
        $arreglo_prioridad = [
            '', 'ROJO', 'AMARILLO', 'VERDE', 'NEGRO'
        ];
        $aux = RelVictimasLesionados::select(
        "anonimo",
        DB::RAW("CONCAT(nombre, apellido_paterno, apellido_materno) AS nombre"),
        "edad",
        "silla_id",
        "sexo_id",
        "embarazada",
        "pre_hospitalizacion",
        "no_ambulancia",
        "prestador_servicio",
        "otro_prestador",
        "nivel_conciencia",
        "pulso",
        "color_piel",
        "prioridad_traslado",
        "negativa_traslado",
        "especifique_negativa",
        "diagnostico",
        "hospitalizacion",
        DB::RAW("(SELECT descripcion FROM catalogo_municipios WHERE id=rel_victimas_lesionados.`municipio_hospitalizacion`) AS municipio_hospitalizacion"),
        DB::RAW("(SELECT descripcion FROM catalogo_clues WHERE clues=rel_victimas_lesionados.`clues`) AS clues"),
        "casco",
        "ubicacion",
        DB::RAW("(SELECT CONCAT(b.`descripcion`,' ',IF(a.`otro_tipo_vehiculo` !='',CONCAT('( ',a.`otro_tipo_vehiculo`,') '),''), ' ',c.`descripcion`,' ', IF(a.`otra_marca`!='', CONCAT('( ',a.`otra_marca`,' ) '),'') ) AS auto FROM rel_vehiculos a 
        INNER JOIN catalogo_vehiculos b ON a.`catalogo_tipo_vehiculo_id`=b.id 
        INNER JOIN catalogo_marcas c ON a.`marca_id`=c.`id` where rel_victimas_lesionados.`vehiculo_id`=a.id) as auto"))
        ->where("lesiones_id", $id)
        ->where("tipo_id", 1)
        ->get();
        
        $obj['cantidad_lesionados'] = "--";
        if(count($aux) > 0)
        {
            $obj['cantidad_lesionados'] = count($aux);
        }

        $cantidad = count($aux);
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
           
            $obj["anonimo_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["anonimo"]==1?"SI":"NO");
            
            $obj["nombre_".$i]          = !isset($aux[$indice])?"":strtoupper($aux[$indice]["nombre"]);
            $obj["edad_".$i]          = !isset($aux[$indice])?"":$aux[$indice]["edad"];
            $obj["silla_infantil_".$i]  = !isset($aux[$indice])?"":($aux[$indice]["silla_id"]==1?"SI":"NO");
            $obj["sexo_".$i]  = !isset($aux[$indice])?"":($aux[$indice]["sexo_id"]==1?"MASCULINO":"FEMENINO");
            $obj["embarazada_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["embarazada"]==1?"SI":"NO");
            
            $obj["pre_hospitalizacion_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["pre_hospitalizacion"]==1?"SI":"NO");
            $obj["no_ambulancia_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["no_ambulancia"]);
            
            $obj["prestador_servicio_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["prestador_servicio"]!=null?$arreglo_prestador[$aux[$indice]["prestador_servicio"]]:'');
            $obj["otro_prestador_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["otro_prestador"]);
            $obj["nivel_conciencia_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["nivel_conciencia"]!=null?$arreglo_nivel[$aux[$indice]["nivel_conciencia"]]:'');
            
            $obj["pulso_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["pulso"]==1?"SI":"NO");
            
            
            $obj["color_piel_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["color_piel"]);
            $obj["prioridad_traslado_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["prioridad_traslado"]!=null?$arreglo_prioridad[$aux[$indice]["prioridad_traslado"]]:'');
           
            $obj["negativa_traslado_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["negativa_traslado"]==1?"SI":"NO");
            $obj["especifique_negativa_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["especifique_negativa"]);
            $obj["diagnostico_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["diagnostico"]);
            
            $obj["hospitalizacion_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["hospitalizacion"]==1?"SI":"NO");
            
            $obj["municipio_hospitalizacion_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["municipio_hospitalizacion"];
            $obj["clues_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["clues"];
            
            $obj["casco_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["casco"]==1?"SI":"NO");
            $obj["ubicacion_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["ubicacion"];
            $obj["vehiculo_".$i]         = !isset($aux[$indice])?"":strtoupper($aux[$indice]["auto"]);
            $obj["placa_pais_".$i] = (isset($aux[$indice])?($aux[$indice]["placa_pais"] == 1)?"SI":"NO":"");
               
        }
        
        return $obj;
    }
    private function ConsultaVictimaDefunsion($id, $cantidad, $arreglo, $obj)
    {
        $arreglo_prestador = [
            '', 'SSA', 'CRUZ ROJA','PROTECCIÓN CIVIL', 'SEDENA', 'IMSS','ISSSTE', 'ISSSTECH', 'ERUM', 'BOMBEROS', 'OTROS'
        ];

        $arreglo_nivel = [
            '', 'CONCIENTE', 'RESPUESTA A ESTÍMULOS VERBALES', 'RESPUESTA A ESTÍMULO DOLOROSO', 'INCONCIENTE'
        ];
        $arreglo_prioridad = [
            '', 'ROJO', 'AMARILLO', 'VERDE', 'NEGRO'
        ];
        $aux = RelVictimasLesionados::select(
        "acta_certificacion_id",
        "no_acta_certificacion",
        "anonimo",
        DB::RAW("CONCAT(nombre, apellido_paterno, apellido_materno) AS nombre"),
        "edad",
        "silla_id",
        "sexo_id",
        "embarazada",
        "pre_hospitalizacion",
        "no_ambulancia",
        "prestador_servicio",
        "otro_prestador",
        "nivel_conciencia",
        "pulso",
        "color_piel",
        "prioridad_traslado",
        "negativa_traslado",
        "especifique_negativa",
        "diagnostico",
        "hospitalizacion",
        DB::RAW("(SELECT descripcion FROM catalogo_municipios WHERE id=rel_victimas_lesionados.`municipio_hospitalizacion`) AS municipio_hospitalizacion"),
        DB::RAW("(SELECT descripcion FROM catalogo_clues WHERE clues=rel_victimas_lesionados.`clues`) AS clues"),
        "casco",
        "ubicacion",
        DB::RAW("(SELECT CONCAT(b.`descripcion`,' ',IF(a.`otro_tipo_vehiculo` !='',CONCAT('( ',a.`otro_tipo_vehiculo`,') '),''), ' ',c.`descripcion`,' ', IF(a.`otra_marca`!='', CONCAT('( ',a.`otra_marca`,' ) '),'') ) AS auto FROM rel_vehiculos a 
        INNER JOIN catalogo_vehiculos b ON a.`catalogo_tipo_vehiculo_id`=b.id 
        INNER JOIN catalogo_marcas c ON a.`marca_id`=c.`id` where rel_victimas_lesionados.`vehiculo_id`=a.id) as auto"))
        ->where("lesiones_id", $id)
        ->where("tipo_id", 2)
        ->get();
        
        $obj['cantidad_defunciones'] = "--";
        if(count($aux) > 0)
        {
            $obj['cantidad_defunciones'] = count($aux);
        }

        $cantidad = count($aux);
        for ($i=1; $i <= $cantidad; $i++) { 
            
            $indice = ($i - 1);
           
            $obj["acta_certificacion_id_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["acta_certificacion_id"]==1?"ACTA":"CERTIFICADO");
            $obj["no_acta_certificacion_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["no_acta_certificacion"];
            $obj["anonimo_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["anonimo"]==1?"SI":"NO");
            
            $obj["nombre_".$i]          = !isset($aux[$indice])?"":$aux[$indice]["nombre"];
            $obj["edad_".$i]          = !isset($aux[$indice])?"":$aux[$indice]["edad"];
            $obj["silla_infantil_".$i]  = !isset($aux[$indice])?"":($aux[$indice]["silla_id"]==1?"SI":"NO");
            $obj["sexo_".$i]  = !isset($aux[$indice])?"":($aux[$indice]["sexo_id"]==1?"MASCULINO":"FEMENINO");
            $obj["embarazada_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["embarazada"]==1?"SI":"NO");
            
            $obj["pre_hospitalizacion_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["pre_hospitalizacion"]==1?"SI":"NO");
            $obj["no_ambulancia_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["no_ambulancia"];
            $obj["prestador_servicio_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["prestador_servicio"]!=null?$arreglo_prestador[$aux[$indice]["prestador_servicio"]]:'');
            $obj["otro_prestador_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["otro_prestador"];
            $obj["nivel_conciencia_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["nivel_conciencia"]!=null?$arreglo_nivel[$aux[$indice]["nivel_conciencia"]]:'');
            
            $obj["pulso_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["pulso"]==1?"SI":"NO");
            
            
            $obj["color_piel_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["color_piel"];
            $obj["prioridad_traslado_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["prioridad_traslado"]!=null?$arreglo_prioridad[$aux[$indice]["prioridad_traslado"]]:'');
           
            $obj["negativa_traslado_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["negativa_traslado"]==1?"SI":"NO");
            $obj["especifique_negativa_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["especifique_negativa"];
            $obj["diagnostico_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["diagnostico"];
            
            $obj["hospitalizacion_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["hospitalizacion"]==1?"SI":"NO");
            
            $obj["municipio_hospitalizacion_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["municipio_hospitalizacion"];
            $obj["clues_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["clues"];
            
            $obj["casco_".$i]         = !isset($aux[$indice])?"":($aux[$indice]["casco"]==1?"SI":"NO");
            $obj["ubicacion_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["ubicacion"];
            $obj["vehiculo_".$i]         = !isset($aux[$indice])?"":$aux[$indice]["auto"];
            //$obj["placa_pais_".$i] = (isset($aux[$indice])?($aux[$indice]["placa_pais"] == 1)?"SI":"NO":"");
               
        }
        
        return $obj;
    }

    private function getUserAccessData($loggedUser = null){
        if(!$loggedUser){
            $loggedUser = auth()->userOrFail();
        }
        $loggedUser->load('municipios');
        $lista_municipio = [];
        foreach ($loggedUser->municipios as $municipio) {
            $lista_municipio[] = intval($municipio['id']);
            //echo $municipio['id']."-";
        }
    
        $loggedUser->listaMunicipios = $lista_municipio;
        return $loggedUser;
    }
}
