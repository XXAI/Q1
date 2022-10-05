import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { startWith, map, delay } from 'rxjs/operators';
import { LesionesService } from '../lesiones.service';
import { ImportarService } from '../importar.service';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';

import { MatSnackBar } from '@angular/material/snack-bar';
import { SharedService } from '../../shared/shared.service';
import { Router, ActivatedRoute } from '@angular/router';
import { VehiculosDialogComponent } from '../vehiculos-dialog/vehiculos-dialog.component';
import { VictimasDialogComponent } from '../victimas-dialog/victimas-dialog.component';
import { ViewImagenComponent } from '../view-imagen/view-imagen.component';


@Component({
  selector: 'app-registro-lesion',
  templateUrl: './registro-lesion.component.html',
  styleUrls: ['./registro-lesion.component.css']
})
export class RegistroLesionComponent implements OnInit {

  isLoading:boolean;
  
  catalogos: any = {'municipios':[]};
  filteredCatalogs:any = {};

  selectedItemIndex: number = -1;
  tipo:number = 1;
  id:number;
  tipoAccidenteFlag:boolean = true;
  cantidadTipos:number = 0;
  cantidadCausas:number = 0;
  cantidadAccidente:number = 0;
  cantidadPeaton:number = 0;
  cantidadFalla:number = 0;
  cantidadCamino:number = 0;
  cantidadAgentes:number = 0;
  ValidadorCausas:boolean = true;
  arregloFotos:any = [];

  editable:boolean =true;

  validadorformCausas:string = "true";

  firstFormGroup: FormGroup;
  secondFormGroup: FormGroup;
  principalForm:FormGroup;
  zonaForm:FormGroup;
  tipoAccidenteForm:FormGroup;
  causasForm:FormGroup;
  tipoConductorForm:FormGroup;
  tipoPeatonForm:FormGroup;
  tipoPasajeroForm:FormGroup;
  fallaForm:FormGroup;
  caminoForm:FormGroup;
  agentesForm:FormGroup;
  formFotos:FormGroup;

  archivo: File = null;
  archivoSubido:boolean;
  enviandoDatos:boolean;
  progreso: number = 0; 
  errorArchivo:boolean;
  Fotos:any = [];
  
  datosVehiculo:any = [];
  datosVictima:any = [];
  mediaSize: string;

  latitud: number = 16.204130;
  longitud: number = -92.655800;
  zoom: number = 8;
  latitudSelected!:number;
  longitudSelected!:number;
  
  displayedColumns: string[] = ['tipo','marca','placas','ocupantes', 'actions'];
  displayColumns: string[] = ['tipo','nombre','usuario','hospitalizacion', 'actions'];
  dataSourceVehiculos:any = new MatTableDataSource(this.datosVehiculo);
  dataSourceVictima:any = new MatTableDataSource(this.datosVictima);

  panelOpenState = false;
  constructor(
    private fb: FormBuilder,
    private lesionesService: LesionesService,
    private importarService: ImportarService,
    private snackBar: MatSnackBar,
    private sharedService: SharedService,
    public router: Router,
    private route: ActivatedRoute,
    public dialog: MatDialog
  ) {}

