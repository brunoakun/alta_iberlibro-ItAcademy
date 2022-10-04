import { environment } from 'src/environments/environment';
import { DatosService } from './_servicios/datos.service';

import { Component } from '@angular/core';
import { UntypedFormBuilder, Validators } from '@angular/forms';
import Validation from './providers/CustomValidators';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  titulo: string = environment.titulo;
  enviado: boolean = false;
  loading: boolean = false;
  errorApi: string = '';
  successApi: string = '';

  public listaPob: Array<string> = [];
  eMailPlaceholder: string = '';

  constructor(
    private formBuilder: UntypedFormBuilder,
    private modal: NgbModal,
    public srvDatos: DatosService
  ) { }

  registerForm = this.formBuilder.group({
    nombreFiscal: ['', Validators.required],
    nombreComercial: ['', Validators.required],
    dir: ['', Validators.required],
    dirCp: ['', [Validators.required, Validators.minLength(5)]],
    dirPob: [''],
    dirPro: [''],
    contacto: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(20)]],
    nif: ['', [Validators.required, Validation.validateDNI]],
    eMail: ['', [Validators.required, Validators.email]],
    telefono: [''],
    iban: ['', [Validators.required, Validation.validateIBAN]],
    tarifa: ['', Validators.required],
    recogida: ['', Validators.required],
    seguro: ['', Validators.required],

    acceptTerms: [false, Validators.requiredTrue],
    acceptCondiciones: [false, Validators.requiredTrue],
    acceptExoneracion: [false, Validators.requiredTrue]

  },
    {
      // validators: [Validation.match('password', 'confirmPassword')]
    }
  );

  ngOnInit() {

  }


  get f() {
    return this.registerForm.controls;
  }



  onSubmit() {
    console.log(this.registerForm.value);
    this.enviado = true;
    if (this.registerForm.invalid) {
      console.log("ERRORES:");
      console.log(this.registerForm.errors);
      return;
    }

    this.loading = true;
    this.srvDatos.addSolicitud(this.registerForm).subscribe((respuesta) => {
      if (!respuesta || respuesta.estado == 'error') {
        this.successApi = '';
        this.errorApi = respuesta.mensaje;
        this.loading = false;
        return;
      }

      console.log('respuesta:');
      console.log(respuesta);
      
      this.errorApi = '';
      this.successApi = respuesta.mensaje;
      this.loading = false;
    });
  }

  onReset() {
    //alert("onReset");
    this.enviado = false;
    this.registerForm.reset();
  }

  contactoChange() {
    this.eMailPlaceholder = '';
    var contacto = this.f['contacto'].value;
    if (contacto.length >= 5) this.eMailPlaceholder = "eMail para contactar con " + contacto;
  }

  // MODAL info //
  abrirModal(contenido: any): void {
    this.modal.open(contenido, { size: 'lg' });
  }
  cerrarModal(): void {
    this.modal.dismissAll('');
  }



  //  BUSCAR CP //
  buscaPob() {
    var cp = this.registerForm.value.dirCp;
    this.registerForm.patchValue({ dirPob: '' });
    this.registerForm.patchValue({ dirPro: '' });
    this.listaPob = [];

    if (cp.length < 5) return;
    this.loading = true;

    this.srvDatos.getPoblaciones(cp).subscribe((respuesta) => {
      if (!respuesta || respuesta.estado == 'error') {
        this.registerForm.controls['dirCp'].setErrors({ 'noExiste': true });
        this.loading = false;
        return;
      }

      // Lista de Poblaciones
      for (let elemento of respuesta.lista) {
        if (!this.listaPob.includes(elemento.Poblacion)) this.listaPob.push(elemento.Poblacion);
      }
      this.registerForm.patchValue({ dirPob: this.listaPob[0] });

      // Provincia
      this.registerForm.patchValue({ dirPro: respuesta.provincia });

      this.loading = false;
    });
  }


  //  ABRIR pdf's //
  verTarifas() {
    if (this.registerForm.value.tarifa == '1') window.open('./assets/pdf/TARIFA_PROMEDIO_INFERIOR_120_ENVIOS_MES.pdf', '_blank');
    if (this.registerForm.value.tarifa == '2') window.open('./assets/pdf/TARIFA_PROMEDIO_SUPERIROR_120_ENVIOS_MES.pdf', '_blank');
  }
  verCondiciones() {
    window.open('./assets/pdf/CONDICIONES_GENERALES_NUEVAS_2022.pdf', '_blank');
  }
  verExoneracion() {
    window.open('./assets/pdf/EXONERACION.pdf', '_blank');
  }



}


