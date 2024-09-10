import { Component, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../shared/shared.service';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTable } from '@angular/material/table';
import { MatExpansionPanel } from '@angular/material/expansion';
import { MatDialog } from '@angular/material/dialog';
import { FormBuilder, FormControl } from '@angular/forms';
import { trigger, transition, animate, style } from '@angular/animations';

import { ConfirmActionDialogComponent } from '../../utils/confirm-action-dialog/confirm-action-dialog.component';
import { LesionesService } from '../lesiones.service';

import { STEPPER_GLOBAL_OPTIONS } from '@angular/cdk/stepper';

import { AuthService } from '../../auth/auth.service';

import * as FileSaver from 'file-saver';
import * as XLSX from 'xlsx';

const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
const EXCEL_EXTENSION = '.xlsx';

export class Person {
  id: number;
  name: String;
  surname: String;
  age: number;
}


export const PERSONS: Person[] = [
  {
      id: 1,
      name: 'Very long name which needs to be wrapped',
      surname: 'Novicky',
      age: 21
  },
  {
      id: 2,
      name: 'Another long name that won\'t be wrapped',
      surname: 'Tracz',
      age: 12
  },
  {
      id: 3,
      name: 'Steve',
      surname: 'Laski',
      age: 38
  }
];

@Component({
  selector: 'app-lista-lesiones',
  templateUrl: './lista-lesiones.component.html',
  styleUrls: ['./lista-lesiones.component.css'],
  animations: [
    trigger('buttonInOut', [
        transition('void => *', [
            style({opacity: '1'}),
            animate(200)
        ]),
        transition('* => void', [
            animate(200, style({opacity: '0'}))
        ])
    ])
  ],
  providers:[
    { provide: STEPPER_GLOBAL_OPTIONS, useValue: { displayDefaultIndicatorType: false, showError: true } }
  ]
})
export class ListaLesionesComponent implements OnInit {

  permisoGuardar:boolean = false;
  permisoImprimir:boolean = false;
  permisoGuardarLesiones:boolean = false;
  permisoEliminarLesion:boolean = false;
  isLoading: boolean = false;
  isLoadingPDF: boolean = false;
  isLoadingPDFArea: boolean = false;
  isLoadingAgent: boolean = false;
  loadReporteExcel:boolean = false;
  mediaSize: string;

  years:number[] = [];
  showMyStepper:boolean = false;
  showReportForm:boolean = false;
  stepperConfig:any = {};
  reportTitle:string;
  reportIncludeSigns:boolean = false;
 
  searchQuery: string = '';

  pageEvent: PageEvent;
  resultsLength: number = 0;
  currentPage: number = 0;
  pageSize: number = 20;
  selectedItemIndex: number = -1;

  displayedColumns: string[] = ['#','fecha' ,'municipio', 'direccion',  'opciones'];
  dataSource: any = [];
  dataSourceFilters: any = [];

  isLoadingEstadosActuales:boolean = false;
  estadosActuales:any[] = [];

  filterChips:any = []; //{id:'field_name',tag:'description',tooltip:'long_description'}
  
  filterCatalogs:any = {};
  filteredCatalogs:any = {};
  catalogos: any = {};

  filterForm = this.fb.group({

    'catalogo_municipio_id'                  : [undefined],

  });

  fechaActual:any = '';
  maxDate:Date;
  minDate:Date;


  constructor(
    private sharedService: SharedService,
    private lesionesService: LesionesService,
    private authService: AuthService,
    private fb: FormBuilder,
    public dialog: MatDialog) { }

    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatTable) usersTable: MatTable<any>;
    @ViewChild(MatExpansionPanel) advancedFilter: MatExpansionPanel;

  ngOnInit() {
    this.loadData();
    this.getAnios();
    this.loadPermisos();
    this.loadCatalogos();
  }

  loadCatalogos()
  {
    this.isLoading = true;

    let carga_catalogos = {'Estados':0, 'Municipio':0, 'TipoVehiculo':0, 'Vehiculo':0};
    this.lesionesService.getCatalogos(carga_catalogos).subscribe(
      response => {
        this.catalogos = response.data;
        this.isLoading = false; 
      } 
    );
  }

  getDisplayFn(label: string){
    return (val) => this.displayFn(val,label);
  }

  displayFn(value: any, valueLabel: string){
    return value ? value[valueLabel] : value;
  }

  applyFilter(){
    this.selectedItemIndex = -1;
    this.paginator.pageIndex = 0;
    this.paginator.pageSize = this.pageSize;
    this.loadData();  
  }

  cleanFilter(filter){
    filter.value = '';
    //filter.closePanel();
  }

  cleanSearch(){
    this.searchQuery = '';
    //this.paginator.pageIndex = 0;
    //this.loadEmpleadosData(null);
  }

  toggleAdvancedFilter(status){

    if(status){
      this.advancedFilter.open();
    }else{
      this.advancedFilter.close();
    }

  }

  public loadFilterCatalogs(){

    this.isLoading = true;
    let carga_catalogos = [
      {nombre:'seguros',orden:'descripcion'},
      {nombre:'estados',orden:'nombre'},
    ];

    this.lesionesService.getCatalogos(carga_catalogos).subscribe(
      response => {

        this.catalogos = response.data;

      },
      errorResponse =>{
        var errorMessage = "Ocurrió un error.";
        if(errorResponse.status == 409){
          errorMessage = errorResponse.error.message;
        }
        this.sharedService.showSnackBar(errorMessage, null, 3000);
      }
    );
  }

  public loadPermisos()
  {
    this.lesionesService.getPermisos().subscribe(
      response => {
        response.data.forEach(element => {
         // console.log(element);
         if(element == "permisoGuardarIncidente" || element == "permisoAdmin")
         {
          this.permisoGuardar = true;
         }
         
         if(element == "permisoImprimir" || element == "permisoAdmin")
         {
          this.permisoImprimir = true;
         }
         if(element == "permisoImprimir" || element == "permisoAdmin")
         {
          this.permisoImprimir = true;
         }
         if(element == "permisoEliminarLesion" || element == "permisoAdmin")
         {
          this.permisoEliminarLesion = true;
         }
         //console.log(this.permisoGuardar);
        });
      },
      errorResponse =>{
        var errorMessage = "Ocurrió un error.";
        if(errorResponse.status == 409){
          errorMessage = errorResponse.error.message;
        }
        this.sharedService.showSnackBar(errorMessage, null, 3000);
      }
    );
  }

  public getAnios()
  {
    
    this.lesionesService.getAnios().subscribe(
      response => {
        console.log(response);
        let min = response.min_fecha;
        let max = response.max_fecha;
        for (let index = min; index <= max; index++) {
          this.years.push(index);
        }
      },
      errorResponse =>{
        var errorMessage = "Ocurrió un error.";
        if(errorResponse.status == 409){
          errorMessage = errorResponse.error.message;
        }
        this.sharedService.showSnackBar(errorMessage, null, 3000);
      }
    );
  }

  private _filter(value: any, catalog: string, valueField: string): string[] {
    if(this.catalogos[catalog]){
      let filterValue = '';
      if(value){
        if(typeof(value) == 'object'){
          filterValue = value[valueField].toLowerCase();
        }else{
          filterValue = value.toLowerCase();
        }
      }
      return this.catalogos[catalog].filter(option => option[valueField].toLowerCase().includes(filterValue));
    }
  }

  public loadData(event?:PageEvent){
    this.isLoading = true;
    let params:any;
    if(!event){
      params = { page: 1, per_page: this.pageSize }
    }else{
      params = {
        page: event.pageIndex+1,
        per_page: event.pageSize
      };
    }

    if(event && !event.hasOwnProperty('selectedIndex')){
      this.selectedItemIndex = -1;
    }
    
    params.query = this.searchQuery;

    let filterFormValues = this.filterForm.value;
    let countFilter = 0;

    for(let i in filterFormValues){
      if(filterFormValues[i]){
        if(i == 'catalogo_municipio_id'){
          params[i] = filterFormValues[i];
        }
        countFilter++;
      }
    }

    if(countFilter > 0){
      params.active_filter = true;
    }

    let dummyPaginator;
    if(event){
      this.sharedService.setDataToCurrentApp('paginator',event);
    }else{
      dummyPaginator = {
        length: 0,
        pageIndex: (this.paginator)?this.paginator.pageIndex:this.currentPage,
        pageSize: (this.paginator)?this.paginator.pageSize:this.pageSize,
        previousPageIndex: (this.paginator)?this.paginator.previousPage:((this.currentPage > 0)?this.currentPage-1:0)
      };
    }

    this.sharedService.setDataToCurrentApp('searchQuery',this.searchQuery);
    this.sharedService.setDataToCurrentApp('filter',filterFormValues);
    
    this.lesionesService.getLesionesList(params).subscribe(
      response => {
        console.log(response);
        this.dataSource = [];
        this.resultsLength = 0;
        if(response.data.total > 0){
          this.dataSource = response.data.data;
          this.resultsLength = response.data.total;
        }
        this.isLoading = false; 
      },
      errorResponse =>{
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
        this.isLoading = false;
      } 
    );
    return event;
  }

  reporte(anio)
  {
    let params = { export_excel : true, anio_reporte:anio };
    this.loadReporteExcel = true;
    this.lesionesService.getLesionesList(params).subscribe(
      response => {
        this.loadReporteExcel = false;
        //console.log("Hola",response);
        //FileSaver.saveAs(response,'reporte_general');
        //this.exportAsExcelFile(response.data, "prueba");
        this.cargarJson(response.data, response.totales);
      },
      errorResponse =>{
        this.loadReporteExcel = false;
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
        this.isLoading = false;
      } 
    );

  }
  verIncidente(indice)
  {
    
  }
  eliminarIncidente(indice)
  {
    const dialogRef = this.dialog.open(ConfirmActionDialogComponent, {
      width: '500px',
      data:{dialogTitle:'ELIMINAR',dialogMessage:'¿Realmente desea eliminar este registro? Escriba ACEPTAR a continuación para realizar el proceso.',validationString:'ACEPTAR',btnColor:'primary',btnText:'Aceptar'}
    });

    dialogRef.afterClosed().subscribe(valid => {
      if(valid){
        this.lesionesService.deleteIncidente(indice).subscribe(
          response => {
            this.loadData();
            this.sharedService.showSnackBar("SE HA ELIMINADO CORRECTAMENTE EL REGISTRO", null, 3000);
          },
          errorResponse =>{
            this.loadReporteExcel = false;
            let objError = errorResponse.error.error.data;
            let claves = Object.keys(objError); 
            this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
            this.isLoading = false;
          } 
        );
      }
    });

  }

  cargarJson(obj, obj_totales)
  {

    /*Area de catalogos */
    let arreglo_accidente = ['','Colisión con vehículo automotor',
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

    let arreglo_peaton   = ['','Cruzó la calle inapropiadamente', 'Subía o bajaba del vehículo', 'No respetó el semáforo', 'Presumible estado de ebriedad', 'Empujaba o trabajaba en el vehículo', 'Otro'];                             
    let arreglo_falla   = ['','Llantas', 'Ejes', 'Frenos', 'Transmisión', 'Dirección', 'Motor', 'Suspensión', 'Sobrecarga', 'Luces', 'Exceso de dimensiones', 'Otro']; 
    let arreglo_camino   = ['','Mala condición de la vía', 'Falta de señales', 'Objetos en el camino', 'Camino mojado o encharcado (vía mojada)', 'Vía resbalosa /presenta un riesgo de resbalar fácilmente/provoca falta de adherencia del vehículo', 'Obstrucción de la vía por animales', 'Otro']; 
    let arreglo_agente   = ['','Llovizna','Neblina', 'Lluvia', 'Humo', 'Aguacero', 'Tolvanera', 'Nieve', 'Vientos fuertes', 'Granizo', 'Otro']; 
    let arreglo_uso        = ['PARTICULAR', 'PUBLICO', 'VEHICULO PASAJEROS', 'VEHICULO SEGUN TIPO DE CARGA'];
    let arreglo_tipo_uso   = ['PASAJERO', 'CARGA', 'RURAL MIXTO DE CARGA Y PASAJE', 'ESPECIAL', 
                                'COLECTIVO URBANO', 'COLECTIVO SUBURBANO', 'COLECTIVO INTERMUNICIPAL', 'COLECTIVO FORANEO', 
                                'TAXI','BAJO TONELAJE', 'ALTO TONELAJE', 'PAQUETERIA', 'MATERIALES PARA LA CONSTRUCCION A GRANEL', 'ESPECIALIZADA'];  

    let arreglo_conductor   = ['','Iba a exceso de velocidad', 'No guardó distancia', 'No respetó las señales viales', 'No cedió el paso', 
                                'Utilizaba el teléfono móvil', 'Deslumbramiento', 'Dormitando', 'Rebasó indebidamente', 
                                'No respetó el semáforo','Invadió el carril contrario', 'Viró indebidamente', 'Presumible estado de ebriedad', 'Otro']; 
    
    let arreglo_usos = [arreglo_uso, arreglo_tipo_uso];                                      
    /* */
    let JsonResponse = [];
    obj.forEach(element => {
      let json = {
        id: element.id,
        folio: 'CHIS-'+element.id,
        fecha_incidente: element.fecha,
        hora_incidente: element.hora,
        fecha_hora_captura: element.created_at.slice(0,19).replace("T"," "),
        entidad: element.entidad.descripcion,
        municipio: element.municipio.descripcion,
        localidad: element.localidad_id,
        colonia: element.colonia,
        calle: element.calle,
        numero: element.numero,
        cp: element.cp,
        latitud: element.latitud,
        longitud: element.longitud,
        zona: (element.zona_id = 1)? 'ZONA URBANA': 'ZONA SUBURBANA',
        carretera_estatal: (element.estatal_id = 1)? 'SI': 'NO',
        interseccion: (element.estatal_id = 1)? 'SI': 'NO',
        entre_calle: element.calle1,
        y_calle: element.calle2,
        punto_referencia: element.punto_referencia,
        via: (element.via_id = 1)? 'PAVIMENTADA':'NO PAVIMENTADA',
        tipo_via: (element.via_id = 1)? (element.tipo_pavimentado = 1)?'ASFALTO': 'CONCRETO': (element.tipo_via_id = 1)?'TERRACERÍA':(element.tipo_via_id = 2)?'EMPEDRADO':'OTRO',
        otro_tipo_via: element.otro_tipo_via,
        tipo_camino: (element.tipo_camino = null)?'':(element.tipo_camino = 1)?'CAMINO RURAL': (element.tipo_camino = 2)?'CARRETERA ESTATAL':'OTRO',
        otro_tipo_camino:element.otro_tipo_camino
      }

      json = this.addJson(element, json,{nombre:'tipo_accidente', valor:'rel_tipo_accidente_id' }, obj_totales.cantidad_tipo_accidente, arreglo_accidente, 'tipo_accidente_', 
      {nombre_otro:'otro_tipo_accidente', valor_otro:'otro_tipo_accidente'}, 
      {indicador : 0});//Tipo accidente
      //ConsultaVehiculos
      json = this.addJsonVehiculos(element, json, obj_totales.cantidad_vehiculos, arreglo_usos);
      //ConsultaConductor
      json = this.addJsonConductor(element, json, obj_totales.cantidad_causa_conductor, arreglo_conductor);
      
      json = this.addJson(element, json, {nombre:'causa_peaton', valor:'rel_causa_peaton_id' }, obj_totales.cantidad_causa_peaton, arreglo_peaton, 'peaton_', 
      {nombre_otro:'otro_causa_peaton', valor_otro:'otro_causa_peaton'}, 
      {indicador:1, nombre_indicador:'causa_peaton', 'valor_indicador': (element.causa_peaton.length > 0)?'SI':'NO'});//peaton
      
      //pasajeros
      json['causa_pasajero'] = (obj_totales.cantidad_causa_pasajero > 0)?'SI':'NO';
      json['causa_pasajero_descripcion'] = (obj_totales.cantidad_causa_pasajero > 0)?element.causa_pasajero:'';
      //fin pasajeros
      json = this.addJson(element, json, {nombre:'falla_vehiculo', valor:'rel_falla_vehiculo_id' }, obj_totales.cantidad_causa_falla, arreglo_falla, 'falla_vehiculo_',
      {nombre_otro:'otro_causa_falla_vehiculo', valor_otro:'otro_falla_accidente'}, 
      {indicador:1, nombre_indicador:'causa_falla_vehiculo', 'valor_indicador': (element.falla_vehiculo.length > 0)?'SI':'NO'});//causa falla

      json = this.addJson(element, json, {nombre:'condicion_camino', valor:'rel_condicion_camino_id' }, obj_totales.cantidad_causa_camino, arreglo_camino, 'condicion_camino_',
      {nombre_otro:'otro_causa_condicion_camino', valor_otro:'otro_condicion'}, 
      {indicador:1, nombre_indicador:'causa_condicion_camino', 'valor_indicador': (element.condicion_camino.length > 0)?'SI':'NO'});//causa camino

      json = this.addJson(element, json, {nombre:'agentes', valor:'rel_agente_natural_id' }, obj_totales.cantidad_causa_natural, arreglo_agente, 'agente_natural_',
      {nombre_otro:'otro_agente_natural', valor_otro:'otro_agente_camino'}, 
      {indicador:1, nombre_indicador:'causa_agente_natural', 'valor_indicador': (element.agentes.length > 0)?'SI':'NO'});//causa agentes

      //ConsultaVictimaLesion
      json = this.addJsonvictima({ tipo:1, data:element.victima_no_fatal}, json, obj_totales.cantidad_victimas_lesion);
      json = this.addJsonvictima({tipo: 2, data:element.defuncion}, json, obj_totales.cantidad_victimas_defunsion);

      JsonResponse.push(json);
    });
    this.exportAsExcelFile(JsonResponse, "OEL");
  }

  public addJson(info, json, campos_base, total, catalogo, campo, campo_otro, config)
  {
    if(config.indicador == 1)
    {
      json[config.nombre_indicador] = config.valor_indicador;
    }

    for (let index = 0; index < total; index++) {
      if(info[campos_base.nombre][index])
      {
        json[campo+(index + 1)] = catalogo[info[campos_base.nombre][index][campos_base.valor]];
      }else{
        json[campo+(index + 1)] = '';
      }
    }
    json[campo_otro.nombre_otro] = info[campo_otro.valor_otro];
    return json;
  }

  public addJsonVehiculos(obj, json, cantidad_total, catalogos)
  {
    let vehiculos = obj.vehiculo;
    json['cantidad_vehiculos'] = vehiculos.length;
    
    for (let index = 0; index < cantidad_total; index++) {
        let indice = index + 1;  
        json["tipo_vehiculo_"+indice] = (vehiculos[index])?(vehiculos[index].tipo)?vehiculos[index].tipo.descripcion:"":"";
        json["otro_tipo_vehiculo_"+indice] = (vehiculos[index])?vehiculos[index]["otro_tipo_vehiculo"]:"";
        json["marca_vehiculo_"+indice] = (vehiculos[index])?(vehiculos[index].marca)?vehiculos[index].marca.descripcion:"":"";
        json["otro_marca_vehiculo_"+indice] = (vehiculos[index])?vehiculos[index]["otra_marca"]:"";
        
        let uso = "";
        let tipo_uso = "";
        if((vehiculos[index]) && (vehiculos[index]["uso_vehiculo"]) > 0)
        {
            uso = catalogos[0][(vehiculos[index]["uso_vehiculo"] - 1)];
            if(parseInt(vehiculos[index]["tipo_uso_vehiculo_id"]) > 0)
            {
                tipo_uso = catalogos[1][(vehiculos[index]["tipo_uso_vehiculo_id"] - 1)];
            }
        }
        json["uso_vehiculo_"+indice] = uso;
        json["tipo_uso_vehiculo_"+indice] = tipo_uso;//(vehiculos[index])?vehiculos[index]["tipo_uso_vehiculo_id"]:"";
        
        json["puesto_disposicion_"+indice] = (vehiculos[index])?(vehiculos[index]["puesto_disposicion"] == 1)?"SI":"NO":"";
        json["placa_pais_"+indice] = (vehiculos[index])?(vehiculos[index]["placa_pais"] == 1)?"SI":"NO":"";
        json["no_ocupantes_"+indice] = (vehiculos[index])?vehiculos[index]["no_ocupantes"]:"";
        json["color_"+indice] = (vehiculos[index])?vehiculos[index]["color"]:"";
        json["modelo_"+indice] = (vehiculos[index])?vehiculos[index]["modelo"]:"";
        json["con_placas_"+indice] = (vehiculos[index])?(vehiculos[index]["con_placas"] == 1)?"SI":"NO":"";
        json["entidad_pais_"+indice] = (vehiculos[index])?vehiculos[index]["entidad"]:"";
        json["no_placa_"+indice] = (vehiculos[index])?vehiculos[index]["no_placa"]:"";   
      
    }
    return json;
  }

  public addJsonConductor(obj, json, cantidad_total, catalogos)
  {
    let arreglo1 = ['', 'HOMBRE', 'MUJER', 'SE FUGÓ', 'SE IGNORA'];
    let arreglo2 = ['', 'SI', 'NO', 'SE IGNORA'];

    json = this.addJson(obj, json, {nombre:'causa_conductor', valor:'rel_causa_conductor_id' }, cantidad_total, catalogos, 'conductor_',
      {nombre_otro:'otro_causa_conductor', valor_otro:'otro_causa_conductor'}, 
      {indicador:1, nombre_indicador:'causa_conductor', 'valor_indicador': (obj.causa_conductor.length > 0)?'SI':'NO'});

      let detalle = obj.causa_conductor_detalle;
      json['conductor_sexo']          = (detalle?arreglo1[detalle['sexo_id']]:"");
      json['conductor_alcoholico']    = (detalle?arreglo2[detalle['alcoholico']]:"");
      json['conductor_cinturon']      = (detalle?arreglo2[detalle['cinturon']]:"");
      json['conductor_edad']          = (detalle?detalle['edad']:'SE IGNORA' );//(detalle['edad']="")?"SE IGNORA": detalle['edad']:"SE IGNORA");
    return json;
  }

  public addJsonvictima(obj, json, cantidad_total)
  {
    let arreglo_prestador = ['', 'SSA', 'CRUZ ROJA', 'PROTECCIÓN CIVIL', 'SEDENA', 'IMSS','ISSSTE', 'ISSSTECH', 'ERUM', 'BOMBEROS', 'OTROS' ];
    let arreglo_nivel = ['', 'CONCIENTE', 'RESPUESTA A ESTÍMULOS VERBALES', 'RESPUESTA A ESTÍMULO DOLOROSO', 'INCONCIENTE'];
    let arreglo_prioridad = ['', 'ROJO', 'AMARILLO', 'VERDE', 'NEGRO'];
    let informacion = obj.data;
    let name = "";
    for (let index = 0; index < cantidad_total; index++) {
      let indice = index + 1;
      if(obj.tipo == 1)
      {
        json['cantidad_lesionados'] = informacion.length;
        name = "lesion";
      }else{
        json['cantidad_defunciones'] = informacion.length;
        json["acta_certificacion_id_"+indice]         = (!informacion[index])?"":(informacion[index]["acta_certificacion_id"]==1?"ACTA":"CERTIFICADO");
        json["no_acta_certificacion_"+indice]         = (!informacion[index])?"":informacion[index]["no_acta_certificacion"];
        name = "defuncion";
      }


      
      json["anonimo_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["anonimo"]==1?"SI":"NO");
            
      json["nombre_"+name+"_"+indice]          = (!informacion[index])?"":(informacion[index]["nombre"]+" "+informacion[index]["apellido_paterno"]+ " "+informacion[index]["apellido_materno"]);
      json["edad_"+name+"_"+indice]          = (!informacion[index])?"":informacion[index]["edad"];
      json["silla_infantil_"+name+"_"+indice]  = (!informacion[index])?"":(informacion[index]["silla_id"]==1?"SI":"NO");
      json["sexo_"+name+"_"+indice]  = (!informacion[index])?"":(informacion[index]["sexo_id"]==1?"MASCULINO":"FEMENINO");
      json["embarazada_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["embarazada"]==1?"SI":"NO");
      
      json["pre_hospitalizacion_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["pre_hospitalizacion"]==1?"SI":"NO");
      json["no_ambulancia_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["no_ambulancia"]);
      
      json["prestador_servicio_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["prestador_servicio"]!=null?arreglo_prestador[informacion[index]["prestador_servicio"]]:'');
      json["otro_prestador_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["otro_prestador"]);
      json["nivel_conciencia_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["nivel_conciencia"]!=null?arreglo_nivel[informacion[index]["nivel_conciencia"]]:'');
      
      json["pulso_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["pulso"]==1?"SI":"NO");
      
      
      json["color_piel_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["color_piel"]);
      json["prioridad_traslado_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["prioridad_traslado"]!=null?arreglo_prioridad[informacion[index]["prioridad_traslado"]]:'');
      
      json["negativa_traslado_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["negativa_traslado"]==1?"SI":"NO");
      json["especifique_negativa_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["especifique_negativa"]);
      json["diagnostico_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["diagnostico"]);
      
      json["hospitalizacion_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["hospitalizacion"]==1?"SI":"NO");
      
      json["municipio_hospitalizacion_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index].municipio)?informacion[index].municipio.descripcion:'';
      json["clues_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index].clues_hospitalizacion)?informacion[index].clues_hospitalizacion.clues+" - "+informacion[index].clues_hospitalizacion.descripcion:'';
      
      json["casco_"+name+"_"+indice]         = (!informacion[index])?"":(informacion[index]["casco"]==1?"SI":"NO");
      json["ubicacion_"+name+"_"+indice]         = (!informacion[index])?"":informacion[index]["ubicacion"];
      
      let vehiculo = "";
      
      
      if(informacion[index] && informacion[index].vehiculo)
      {
        let obj_vehiculo = informacion[index].vehiculo;
        console.log(obj_vehiculo.tipo.descripcion, (obj_vehiculo.marca)?obj_vehiculo.marca.descripcion:obj_vehiculo.otra_marca);
        let tipo = obj_vehiculo.tipo.descripcion;
        let marca = (obj_vehiculo.marca)?obj_vehiculo.marca.descripcion:obj_vehiculo.otra_marca;
        let extra = obj_vehiculo.modelo+" "+obj_vehiculo.placa;;
        vehiculo = tipo+" - "+marca+" - "+extra;
      }
      
      json["vehiculo_"+name+"_"+indice]         = vehiculo;
      json["placa_pais_"+name+"_"+indice] = ((informacion[index])?(informacion[index]["placa_pais"] == 1)?"SI":"NO":"");
      
    }
    return json;
  }

  public exportAsExcelFile(json: any[], excelFileName: string): void {
    const worksheet: XLSX.WorkSheet = XLSX.utils.json_to_sheet(json);
    const workbook: XLSX.WorkBook = { Sheets: { 'data': worksheet }, SheetNames: ['data'] };
    const excelBuffer: any = XLSX.write(workbook, { bookType: 'xlsx', type: 'buffer' });
    this.saveAsExcelFile(excelBuffer, excelFileName);
    this.loadReporteExcel = false;
  }

  private saveAsExcelFile(buffer: any, fileName: string): void {
    const data: Blob = new Blob([buffer], {
      type: EXCEL_TYPE
    });
    FileSaver.saveAs(data, fileName + '_SSA_' + new Date().getTime() + EXCEL_EXTENSION);
  }

}