  async ngOnInit() {
    
    this.principalForm = this.fb.group ({

      fecha:[''],
      hora:[''],
      entidad:[1],
      municipio:[''],
      localidad:[''],
      colonia:['',Validators.required],
      calle:['',Validators.required],
      no:['', Validators.required],
      latitud:['',Validators.required],
      longitud:['',Validators.required],
    });

    
    this.zonaForm = this.fb.group ({
      zona:[1,Validators.required],
      carretera:[''],
      interseccion:[''],
      calle1:[''],
      calle2:[''],
      referencia:[''],
      tipo_camino:[''],
      otro_camino:[''],
      via:[1,Validators.required],
      tipo_via:[1],
      tipo_pavimentado:['',Validators.required],
      otro_tipo_via:['']
    });

    this.tipoAccidenteForm = this.fb.group ({
      tipoAccidente_1:[''],
      tipoAccidente_2:[''],
      tipoAccidente_3:[''],
      tipoAccidente_4:[''],
      tipoAccidente_5:[''],
      tipoAccidente_6:[''],
      tipoAccidente_7:[''],
      tipoAccidente_8:[''],
      tipoAccidente_9:[''],
      tipoAccidente_10:[''],
      tipoAccidente_11:[''],
      tipoAccidente_12:[''],
      otro_accidente: ['']
    });
    this.causasForm = this.fb.group ({
      causas_1:[''],
      causas_2:[''],
      causas_3:[''],
      causas_4:[''],
      causas_5:[''],
      causas_6:['']
    });

    this.tipoConductorForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      tipo_7:[''],
      tipo_8:[''],
      tipo_9:[''],
      tipo_10:[''],
      tipo_11:[''],
      tipo_12:[''],
      tipo_13:[''],
      otro:[''],
      sexo:[4],
      aliento_alcoholico:[3],
      cinturon_seguridad:[3],
      edad:['',Validators.required]
    });

    this.tipoPeatonForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      descripcion_otro:['']
    });

    this.tipoPasajeroForm = this.fb.group ({
      causa_pasajero:['',Validators.required],
    });
    
    this.fallaForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      tipo_7:[''],
      tipo_8:[''],
      tipo_9:[''],
      tipo_10:[''],
      tipo_11:[''],
      descripcion_otro:['']
    });
    
    this.caminoForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      tipo_7:[''],
      descripcion_otro:['']
    });

    this.agentesForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      tipo_7:[''],
      tipo_8:[''],
      tipo_9:[''],
      tipo_10:[''],
      descripcion_otro:['']
    });
    
    this.formFotos = this.fb.group ({
      file:[''],
    });

    await this.IniciarCatalogos();
    //this.cargarImages();

    this.route.paramMap.subscribe(async params => {
      this.id = Number(params.get('id'));
      if(this.id > 0)
      {
        this.editable = false;
        this.latitud = Number(params.get('lat'));
        this.longitud = Number(params.get('long'));
        this.latitudSelected = Number(params.get('lat'));
          this.longitudSelected =  Number(params.get('long'));
        this.zoom = 17;
        if(this.id){
          await this.editarRegistro();
        }
      }
      
    });
    
  }

  mapClicked($event: any) {
    this.latitudSelected = $event.coords.lat.toFixed(6);
    this.longitudSelected = $event.coords.lng.toFixed(6);
    this.principalForm.patchValue({latitud: this.latitudSelected});
    this.principalForm.patchValue({longitud:  this.longitudSelected});
  }
  
  public campoOtro(valor)
  {
    this.tipo =valor;
  }

  public validarZona(valor)
  {
    this.zonaForm.clearValidators();
    if(valor == 1)
    {
      this.zonaForm.get("interseccion").setValidators(Validators.required);
      this.zonaForm.get("calle1").setValidators(Validators.required);
      this.zonaForm.get("calle2").setValidators(Validators.required);
      this.zonaForm.get("referencia").setValidators(Validators.required);
      this.zonaForm.get("tipo_pavimentado").setValidators(Validators.required);
      this.zonaForm.get("interseccion").updateValueAndValidity();
      this.zonaForm.get("calle1").updateValueAndValidity();
      this.zonaForm.get("calle2").updateValueAndValidity();
      this.zonaForm.get("referencia").updateValueAndValidity();
      this.zonaForm.get("tipo_pavimentado").updateValueAndValidity();
    }else if(valor == 2){
      
      this.zonaForm.get("tipo_camino").setValidators(Validators.required);
      this.zonaForm.get("tipo_camino").updateValueAndValidity();
    }
  }

  public async editarRegistro()
  {
    this.lesionesService.getIncidente(this.id).subscribe(
      response => {
        console.log(response);
        //Empezamos a construir los arreglos :)
        let p = response;
        let principal = {fecha: p.fecha, hora: p.hora, municipio: p.municipio_id, localidad: p.localidad, colonia: p.colonia, calle: p.calle, no:p.numero, latitud:p.latitud, longitud:p.longitud};
        
        this.principalForm.patchValue(principal);

        let z = response;
        this.validarZona(z.zona_id);
        let zona = {};
        if(z.zona_id == 1)
        {
          zona = {zona: z.zona_id, interseccion:z.interseccion_id, calle1: z.calle1, calle2:z.calle2, referencia: z.punto_referencia, via: z.via_id, tipo_pavimentado:z.tipo_pavimentado };
        }else if(z.zona_id == 2){
          zona = {zona: z.zona_id, tipo_camino:z.tipo_camino, otro_camino: z.otro_tipo_camino, tipo_via:z.tipo_via_id, otro_tipo_via: z.otro_tipo_via, via: z.via_id, tipo_pavimentado:z.tipo_pavimentado };
        }
        this.zonaForm.patchValue(zona);

        let t  = response.tipo_accidente;
        let tipo = {};
        t.forEach(element => {
          tipo['tipoAccidente_'+element.rel_tipo_accidente_id] = 1;
        });
        this.tipoAccidenteForm.patchValue(tipo);
        
        let arregloVehiculo = [];
        console.log("-------------------");
        response.vehiculo.forEach(e => {
          console.log(e);
          let estado = "";
          if(e.estado != null)
          {
            estado =  e.estado.descripcion;
          }
          
          arregloVehiculo.push({dataTipovehiculo: e.tipo.descripcion, dataVehiculo:e.marca.descripcion, modelo:e.modelo, con_placas:e.con_placas, no_placa: e.no_placa, placa_pais:e.placa_pais, dataEstado:estado,no_ocupantes:e.no_ocupantes, color:e.color, catalogo_tipo_vehiculo_id:e.catalogo_tipo_vehiculo_id, marca_id:e.marca_id, entidad_placas: e.entidad_placas});
        });
        this.datosVehiculo = arregloVehiculo;
        this.dataSourceVehiculos.connect().next(this.datosVehiculo);

        let ca = response.causa_accidente;
        let causa = {};
        
        this.cantidadCausas = ca.length;
      
        ca.forEach(element => {
          causa['causas_'+element.rel_causa_accidente_id] = 1;

          switch(element.rel_causa_accidente_id)
          {
            case 1:
              let co = response.causa_conductor;
              let a1 = {};
              this.cantidadAccidente = co.length;
              co.forEach(element => {
                a1['tipo_'+element.rel_causa_conductor_id] = 1;
              });
              let co1 = response.causa_conductor_detalle[0];
              a1['sexo'] = co1.sexo_id;
              a1['aliento_alcoholico']= co1.alcoholico;
              a1['cinturon_seguridad']=co1.cinturon;
              a1['edad']=co1.edad;
              this.tipoConductorForm.patchValue(a1);
              break;
              case 2:
                let pe = response.causa_peaton;
                let p = {};
                this.cantidadPeaton = pe.length;
                pe.forEach(element => {
                  p['tipo_'+element.rel_causa_peaton_id] = 1;
                });
                this.tipoPeatonForm.patchValue(p);
                break;
                
              case 3:
                let pa = response.causa_pasajero;
                let pas = {causa_pasajero: pa.causa_pasajero};
                this.tipoPasajeroForm.patchValue(pas);
                break;
                
              case 4:
                let fa = response.falla_vehiculo;
                let f = {};
                this.cantidadFalla = fa.length;
                fa.forEach(element => {
                  f['tipo_'+element.rel_falla_vehiculo_id] = 1;
                });
                this.fallaForm.patchValue(f);
                break;
                
              case 5:
                let cm = response.condicion_camino;
                let cb = {};
                this.cantidadCamino = cm.length;
                cm.forEach(element => {
                  cb['tipo_'+element.rel_condicion_camino_id] = 1;
                });
                this.caminoForm.patchValue(cb);
                break;
                
              case 6:
                let ag = response.agentes;
                let a2 = {};
                this.cantidadAgentes = ag.length;
                ag.forEach(element => {
                  a2['tipo_'+element.rel_agente_natural_id] = 1;
                });
                this.agentesForm.patchValue(a2);
                break;
                
          }
        });

        this.causasForm.patchValue(causa);
        let vitima = [];
        console.log(response.victima);
        response.victima.forEach(el => {
          let tipo_victima:any = [{id:1, descripcion:"LESIÓN"},{id:2, descripcion:"DEFUNSIÓN"}];
          let validacion:any = [{id:1, descripcion:"ACTA"},{id:2, descripcion:"CERTIFICADO"}];
          let tipo_usuario:any = [{id:1, descripcion:"CONDUCTOR"},{id:2, descripcion:"PASAJERO"},{id:3, descripcion:"PEATÓN"},{id:4, descripcion:"CICLISTA"},{id:5, descripcion:"MOTOCICLISTA"}];
          
          el.dataTipovictima = tipo_victima.find(x=>x.id == el.tipo_id).descripcion;
          if(el.tipo_id == 2)
          {
            el.dataValidacion = validacion.find(x=>x.id == el.acta_certificacion_id).descripcion;
          }
          el.dataTipousuario = tipo_usuario.find(x=>x.id == el.tipo_usuario_id).descripcion;

          let contador =  0;
          let arrayLesiones = Array();
          el.lesion_parte.forEach(lesion => {
            lesion.op_1 =null;
            lesion.op_2 =null;
            lesion.op_3 =null;
            lesion.op_4 =null;
            lesion.op_5 =null;
            lesion.op_6 =null;
            arrayLesiones[contador] = lesion;

            lesion.lesion_victima.forEach(lesionVictima => {
              if(lesionVictima.opcion == 1)
              {
                arrayLesiones[contador].op_1 =true;
              }
              if(lesionVictima.opcion == 2)
              {
                arrayLesiones[contador].op_2 =true;
              }
              if(lesionVictima.opcion == 3)
              {
                arrayLesiones[contador].op_3 =true;
              }
              if(lesionVictima.opcion == 4)
              {
                arrayLesiones[contador].op_4 =true;
              }
              if(lesionVictima.opcion == 5)
              {
                arrayLesiones[contador].op_5 =true;
              }
              if(lesionVictima.opcion == 6)
              {
                arrayLesiones[contador].op_6 =true;
              }
              //lesionVictima.lesiones 
            });
            contador++;
          });
          el.lesiones = arrayLesiones;
          vitima.push(el); 
          
        });
        
        this.datosVictima = vitima;
        console.log(this.datosVictima);
        this.dataSourceVictima.connect().next(this.datosVictima);
        this.cargarImages();
        //this.isLoading = false; 
      },
      errorResponse =>{
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema al cargar los datos", null, 3000);
        this.isLoading = false;
      } 
    );
  }

  showVehiculoDialog(objeto, indice = null){
    console.log(this.catalogos);
    let configDialog = {data:{scSize:'',tipoVehiculo: this.catalogos['TipoVehiculo'], marcas: this.catalogos['Vehiculo'], entidades: this.catalogos['Entidades']}, width:'50%',maxWidth: null,maxHeight: null,height: null};
    if(this.mediaSize == 'xs'){
      configDialog = {
        maxWidth: '100vw',
        maxHeight: '100vh',
        height: '100%',
        width: '100%',
        data:{
          scSize:this.mediaSize,
          tipoVehiculo: this.catalogos['TipoVehiculo'], 
          marcas: this.catalogos['Vehiculo'],
          entidades: this.catalogos['Entidades']
        }
        
      };
    }

    if(objeto != null)
    {
      objeto.index = indice;
      configDialog.data = objeto;
      configDialog.data.tipoVehiculo = this.catalogos['TipoVehiculo'];
      configDialog.data.marcas =  this.catalogos['Vehiculo'];
      configDialog.data.entidades = this.catalogos['Entidades']
    }
    const dialogRef = this.dialog.open(VehiculosDialogComponent, configDialog);

    dialogRef.afterClosed().subscribe(valid => {
      if(valid.activo){ 
        if(valid.index == null)
        {
          this.datosVehiculo.push(valid);
        }else{
          this.datosVehiculo[valid.index] = valid;
        }
        
        this.dataSourceVehiculos.connect().next(this.datosVehiculo);
        if(this.datosVehiculo.length > 0)
        {
          this.tipoAccidenteFlag = false;
        }
        //console.log(this.datosVehiculo);
      }
    });
  }

  eliminarVehiculo(id)
  {
    let indice = this.datosVehiculo.findIndex(x=> x.id == id);
    this.datosVehiculo.splice(indice,1);
    this.dataSourceVehiculos.connect().next(this.datosVehiculo);
  }

  editarVehiculo(obj, indice)
  {
    this.showVehiculoDialog(obj, indice);
  }
  
  showVictimaDialog(objeto, indice = null){
    //console.log(objeto);
    let configDialog = {data:{}, width:'50%',maxWidth: null,maxHeight: null,height: null};
    if(this.mediaSize == 'xs'){
      configDialog = {
        maxWidth: '100vw',
        maxHeight: '100vh',
        height: '100%',
        width: '100%',
        data:{scSize:this.mediaSize}
      };
    }

    if(objeto != null)
    {
      objeto.index = indice;
      configDialog.data = objeto;
    }
    const dialogRef = this.dialog.open(VictimasDialogComponent, configDialog);

    dialogRef.afterClosed().subscribe(valid => {
      if(valid)
      {
        
        if(valid.activo){ 
          if(valid.index == null)
          {
            this.datosVictima.push(valid);
          }else{
            this.datosVictima[valid.index] = valid;
          }
          
          this.dataSourceVictima.connect().next(this.datosVictima);
          //this.changeDetectorRefs.detectChanges();
        }
      }
      
      console.log(valid);
    });
  }

  eliminarVictima(id)
  {
    let indice = this.datosVictima.findIndex(x=> x.id == id);
    this.datosVictima.splice(indice,1);
    this.dataSourceVictima.connect().next(this.datosVictima);
  }

  editarVictima(obj, indice)
  {
    this.showVictimaDialog(obj, indice);
  }

  public async IniciarCatalogos(obj:any = null)
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

  getDisplayFn(label: string){
    return (val) => this.displayFn(val,label);
  }

  displayFn(value: any, valueLabel: string){
    return value ? value[valueLabel] : value;
  }

  public verificacionTipo(check:boolean)
  {
    if(!check)
    {
      this.cantidadTipos++;
    }else{
      this.cantidadTipos--;
    }
  }
  
  public VerificaCausa(check:boolean, tipo)
  {
    if(!check)
    {
      this.cantidadCausas++;
    }else{
      this.cantidadCausas--;
    }

    if(tipo == 1)
    {
      this.validadorformCausas = "tipoConductorForm.invalid";
    }


  }
  
  public VerificarAccidente(check:boolean)
  {
    if(!check)
    {
      this.cantidadAccidente++;
    }else{
      this.cantidadAccidente--;
    }
  }

  public VerificarPeaton(check:boolean)
  {
    if(!check)
    {
      this.cantidadPeaton++;
    }else{
      this.cantidadPeaton--;
    }
  }
  public VerificarFalla(check:boolean)
  {
    if(!check)
    {
      this.cantidadFalla++;
    }else{
      this.cantidadFalla--;
    }
  }
  public VerificarCamino(check:boolean)
  {
    if(!check)
    {
      this.cantidadCamino++;
    }else{
      this.cantidadCamino--;
    }
  }
  public VerificarAgentes(check:boolean)
  {
    if(!check)
    {
      this.cantidadAgentes++;
    }else{
      this.cantidadAgentes--;
    }
  }


  public Guardar(etapa:number)
  {
    let formulario;
    if(etapa == 1){ formulario = this.principalForm.value; }
    if(etapa == 2){ formulario = this.zonaForm.value; }
    if(etapa == 3){ 
      let objeto_accidente = this.tipoAccidenteForm.value;
      objeto_accidente.vehiculos = this.datosVehiculo;
      formulario = objeto_accidente;
    }
    if(etapa == 4){ 
      let datos = this.causasForm.value;
      datos.conductor = this.tipoConductorForm.value;
      datos.peaton = this.tipoPeatonForm.value;
      datos.pasajero = this.tipoPasajeroForm.value;
      datos.falla = this.fallaForm.value;
      datos.camino = this.caminoForm.value;
      datos.agentes = this.agentesForm.value;
      formulario = datos; 
    }
    if(etapa == 5){
      let obj = { 'etapa' : etapa, victimas: []};
      obj.victimas = this.datosVictima;
      formulario = obj; 
      console.log(this.datosVictima);
    }
    formulario.etapa = etapa;

    //this.id = 1;
    this.lesionesService.saveFormulario(formulario, this.id).subscribe(
      response => {
        this.isLoading = false; 
        this.sharedService.showSnackBar("Se ha guardado correctamente el registro", null, 3000);
        this.editable = false;
        if(etapa == 1)
        {
          this.id = response.data.id;
        }
        
      },
      errorResponse =>{
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
        this.isLoading = false;
      } 
    );
  }

  fileChange(event) {
		let fileList: FileList = event.target.files;
    let tamano = fileList.length;
    if (tamano > 0) {
      for (let index = 0; index < tamano; index++) {
        //console.log(fileList[index]);
        this.Fotos.push(<File>fileList[index]);
      }
    }
  }

  subir() {
    this.archivoSubido = false;
    this.isLoading = true;
    let data = {id:this.id};
    console.log(this.Fotos);
    this.importarService.uploadFotos(data, this.Fotos).subscribe(
      response => {
        this.sharedService.showSnackBar("Ha subido correctamente las imagenes", null, 3000);
        this.isLoading = false;
        //console.log(response);
        
        this.cargarImages();
      }, errorResponse => {
        console.log(errorResponse.error);
        this.sharedService.showSnackBar(errorResponse.error, null, 3000);
        this.isLoading = false;
      });
    }

    cargarImages()
    {
      this.lesionesService.cargarImagen(this.id).subscribe(
        response => {
          console.log("---- imagenes");
          console.log(response);
          this.arregloFotos = response.data;
          this.isLoading = false; 
        },
        errorResponse =>{
          let objError = errorResponse.error.error.data;
          let claves = Object.keys(objError); 
          this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
          this.isLoading = false;
        } 
      );
    }

    eliminaImagen(nombre:string)
    {
      this.lesionesService.eliminaImagen(this.id, nombre).subscribe(
        response => {
         this.cargarImages();
          this.isLoading = false; 
        },
        errorResponse =>{
          let objError = errorResponse.error.error.data;
          let claves = Object.keys(objError); 
          this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
          this.isLoading = false;
        } 
      );
    }

    ver(imagen)
    {
      console.log(imagen);
      let configDialog = {data:{imagen:imagen}};
      
      const dialogRef = this.dialog.open(ViewImagenComponent, configDialog);
  
      dialogRef.afterClosed().subscribe(valid => {
        
      });
    }

    
}
