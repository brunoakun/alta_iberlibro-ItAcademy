import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DatosService {
  apiURL: string = environment.apiURL;
  constructor(private http: HttpClient) { }

  getPoblaciones(cp: string) { 
    const path = `${this.apiURL}?accion=busca_cp&cp=${cp}`;
    return this.http.get<any>(path);
  }

  addSolicitud(solicitud: any) { 
    const data = JSON.stringify(solicitud.value);
    const path = `${this.apiURL}?accion=add_solicitud`;
    return this.http.post<any>(path,data);
  }

  


}
