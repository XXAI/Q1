import { Component, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../shared/shared.service';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatTable } from '@angular/material/table';
import { MatExpansionPanel } from '@angular/material/expansion';
import { MatDialog } from '@angular/material/dialog';
import { FormBuilder, FormControl } from '@angular/forms';
import { trigger, transition, animate, style } from '@angular/animations';

import { MediaObserver } from '@angular/flex-layout';
import { ConfirmActionDialogComponent } from '../../utils/confirm-action-dialog/confirm-action-dialog.component';
import { LesionesService } from '../lesiones.service';

import { STEPPER_GLOBAL_OPTIONS } from '@angular/cdk/stepper';

import { AuthService } from '../../auth/auth.service';

import * as FileSaver from 'file-saver';



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

  permisoGuardar:boolean = false;
  isLoading: boolean = false;
  isLoadingPDF: boolean = false;
  isLoadingPDFArea: boolean = false;
  isLoadingAgent: boolean = false;
  loadReporteExcel:boolean = false;
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

  displayedColumns: string[] = ['#','fecha' ,'municipio', 'direccion',  'opciones'];
  dataSource: any = [];
  dataSourceFilters: any = [];

  isLoadingEstadosActuales:boolean = false;
  estadosActuales:any[] = [];

  filterChips:any = []; //{id:'field_name',tag:'description',tooltip:'long_description'}
  
  filterCatalogs:any = {};
  filteredCatalogs:any = {};
  catalogos: any = {};

  filterForm = this.fb.group({

    'catalogo_municipio_id'                  : [undefined],

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

    @ViewChild(MatPaginator) paginator: MatPaginator;
    @ViewChild(MatTable) usersTable: MatTable<any>;
    @ViewChild(MatExpansionPanel) advancedFilter: MatExpansionPanel;

  ngOnInit() {
    this.loadData();
    this.loadPermisos();
    this.loadCatalogos();
  }

  loadCatalogos()
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
    this.loadData();  
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

  public loadPermisos()
  {
    this.lesionesService.getPermisos().subscribe(
      response => {
        response.data.forEach(element => {
         if(element == "permisoGuardarIncidente" || element == "permisoAdmin")
         {
          this.permisoGuardar = true;
         }
        });
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
    this.isLoading = true;
    let params:any;
    if(!event){
      params = { page: 1, per_page: this.pageSize }
    }else{
      params = {
        page: event.pageIndex+1,
        per_page: event.pageSize
      };
    }

    if(event && !event.hasOwnProperty('selectedIndex')){
      this.selectedItemIndex = -1;
    }
    
    params.query = this.searchQuery;

    let filterFormValues = this.filterForm.value;
    let countFilter = 0;

    for(let i in filterFormValues){
      if(filterFormValues[i]){
        if(i == 'catalogo_municipio_id'){
          params[i] = filterFormValues[i];
        }
        countFilter++;
      }
    }

    if(countFilter > 0){
      params.active_filter = true;
    }

    let dummyPaginator;
    if(event){
      this.sharedService.setDataToCurrentApp('paginator',event);
    }else{
      dummyPaginator = {
        length: 0,
        pageIndex: (this.paginator)?this.paginator.pageIndex:this.currentPage,
        pageSize: (this.paginator)?this.paginator.pageSize:this.pageSize,
        previousPageIndex: (this.paginator)?this.paginator.previousPage:((this.currentPage > 0)?this.currentPage-1:0)
      };
    }

    this.sharedService.setDataToCurrentApp('searchQuery',this.searchQuery);
    this.sharedService.setDataToCurrentApp('filter',filterFormValues);
    
    this.lesionesService.getLesionesList(params).subscribe(
      response => {
        console.log(response);
        this.dataSource = [];
        this.resultsLength = 0;
        if(response.data.total > 0){
          this.dataSource = response.data.data;
          this.resultsLength = response.data.total;
        }
        this.isLoading = false; 
      },
      errorResponse =>{
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
        this.isLoading = false;
      } 
    );
    return event;
  }

  reporte()
  {
    let params = { export_excel : true };
    this.loadReporteExcel = true;
    this.lesionesService.getLesionesList(params).subscribe(
      response => {
        this.loadReporteExcel = false;
        //console.log(response);
        FileSaver.saveAs(response,'reporte_general');
      },
      errorResponse =>{
        this.loadReporteExcel = false;
        let objError = errorResponse.error.error.data;
        let claves = Object.keys(objError); 
        this.sharedService.showSnackBar("Existe un problema en el campo "+claves[0], null, 3000);
        this.isLoading = false;
      } 
    );

  }
  verIncidente(indice)
  {
    
  }
  eliminarIncidente(indice)
  {

  }

}
