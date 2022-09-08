import { Component, OnInit, ChangeDetectorRef  } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { startWith, map } from 'rxjs/operators';
import { PublicService } from '../lesiones.service';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';

import { MatSnackBar } from '@angular/material/snack-bar';
import { SharedService } from '../../shared/shared.service';
import { Router, ActivatedRoute } from '@angular/router';
import { formatDate } from '@angular/common';
import { VehiculosDialogComponent } from '../vehiculos-dialog/vehiculos-dialog.component';
import { VictimasDialogComponent } from '../victimas-dialog/victimas-dialog.component';

import { ReportWorker } from '../../web-workers/report-worker';
import * as FileSaver from 'file-saver';

@Component({
  selector: 'app-registro-lesion',
  templateUrl: './registro-lesion.component.html',
  styleUrls: ['./registro-lesion.component.css']
})
export class RegistroLesionComponent implements OnInit {

  isLoading:boolean;
  
  catalogos: any = {};
  filteredCatalogs:any = {};

  selectedItemIndex: number = -1;
  tipo:number = 1;

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
  datosVehiculo:any = [{
    activo: true,
    color: "PLATA",
    dataEstado: "CHIAPAS",
    dataTipovehiculo: "VOLSWAGEN",
    dataVehiculo: "VENTO",
    estado: 1,
    marca: 1,
    modelo: "2018",
    no_ocupantes: "4",
    no_placa: "D1234",
    tiene_placas: 1,
    tipo: 1,
    tipo_vehiculo: 1
  }];
  datosVictima:any = [{
    activo: true,
    apellido_materno: "x",
    apellido_paterno: "x",
    dataSexo: "MASCULINO",
    dataTipousuario: "PEATÓN",
    dataTipovictima: "LESIÓN",
    dataValidacion: "",
    edad: 23,
    ignora_nombre: null,
    no_acta_certificado: null,
    nombre: "x",
    sexo: 1,
    tipo: 1,
    tipo_usuario: 3,
    unidad: "asd",
    validacion: null
  }];
  mediaSize: string;

  displayedColumns: string[] = ['tipo','marca','placas','ocupantes', 'actions'];
  displayColumns: string[] = ['tipo','nombre','usuario','hospitalizacion', 'actions'];
  dataSourceVehiculos:any = new MatTableDataSource(this.datosVehiculo);
  dataSourceVictima:any = new MatTableDataSource(this.datosVictima);

  panelOpenState = false;
  constructor(
    private fb: FormBuilder,
    private publicService: PublicService,
    private snackBar: MatSnackBar,
    private sharedService: SharedService,
    public router: Router,
    private route: ActivatedRoute,
    public dialog: MatDialog,
    private changeDetectorRefs: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.principalForm = this.fb.group ({

      fecha:[''],
      hora:[''],
      entidad:[1],
      municipio:[''],
      localidad:[''],
      colonia:['',Validators.required],
      calle:['',Validators.required],
      no:['', Validators.pattern(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)],
      latitud:['',Validators.required],
      longitud:['',Validators.required],
      

    });

    
    this.zonaForm = this.fb.group ({
      zona:[1],
      carretera:[''],
      interseccion:[''],
      calle1:[''],
      calle2:[''],
      referencia:[''],
      tipo_camino:[''],
      otro_camino:[''],
      via:[1],
      tipo_via:[1],
      tipo_pavimentado:[''],
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
      otro_tipo:[''],
      otro:[''],
      sexo:[4],
      aliento_alcoholico:[3],
      cinturon_seguridad:[3],
      edad:['']
    });

    this.tipoPeatonForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      otro:[''],
      descripcion_otro:['']
    });

    this.tipoPasajeroForm = this.fb.group ({
      causa_pasajero:[''],
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
      otro:[''],
      descripcion_otro:['']
    });
    
    this.caminoForm = this.fb.group ({
      tipo_1:[''],
      tipo_2:[''],
      tipo_3:[''],
      tipo_4:[''],
      tipo_5:[''],
      tipo_6:[''],
      otro:[''],
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
      otro:[''],
      descripcion_otro:['']
    });
  }
  
  public campoOtro(valor)
  {
    this.tipo =valor;
  }

  showVehiculoDialog(objeto, indice = null){
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
        //this.changeDetectorRefs.detectChanges();
      }
      console.log(valid);
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

  public IniciarCatalogos(obj:any)
  {
    this.isLoading = true;

    let carga_catalogos = [
      {nombre:'estados',orden:'nombre'},
      {nombre:'seguros',orden:'descripcion'},
    ];

    this.publicService.obtenerCatalogos(carga_catalogos).subscribe(
      response => {

        this.catalogos = response.data;

        this.filteredCatalogs['estados'] = this.principalForm.get('entidad_federativa_id').valueChanges.pipe(startWith(''),map(value => this._filter(value,'estados','nombre')));
        this.filteredCatalogs['seguros'] = this.principalForm.get('seguro_id').valueChanges.pipe(startWith(''),map(value => this._filter(value,'seguros','descripcion')));

      
        if(obj)
        {
          this.principalForm.get('entidad_federativa_id').setValue(obj.entidad_federativa);
          this.principalForm.get('seguro_id').setValue(obj.seguro);
        }
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
}
