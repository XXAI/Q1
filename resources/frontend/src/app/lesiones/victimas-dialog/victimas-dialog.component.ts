import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { Observable } from 'rxjs';
import { debounceTime, finalize, switchMap, tap } from 'rxjs/operators';
import { LesionesService } from '../lesiones.service';

export interface VictimaClass {
  index?: number;
  tipo_id?: number;
  acta_certificacion_id?: number;
  no_acta_certificacion?: string;
  nombre?: string;
  apellido_paterno?: string;
  apellido_materno?: string;
  edad?: number;
  sexo_id?: number;
  tipo_usuario?: number;
  municipio_hospitalizacion?: any;
  hospitalizacion?: any;
  casco?: any;
  ubicacion?: any;
  lesiones?:any
  vehiculos?:any
  municipios?:any
  lesion_id?:any
}

@Component({
  selector: 'app-victimas-dialog',
  templateUrl: './victimas-dialog.component.html',
  styleUrls: ['./victimas-dialog.component.css']
})
export class VictimasDialogComponent implements OnInit {

  tipo_victima:any = [{id:1, descripcion:"LESIÓN"},{id:2, descripcion:"DEFUNSIÓN"}];
  validacion:any = [{id:1, descripcion:"ACTA"},{id:2, descripcion:"CERTIFICADO"}];
  sexo:any = [{id:1, descripcion:"MASCULINO"},{id:2, descripcion:"FEMENINO"}];
  tipo_usuario:any = [{id:1, descripcion:"CONDUCTOR"},{id:2, descripcion:"PASAJERO"},{id:3, descripcion:"PEATÓN"},{id:4, descripcion:"CICLISTA"},{id:5, descripcion:"MOTOCICLISTA"}];
  
  listaVehiculos:any = [];
  listaMunicipios:any = [];
  silla:any = [{id:1, descripcion:"SI"}, {id:2, descripcion:"NO"}];
  localidad:any = [];
  municipioIsLoading:boolean = false;
  ubicacion:any = [
    { id:1, posicion:1, parte:1, descripcion:'CABEZA' },
    { id:2, posicion:1, parte:1, descripcion:'CARA' },
    { id:3, posicion:1, parte:1, descripcion:'CUELLO' },
    { id:4, posicion:1, parte:1, descripcion:'HOMBRO' },
    { id:5, posicion:1, parte:2, descripcion:'BRAZO' },
    { id:6, posicion:1, parte:2, descripcion:'TORAX' },
    { id:7, posicion:1, parte:2, descripcion:'CODO' },
    { id:8, posicion:1, parte:2, descripcion:'ANTEBRAZO' },
    { id:9, posicion:1, parte:2, descripcion:'ABDOMEN' },
    { id:10, posicion:1, parte:2, descripcion:'PELVIS' },
    { id:11, posicion:1, parte:2, descripcion:'MANO' },
    { id:12, posicion:1, parte:2, descripcion:'GENITALES' },
    { id:13, posicion:1, parte:2, descripcion:'DEDOS DE LA MANO' },
    { id:14, posicion:1, parte:3, descripcion:'MUSLO' },
    { id:15, posicion:1, parte:3, descripcion:'RODILLA / PIERNA' },
    { id:16, posicion:1, parte:3, descripcion:'TOBILLO' },
    { id:17, posicion:1, parte:3, descripcion:'DEDOS DEL PIE' },
    { id:18, posicion:2, parte:1, descripcion:'COLUMNA VERTEBRAL' },
    { id:19, posicion:2, parte:2, descripcion:'COLUMNA DORSAL' },
    { id:20, posicion:2, parte:2, descripcion:'COLUMNA LUMBO-SACRA' },
  ]

