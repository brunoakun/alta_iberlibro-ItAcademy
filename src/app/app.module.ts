import { NgModule } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';

import { AppComponent } from './app.component';
import { HttpClientModule } from '@angular/common/http';
import { IMaskModule } from 'angular-imask';
import { InputTrimModule } from 'ng2-trim-directive';


@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    ReactiveFormsModule,
    IMaskModule,
    HttpClientModule,
    InputTrimModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
