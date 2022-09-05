import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-victimas-dialog',
  templateUrl: './victimas-dialog.component.html',
  styleUrls: ['./victimas-dialog.component.css']
})
export class VictimasDialogComponent implements OnInit {

  constructor(
    private sharedService: SharedService, 
    private fb: FormBuilder,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<VictimasDialogComponent>,
  ) { }

  public VictimaForm = this.fb.group({
    'tipo':[], 
    'validacion':[], 
    'no_acta_certificado':[], 
    'nombre':[], 
    'apellido_paterno':[], 
    'apellido_materno':[], 
    'ignora_nombre':[], 
    'edad':[], 
    'ignora_edad':[], 
    'sexo':[], 
    'tipo_usuario':[], 
    'unidad':[], 
  });

  ngOnInit() {
  }

  cancelar(): void {
    this.dialogRef.close();
  }

  guardar(): void {
  }

}
