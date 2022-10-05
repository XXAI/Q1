import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface VehiculoClass {
  index?: number;
  catalogo_tipo_vehiculo_id?: number;
  marca_id?: number;
  modelo?: number;
  con_placas?: number;
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
    'marca_id':['',Validators.required], 
    'modelo':['',Validators.required], 
    'con_placas':['',Validators.required], 
    'placa_pais':[], 
    'no_placa':[], 
    'entidad_placas':[], 
    'no_ocupantes':['',Validators.required], 
    'color':['',Validators.required], 
  });

  ngOnInit() {
    this.tipo_vehiculos = this.data.tipoVehiculo;
    console.log(this.data);
    if(this.data.index != null)
    {
      this.cargarDatos();
      

    }else{
      this.resultado.index = 0;
    }
  }

  public async cargarDatos()
  {
    await this.cargarMarcas(this.data.catalogo_tipo_vehiculo_id);
    this.VehiculoForm.patchValue(this.data);
  }

  public cargarMarcas(tipo:number)
  {
    this.vehiculos = [{id:'',descripcion:"TIPO DE VEHICULO"}];
    this.data.marcas.forEach(element => {
      if(element.catalogo_vehiculo_id == tipo)
      {
        this.vehiculos.push(element);
      }
      
    });
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
    this.resultado.dataTipovehiculo = this.tipo_vehiculos.find(x=>x.id == this.resultado.catalogo_tipo_vehiculo_id).descripcion;
    this.resultado.dataVehiculo = this.vehiculos.find(x=>x.id == this.resultado.marca_id).descripcion;
    this.resultado.dataEstado = "";
    if(this.resultado.con_placas == 1 && this.resultado.placa_pais == 1)
    {
      this.resultado.dataEstado = this.data.entidades.find(x=>x.id == this.resultado.entidad_placas).descripcion;
    }

    this.dialogRef.close(this.resultado);
  }
}
