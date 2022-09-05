import { Component, OnInit, Inject } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { SharedService } from 'src/app/shared/shared.service';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-vehiculos-dialog',
  templateUrl: './vehiculos-dialog.component.html',
  styleUrls: ['./vehiculos-dialog.component.css']
})
export class VehiculosDialogComponent implements OnInit {

  constructor(
    private sharedService: SharedService, 
    private fb: FormBuilder,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<VehiculosDialogComponent>,
  ) { }

  public VehiculoForm = this.fb.group({
    'tipo_vehiculo':[], 
    'marca':[], 
    'modelo':[], 
    'tiene_placas':[], 
    'tipo':[], 
    'estado':[], 
    'no_ocupantes':[], 
    'color':[], 
  });

  ngOnInit() {
  }

  cancelar(): void {
    this.dialogRef.close();
  }

  guardar(): void {
  }
}