  prestadorServicio:any = [
    {id:1 , descripcion:"SSA"},
    {id:2 , descripcion:"CRUZ ROJA"},
    {id:3 , descripcion:"PROTECCIÓN CIVIL"},
    {id:4 , descripcion:"SEDENA"},
    {id:5 , descripcion:"IMSS"},
    {id:6 , descripcion:"ISSSTE"},
    {id:7 , descripcion:"ISSSTECH"},
    {id:8 , descripcion:"ERUM"},
    {id:9 , descripcion:"BOMBEROS"},
    {id:10 , descripcion:"OTRO"},
  ]
  nivelConciencia:any = [
    {id:1 , descripcion:"CONCIENTE"},
    {id:2 , descripcion:"RESPUESTA A ESTÍMULOS VERBALES"},
    {id:3 , descripcion:"RESPUESTA A ESTÍMULO DOLOROSO"},
    {id:4 , descripcion:"INCONCIENTE"}
  ]

  listaUnidades:any;

  partes:any = [{}];
  seleccionados:any = [];
  resultado:any = { index : null};

  filteredMunicipio: Observable<any[]>;
  
  constructor(
    private sharedService: SharedService, 
    @Inject(MAT_DIALOG_DATA) public data: VictimaClass,
    private fb: FormBuilder,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<VictimasDialogComponent>,
    private lesionesService: LesionesService,
  ) { }

  public DefuncionForm = this.fb.group({
    'orientacion':[1], 
    'plano':[1], 
    'parte':[1], 
    'op_1':[],
    'op_2':[],
    'op_3':[],
    'op_4':[],
    'op_5':[],
    'op_6':[]
  });
  public VictimaForm = this.fb.group({
    'tipo_id':[1,[Validators.required]], 
    'acta_certificacion_id':[], 
    'no_acta_certificacion':[], 
    'anonimo':['',[Validators.required]], 
    'nombre':[], 
    'apellido_paterno':[], 
    'apellido_materno':[], 
    'pre_hospitalizacion':['',[Validators.required]],
    'edad':[], 
    'silla_id':[], 
    'sexo_id':[], 
    'embarazada':[], 
    'no_ambulancia':[], 
    'prestador_servicio':[''], 
    'otro_prestador':[], 
    'nivel_conciencia':[], 
    'pulso':[], 
    'color_piel':[], 
    'prioridad_traslado':[], 
    'negativa_traslado':[], 
    'especifique_negativa':[], 
    'tipo_usuario_id':['',[Validators.required]], 
    'hospitalizacion':['',[Validators.required]], 
    'municipio_hospitalizacion':[], 
    'clues':[], 
    'casco':[], 
    'ubicacion':['',[Validators.required]], 
    'vehiculo_id':[], 
  });

  async ngOnInit() {
    this.listaVehiculos = this.data.vehiculos;
    this.listaMunicipios = this.data.municipios;
    this.cargarCatalogos();
    if(this.data.index != null)
    {
      
      if(this.data.hospitalizacion == 1)
      {
        await this.unidadMunicipio(this.data.municipio_hospitalizacion);
      }
      this.VictimaForm.patchValue(this.data);
      this.cargarLesiones(this.data.lesiones);
    }else{
      this.resultado.index = 0;
    }
  }


  async cargarCatalogos()
  {
    await this.cargarCatalogo(1,1);
    //
  }



  public cargarLesiones(datos)
  {
    
    //console.log(datos);
    let contador = 0;
    datos.forEach(element => {
      datos[contador].descripcion = this.ubicacion.find(x=>x.id == element.parte).descripcion
      //this.agregarLesion(element);
      contador++;
    });
    this.seleccionados = datos;
  }

  cancelar(): void {
    this.resultado.activo = false;
    this.dialogRef.close(this.resultado);
  }

  public editarLesion(index)
  {
    this.DefuncionForm.patchValue(this.seleccionados[index]);
  }

