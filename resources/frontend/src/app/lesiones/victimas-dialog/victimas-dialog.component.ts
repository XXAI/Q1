import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface VictimaClass {
  index?: number;
  tipo?: number;
  validacion?: number;
  no_acta_certificado?: string;
  nombre?: string;
  apellido_paterno?: string;
  apellido_materno?: string;
  ignora_nombre?: number;
  edad?: number;
  tipo_usuario?: number;
  unidad?: any;

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
  resultado:any = { index : null};
  
  constructor(
    private sharedService: SharedService, 
    @Inject(MAT_DIALOG_DATA) public data: VictimaClass,
    private fb: FormBuilder,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<VictimasDialogComponent>,
  ) { }

  public VictimaForm = this.fb.group({
    'tipo':[1], 
    'validacion':[], 
    'no_acta_certificado':[], 
    'nombre':[], 
    'apellido_paterno':[], 
    'apellido_materno':[], 
    'ignora_nombre':[], 
    'edad':[], 
    'sexo':[], 
    'tipo_usuario':[], 
    'unidad':[], 
  });

  ngOnInit() {
    if(this.data.index != null)
    {
      this.VictimaForm.patchValue(this.data);
    }else{
      this.resultado.index = 0;
    }
  }

  cancelar(): void {
    this.resultado.activo = false;
    this.dialogRef.close(this.resultado);
  }

  guardar(): void {
    this.resultado = this.VictimaForm.value;
    this.resultado.activo = true;
    if(this.data.index != null)
    {
      this.resultado.index = this.data.index;
    }
    this.resultado.dataTipovictima = this.tipo_victima.find(x=>x.id == this.resultado.tipo).descripcion;
    this.resultado.dataTipousuario = this.tipo_usuario.find(x=>x.id == this.resultado.tipo_usuario).descripcion;
    this.resultado.dataSexo = this.sexo.find(x=>x.id == this.resultado.sexo).descripcion;
    this.resultado.dataValidacion = "";
    if(this.resultado.tipo == 2)
    {
      this.resultado.dataValidacion = this.validacion.find(x=>x.id == this.resultado.validacion).descripcion;
    }

    console.log(this.resultado);
    this.dialogRef.close(this.resultado);
    
  }

}
