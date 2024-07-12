import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class LesionesService {

  url                                     = `${environment.base_url}/lesiones`;       
  url_reporte                             = `${environment.base_url}/reporte-lesiones`;       
  url_catalogos                           = `${environment.base_url}/catalogos`;         
  url_catalogo_localidad                  = `${environment.base_url}/catalogo-localidad`;         
  url_catalogo_unidad                     = `${environment.base_url}/catalogo-unidad`;         
  url_vehiculos                           = `${environment.base_url}/lista-vehiculos`;         
  url_imagenes                            = `${environment.base_url}/imagenes`;         
  url_documentos                          = `${environment.base_url}/documentos`;    
  url_document                            = `${environment.base_url}/document-download`;     
  url_permisos                            = `${environment.base_url}/permisos-lesiones`;     

  constructor(private http: HttpClient) {}

  getCatalogos(payload):Observable<any> {
    return this.http.get<any>(this.url_catalogos,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }
  
  getLesionesList(payload):Observable<any> {
    if(payload.export_excel){
      //return this.http.get<any>(this.url_reporte, {params:payload, responseType: 'blob' as 'json'});
      return this.http.get<any>(this.url_reporte,{params: payload}).pipe(
        map( response => {
          return response;
        })
      );
    }
    return this.http.get<any>(this.url,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }
  


  getIncidente(id) {
    return this.http.get<any>(this.url+'/'+id,{}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }
  
  getPermisos() {
    return this.http.get<any>(this.url_permisos,{}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }

  updateIncidente(id,payload) {
    return this.http.put<any>(this.url+'/'+id,payload).pipe(
      map( (response) => {
        return response;
      }
    ));
  }

  createIncidente(payload) {
    return this.http.post<any>(this.url,payload).pipe(
      map( (response) => {
        return response;
      }
    ));
  }  

  deleteIncidente(id) {
    return this.http.delete<any>(this.url+'/'+id,{}).pipe(
      map( (response) => {
        return response;
      }
    ));
  }

  saveFormulario(payload, id)
  {
    return this.http.put<any>(this.url+"/"+id,payload).pipe(
      map( (response) => {
        return response;
      }
    ));
  }

  buscarLocalidad(payload):Observable<any> {
    return this.http.get<any>(this.url_catalogo_localidad  ,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }
  
  buscarUnidad(payload):Observable<any> {
    return this.http.get<any>(this.url_catalogo_unidad  ,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }
  
  getVehiculos(payload):Observable<any> {
    return this.http.get<any>(this.url_vehiculos ,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }

  cargarImagen(id)
  {
    return this.http.get<any>(this.url_imagenes+'/'+id,{}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }
  
  cargarDocumentos(id)
  {
    return this.http.get<any>(this.url_documentos+'/'+id,{}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }

  eliminaImagen(id, nombre)
  {
    return this.http.delete<any>(this.url_imagenes+'/'+id,{params: {'nombre': nombre}}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }
  
  eliminaDocumento(id, nombre)
  {
    return this.http.delete<any>(this.url_documentos+'/'+id,{params: {'identificador': nombre}}).pipe(
      map( (response: any) => {
        return response;
      }
    ));
  }

  getDocument(id):Observable<any> {
    
    return this.http.get<any>(this.url_document+"/"+id, {params:{}, responseType: 'blob' as 'json'}).pipe(
      map( response => {
        return response;
      })
    );
    
  }

}
