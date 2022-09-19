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
  url_catalogos                           = `${environment.base_url}/catalogos`;       

  constructor(private http: HttpClient) { }

  getCatalogos(payload):Observable<any> {
    return this.http.get<any>(this.url_catalogos,{params: payload}).pipe(
      map( response => {
        return response;
      })
    );
  }
  
  getLesionesList(payload):Observable<any> {
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

}
