import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';
import { HttpClient, HttpBackend, HttpHeaders } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ImportarService {

  private httpImport: HttpClient;
  url_fotos                               = `${environment.base_url}/subir-fotos`;    

  constructor( handler: HttpBackend) {
    this.httpImport = new HttpClient(handler);
   }


  uploadFotos(data:any,files:any): Observable<any>{
    //console.log(files);
    const formData: FormData = new FormData();
    for (let index = 0; index < files.length; index++) {
      formData.append('archivo_'+index, <File>files[index], files[index].name);
    }
    formData.append('id', data.id);
    
    let token = localStorage.getItem('token');
    let headers = new HttpHeaders().set(
      "Authorization",'Bearer '+localStorage.getItem("token"),
    );
    headers.append('Content-Type','application/x-www-form-urlencoded;charset=UTF-8');
    headers.append('Access-Control-Allow-Origin','*');
    return this.httpImport.post(this.url_fotos, formData, { headers:headers});
    return ;
  }
}
