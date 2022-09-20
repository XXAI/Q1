import { Component, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../shared/shared.service';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTable } from '@angular/material/table';
import { MatExpansionPanel } from '@angular/material/expansion';
import { MatDialog } from '@angular/material/dialog';
import { FormBuilder, FormControl } from '@angular/forms';
import { debounceTime, tap, switchMap, finalize, map, startWith,  } from 'rxjs/operators';
import { trigger, transition, animate, style } from '@angular/animations';
import {Observable} from 'rxjs';
import { MediaObserver } from '@angular/flex-layout';
import { ConfirmActionDialogComponent } from '../../utils/confirm-action-dialog/confirm-action-dialog.component';
import { LesionesService } from '../lesiones.service';
import { ReportWorker } from '../../web-workers/report-worker';
import * as FileSaver from 'file-saver';


import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfMake.vfs = pdfFonts.pdfMake.vfs;

import { STEPPER_GLOBAL_OPTIONS } from '@angular/cdk/stepper';

import { AuthService } from '../../auth/auth.service';
import { formatDate } from '@angular/common';



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

  isLoading: boolean = false;
  isLoadingPDF: boolean = false;
  isLoadingPDFArea: boolean = false;
  isLoadingAgent: boolean = false;
  mediaSize: string;

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

  statusIcon:any = {
    '1-0':'help', //activo
    '1-1':'verified_user', //activo verificado 
    '2':'remove_circle', //baja
    '3':'warning', // No identificado
    '4':'swap_horizontal_circle' //en transferencia
  };

  displayedColumns: string[] = ['#','folio', 'fecha', 'municipio', 'localidad', 'opciones'];
  dataSource: any = [];
  dataSourceFilters: any = [];

  isLoadingEstadosActuales:boolean = false;
  estadosActuales:any[] = [];

  filterChips:any = []; //{id:'field_name',tag:'description',tooltip:'long_description'}
  
  filterCatalogs:any = {};
  filteredCatalogs:any = {};
  catalogos: any = {};

  filterForm = this.fb.group({

    'seguro'                  : [undefined],
    'seguro_id'               : [undefined],
    'entidad_federativa'      : [undefined],
    'entidad_federativa_id'   : [undefined],
    'edad'                    : [undefined],
    'fecha_inicio'            : [undefined],
    'fecha_fin'               : [undefined],

  });

  fechaActual:any = '';
  maxDate:Date;
  minDate:Date;


  constructor(
    private sharedService: SharedService,
    private lesionesService: LesionesService,
    private authService: AuthService,
    private fb: FormBuilder,
    public dialog: MatDialog,
    public mediaObserver: MediaObserver) { }

  @ViewChild(MatPaginator, {static: false}) paginator: MatPaginator;
  @ViewChild(MatTable, {static:false}) usersTable: MatTable<any>;
  @ViewChild(MatExpansionPanel, {static:false}) advancedFilter: MatExpansionPanel;

  ngOnInit() {

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

    return event;
  }

}
