import { Component } from '@angular/core';
import { NavController, NavParams, MenuController, AlertController} from 'ionic-angular';
import { Http } from "@angular/http";
import { HomePage } from '../home/home';
import { Globals } from '../../providers/globals'
import { SettingsPage } from '../settings/settings';

@Component({
  selector: 'page-contact',
  providers: [Globals],
  templateUrl: 'contact.html'
})
export class ContactPage {
  nameCategory: string;

  constructor(public navCtrl: NavController, public params: NavParams, private http: Http, public globals: Globals, public menuCtrl: MenuController, public alertCtrl: AlertController){
    this.nameCategory = (params.get("nameCategory") != undefined) ? params.get("nameCategory") : "";
    console.log(this.nameCategory);
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack() {
    this.navCtrl.pop();
  }
  goSettings() {
    this.navCtrl.push(SettingsPage);
  }
  closeMenu() {
    this.menuCtrl.close();
  }
  showAlert() {
    let alert = this.alertCtrl.create({
      title: 'TÉRMINOS Y CONDICIONES DE USO',
      subTitle: 'DATOS IDENTIFICATIVOS',
      message: 'En cumplimiento con el deber de información recogido en artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, a continuación se reflejan los siguientes datos: la empresa titular de dominio web es DOSILET by InterCook Solutions (en adelante DOSILET), con domicilio a estos efectos en c/ Joan Güell 52, LOCAL 2, 08028 BARCELONA número de C.I.F.: CCCCC inscrita en el Registro Mercantil de Barcelona: Tomo 43709, folio 57, Hoja B 436160, Inscripción 1. Correo electrónico de contacto: comercial@dosilet.com del sitio web.',
      buttons: ['Ok']
    });
    alert.present();
  }
}