  guardar(): void {
    this.resultado = this.VictimaForm.value;
    this.resultado.activo = true;
    if(this.data.index != null)
    {
      this.resultado.index = this.data.index;
    }
    this.resultado.dataTipovictima = this.tipo_victima.find(x=>x.id == this.resultado.tipo_id).descripcion;
    this.resultado.dataTipousuario = this.tipo_usuario.find(x=>x.id == this.resultado.tipo_usuario_id).descripcion;
    this.resultado.dataSexo = this.sexo.find(x=>x.id == this.resultado.sexo_id).descripcion;
    if(this.resultado.hospitalizacion == 1)
    {
      this.resultado.clues_hospitalizacion = this.listaUnidades.find(x=>x.clues == this.resultado.clues);
    }

    this.resultado.dataValidacion = "";
    if(this.resultado.acta_certificacion_id == 2)
    {
      this.resultado.dataValidacion = this.validacion.find(x=>x.id == this.resultado.acta_certificacion_id).descripcion;
    }

    this.resultado.lesiones = this.seleccionados;
    this.dialogRef.close(this.resultado);
    
  }

  public agregarLesion(formulario = null)
  {
    let datos;
    if(formulario == null)
    {
      datos = this.DefuncionForm.value;
    }else{
      datos = formulario;
      this.cargarLesionesServer(datos);
    }

    datos.descripcion = this.ubicacion.find(x=>x.id == datos.parte).descripcion;
    let cantidad = 0;
    let indice = 0;
    let bandera  = 0;
    let or = this.DefuncionForm.controls['orientacion'].value;
    let pl = this.DefuncionForm.controls['plano'].value;
    
    let op1 = this.DefuncionForm.controls['op_1'].value;
    let op2 = this.DefuncionForm.controls['op_2'].value;
    let op3 = this.DefuncionForm.controls['op_3'].value;
    let op4 = this.DefuncionForm.controls['op_4'].value;
    let op5 = this.DefuncionForm.controls['op_5'].value;
    let op6 = this.DefuncionForm.controls['op_6'].value;
    

    let bandera_seleccion:boolean = false;
    if(op1 == true || op2 == true || op3 == true || op4 == true || op5 == true || op6 == true)
    {
      bandera_seleccion = true;  
     }

     if(bandera_seleccion == true)
     {
      if(or == 2 && pl==3)
      {
        this.sharedService.showSnackBar("No existe la seleccionables", null, 3000);
      }else{
        while(this.seleccionados.length > cantidad)
        {
          if(this.seleccionados[cantidad].parte == datos.parte){
            bandera++;
            indice = cantidad;
          }
          cantidad++;
        }
        if(bandera>0)
        {
          this.seleccionados[indice] = datos;
        }else
        {
          this.seleccionados.push(datos);
        }  
      }
     }else
     {
      this.sharedService.showSnackBar("Seleccionar lesión", null, 3000);
     }
    
  }

  cargarLesionesServer(datos)
  {

  }

  public unidadMunicipio(valor)
  {
    this.lesionesService.buscarUnidad({municipio_id:valor}).subscribe(
      response => {
        this.listaUnidades = response;
      } 
    );
  }

  public elimnaSeleccion(valor)
  {
    let cantidad = 0;
    let eliminar = 0;
    let bandera = 0;
    while(this.seleccionados.length > cantidad)
    {
      //console.log(this.seleccionados[cantidad]);
      if(this.seleccionados[cantidad].parte == valor){
        //console.log(cantidad);
        bandera++;
        eliminar = cantidad;
      }
      cantidad++;
    }
    if(bandera > 0)
    {
      this.seleccionados.splice(eliminar, 1);
        
    }
    console.log(this.seleccionados);
  }

  public direccion(valor)
  {
    let form = this.DefuncionForm.value;
     this.cargarCatalogo(valor, form.parte);
     
  }

  public plano(valor)
  {
    let form = this.DefuncionForm.value;
    this.cargarCatalogo(form.orientacion, valor);
   
  }

  public cargarCatalogo(direccion, plano)
  {
    this.partes = [];
    let contador = 0;
    while (this.ubicacion.length > contador) {
     let registro = this.ubicacion[contador];

     if(this.ubicacion[contador].posicion == direccion)
     {
       if(this.ubicacion[contador].parte == plano)
       {
         this.partes.push(registro);
       }  
     }
     contador++;
    }

    this.lesionesService.getVehiculos({lesion_id:this.data.lesion_id}).subscribe(
      response => {
        console.log(response)
        this.listaVehiculos = response;
      } 
    );
  }
}
