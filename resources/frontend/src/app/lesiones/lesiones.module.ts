import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../shared/shared.module';

import { MatPaginatorIntl } from '@angular/material/paginator';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, MAT_DATE_LOCALE } from '@angular/material/core';
import { getEspPaginatorIntl } from 'src/app/esp-paginator-intl';

import { LesionesRoutingModule } from './lesiones-routing.module';
import { RegistroLesionComponent } from './registro-lesiones/registro-lesion.component';
import { ListaLesionesComponent } from './lista-lesiones/lista-lesiones.component';



@NgModule({
    declarations: [
        RegistroLesionComponent,
        ListaLesionesComponent
    ],
    imports: [
        CommonModule,
        SharedModule,
        MatNativeDateModule,
        MatDatepickerModule,
        LesionesRoutingModule
    ],
    providers: [
        { provide: MAT_DATE_LOCALE, useValue: 'es-MX' },
        { provide: MatPaginatorIntl, useValue: getEspPaginatorIntl() }
    ]
})
export class LesionesModule { }
