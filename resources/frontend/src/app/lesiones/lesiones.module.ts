import { NgModule } from '@angular/core';
import { NgxMaterialTimepickerModule } from 'ngx-material-timepicker';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../shared/shared.module';

import { MatPaginatorIntl } from '@angular/material/paginator';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, MAT_DATE_LOCALE } from '@angular/material/core';
import { getEspPaginatorIntl } from 'src/app/esp-paginator-intl';

import { LesionesRoutingModule } from './lesiones-routing.module';
import { RegistroLesionComponent } from './registro-lesiones/registro-lesion.component';
import { ListaLesionesComponent } from './lista-lesiones/lista-lesiones.component';
import { VehiculosDialogComponent } from './vehiculos-dialog/vehiculos-dialog.component';
import { VictimasDialogComponent } from './victimas-dialog/victimas-dialog.component';
import { ViewImagenComponent } from './view-imagen/view-imagen.component';
import { AgmCoreModule  } from '@agm/core';

@NgModule({
    declarations: [
        RegistroLesionComponent,
        ListaLesionesComponent,
        VehiculosDialogComponent,
        VictimasDialogComponent,
        ViewImagenComponent
    ],
    imports: [
        CommonModule,
        SharedModule,
        MatNativeDateModule,
        MatDatepickerModule,
        LesionesRoutingModule,
        NgxMaterialTimepickerModule,
        AgmCoreModule.forRoot({
            apiKey: 'AIzaSyDs6qDgnfMwaHCrOhA0tCvaUPgANTD0Urw'
        })
    ],
    providers: [
        { provide: MAT_DATE_LOCALE, useValue: 'es-MX' },
        { provide: MatPaginatorIntl, useValue: getEspPaginatorIntl() }
    ]
})
export class LesionesModule { }
