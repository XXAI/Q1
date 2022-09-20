import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface VehiculoClass {
  index?: number;
  tipo_vhiculod?: number;
  marca?: number;
  modelo?: number;
  tiene_placas?: number;
  tipo?: number;
  no_placa?: number;
  estado?: number;
  no_ocupantes?: number;
  color?: number;
  tipoVehiculo?:[],
  marcas?:any

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
    'tipo_vehiculo':['',,Validators.required], 
    'marca':['',,Validators.required], 
    'modelo':['',,Validators.required], 
    'tiene_placas':['',,Validators.required], 
    'tipo':[], 
    'no_placa':[], 
    'estado':[], 
    'no_ocupantes':['',,Validators.required], 
    'color':['',,Validators.required], 
  });

  ngOnInit() {
    this.tipo_vehiculos = this.data.tipoVehiculo;
    if(this.data.index != null)
    {
      this.VehiculoForm.patchValue(this.data);

    }else{
      this.resultado.index = 0;
    }
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
    //console.log(this.VehiculoForm.value);
    this.resultado = this.VehiculoForm.value;
    this.resultado.activo = true;
    if(this.data.index != null)
    {
      this.resultado.index = this.data.index;
    }
    this.resultado.dataTipovehiculo = this.tipo_vehiculos.find(x=>x.id == this.resultado.tipo_vehiculo).descripcion;
    this.resultado.dataVehiculo = this.vehiculos.find(x=>x.id == this.resultado.marca).descripcion;
    this.resultado.dataEstado = "";
    if(this.resultado.tiene_placas == 1 && this.resultado.tipo == 1)
    {
      this.resultado.dataEstado = this.estados.find(x=>x.id == this.resultado.estado).descripcion;
    }

    this.dialogRef.close(this.resultado);
    //console.log(this.resultado);
    
  }
}
