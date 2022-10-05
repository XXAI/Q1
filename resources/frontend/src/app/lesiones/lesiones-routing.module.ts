import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { GuessGuard } from '../auth/guess.guard';
import { AuthGuard } from '../auth/auth.guard';

import { RegistroLesionComponent } from './registro-lesiones/registro-lesion.component';
import { ListaLesionesComponent } from './lista-lesiones/lista-lesiones.component';

const routes: Routes = [

  { path: 'listado-lesiones',       component:  ListaLesionesComponent,     canActivate: [AuthGuard] },
  { path: 'lesiones/registro',         component: RegistroLesionComponent,   canActivate: [AuthGuard] },
  { path: 'lesiones/registro/:id/:lat/:long',    component: RegistroLesionComponent,   canActivate: [AuthGuard] },
  //{ path: 'qr-donante/:codigo',   component: InfoQrDonanteComponent,    canActivate: [GuessGuard] },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LesionesRoutingModule { }
