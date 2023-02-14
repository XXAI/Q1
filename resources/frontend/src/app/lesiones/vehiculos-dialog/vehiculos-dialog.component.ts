import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface VehiculoClass {
  index?: number;
  catalogo_tipo_vehiculo_id?: number;
  otro_tipo_vehiculo?: string;
  marca_id?: number;
  otra_marca?: string;
  modelo?: number;
  con_placas?: number;
  uso_vehiculo?: number;
  tipo_uso_vehiculo?: number;
  puesto_disposicion?: number;
  placa_pais?: number;
  no_placa?: number;
  entidad_placas?: number;
  no_ocupantes?: number;
  color?: number;
  tipoVehiculo?:[],
  marcas?:any
  entidades?:any
}

@Component({
  selector: 'app-vehiculos-dialog',
  templateUrl: './vehiculos-dialog.component.html',
  styleUrls: ['./vehiculos-dialog.component.css']
})
export class VehiculosDialogComponent implements OnInit {

  tipo_vehiculos:any = [];
  tipo_placas:any = [{id:1, descripcion:"NACIONAL"},{id:2, descripcion:"INTERNACIONAL"}];
  uso_vehiculo:any = [{id:1, descripcion:"PARTICULAR"},{id:2, descripcion:"PÚBLICO"},{id:3, descripcion:"VEHÍCULO DE PASAJEROS"},{id:4, descripcion:"VEHÍCULO SEGUN TIPO DE CARGA"}];
  listado:any = [{id:1, tipo:2, descripcion:"PASAJERO"},
                          {id:2, tipo:2, descripcion:"CARGA"},
                          {id:3, tipo:2, descripcion:"RURAL MIXTO DE CARGA Y PASAJE"},
                          {id:4, tipo:2, descripcion:"ESPECIAL"},
                          {id:5, tipo:3, descripcion:"COLECTIVO URBANO"},
                          {id:6, tipo:3, descripcion:"COLECTIVO SUBURBANO"},
                          {id:7, tipo:3, descripcion:"COLECTIVO INTERMUNICIPAL"},
                          {id:8, tipo:3, descripcion:"COLECTIVO FORANEO"},
                          {id:9, tipo:3, descripcion:"TAXI"},
                          {id:10, tipo:4, descripcion:"BAJO TONELAJE"},
                          {id:11, tipo:4, descripcion:"ALTO TONELAJE"},
                          {id:12, tipo:4, descripcion:"PAQUETERÍA"},
                          {id:13, tipo:4, descripcion:"MATERIALES PARA LA CONSTRUCCIÓN A GRANEL"},
                          {id:14, tipo:4, descripcion:"ESPECIALIZADA"},
                          
                          ];

  tipo_uso_vehiculo:any = [];
  disposicion:any = [{id:1, descripcion:"SI"},{id:2, descripcion:"NO"}];
  vehiculos:any = [];
  estados:any = [{id:1, descripcion:"CHIAPAS"}];
  resultado:any = { index : null};
  
  constructor(
    private sharedService: SharedService, 
    @Inject(MAT_DIALOG_DATA) public data: VehiculoClass,
    private fb: FormBuilder,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<VehiculosDialogComponent>,
  ) { }

  public VehiculoForm = this.fb.group({
    'catalogo_tipo_vehiculo_id':['',Validators.required], 
    'otro_tipo_vehiculo':[''], 
    'marca_id':['',Validators.required], 
    'otra_marca':[''], 
    'modelo':['',Validators.required], 
    'uso_vehiculo':[1,Validators.required], 
    'tipo_uso_vehiculo_id':[''], 
    'puesto_disposicion':['',Validators.required], 
    'con_placas':['',Validators.required], 
    'placa_pais':[], 
    'no_placa':[], 
    'entidad_placas':[], 
    'no_ocupantes':['',[Validators.required, Validators.min(0)]], 
    'color':['',Validators.required], 
  });

  ngOnInit() {
    this.tipo_vehiculos = this.data.tipoVehiculo;
    console.log(this.data);
    if(this.data.index != null)
    {
      this.CambiaUso(this.data.uso_vehiculo);
      this.cargarDatos();
    }else{
      
      this.cargarMarcas();
      this.resultado.index = 0;
    }
  }

  public async cargarDatos()
  {
    await this.cargarMarcas();
    this.VehiculoForm.patchValue(this.data);
  }

  public cargarMarcas()
  {
    //this.vehiculos = [{id:'',descripcion:"MARCA DE VEHÍCULO"}];
    this.data.marcas.forEach(element => {
      this.vehiculos.push(element);
      /*if(element.catalogo_vehiculo_id == tipo)
      {
        this.vehiculos.push(element);
      }*/
      
    });
  }

  CambiaUso(value)
  {
    //console.log(this.listado.find(item => item.tipo == value));
    //this.listado.Where()
    this.tipo_uso_vehiculo = this.listado.filter(item => item.tipo == value);
    
  }

  cancelar(): void {
    this.resultado.activo = false;
    this.dialogRef.close(this.resultado);
  }

  guardar(): void {
    this.resultado = this.VehiculoForm.value;
    this.resultado.activo = true;
    if(this.data.index != null)
    {
      this.resultado.index = this.data.index;
    }
    console.log(this.resultado.catalogo_tipo_vehiculo_id);
    this.resultado.dataTipovehiculo = this.tipo_vehiculos.find(x=>x.id == this.resultado.catalogo_tipo_vehiculo_id).descripcion;
    if(this.resultado.marca_id!=999)
    {
      this.resultado.dataVehiculo = this.vehiculos.find(x=>x.id == this.resultado.marca_id).descripcion;
    }else{
      this.resultado.dataVehiculo = this.resultado.otra_marca;
    }
    //
    this.resultado.dataEstado = "";
    if(this.resultado.con_placas == 1 && this.resultado.placa_pais == 1)
    {
      this.resultado.dataEstado = this.data.entidades.find(x=>x.id == this.resultado.entidad_placas).descripcion;
    }

    this.dialogRef.close(this.resultado);
  }
}
