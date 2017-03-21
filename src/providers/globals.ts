import { Injectable } from '@angular/core';
import {Http, Response} from "@angular/http";
import 'rxjs/add/operator/map';
import { Push, PushToken } from '@ionic/cloud-angular';
import { Toast } from 'ionic-native';
import { Platform } from 'ionic-angular';
import { AlertController } from 'ionic-angular';

declare var window: any;

@Injectable()
export class Globals {
  subject: string;
  name: string;
  company: string;
  mailfrom: string;
  phone: string;
  message: string;
  loading: boolean;
  url: string;
  notification: Boolean;

  constructor(public push: Push, private http: Http, private platform: Platform, private alertCtrl: AlertController) {
    this.http = http;
    this.platform = platform;
  }

  // GET de Categorías: Categorías y Productos.
  getCatalog(): any {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_taxonomy_alldata')
      .map(res => res.json());
  }

  // GET de Ofertas.

  getHighlights(): any {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_products_highlight?idcategory=10')
      .map(res => res.json())
  }

  // GET Taxonomy Data de Product

  getTaxonomyDataByCategory(idCategory) {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_taxonomy_data_bycategory?idcategory=' + idCategory)
      .map(res => res.json())
  }

  // GET Product By Taxonomy

  getProductByCategory(idCategory) {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_products_bycategory?idcategory=' + idCategory)
      .map(res => res.json())
  }

  // POST del Token de los dispositivos que abren la App.
  postDeviceToken(deviceToken): void {
    alert(deviceToken);
    this.http.post('', deviceToken)
      .subscribe((res: Response) => {
        this.saveToken(deviceToken);
        console.log(deviceToken);
        deviceToken = res.json();
        alert(deviceToken);
      });
  }

  //Activar notificaciones.
  registerNotifications() {
    this.push.register().then((t: PushToken) => {
      return this.push.saveToken(t);
    }).then((t: PushToken) => {
      console.log('Token saved:', t.token);
      this.postDeviceToken(t.token);
    });
  }

  //Desactivar notificaciones.
  unregisterNotifications() {
    this.push.unregister();
  }

  //Enviar MAIL de contacto
  send(): any {
    this.subject = this.subject;
    this.name = this.subject;
    this.company = this.company;
    this.message = this.message;
    this.phone = this.phone;
    this.mailfrom = this.mailfrom;
    this.url = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/sendmail';
    var data = new FormData();
    data.append('subject', 'Nuevo mensaje de ' + this.subject);
    data.append('message', this.message);
    data.append('mailto', 'jbono@deideasmarketing.com');
    data.append('mailfrom', this.mailfrom);
    data.append('phone', this.phone);
    data.append('name', this.name);
    if (this.company != "undefined") {
      data.append('company', this.company);
    }
    this.loading = true;
    this.http.post(this.url, data)
      .subscribe((res: Response) => {
        data = res.json();
        this.loading = false;
      });
    //document.getElementById("contactForm").reset();
    this.platform.ready().then(() => {
      Toast.show("Tu mensaje ha sido enviado. Gracias.", "short", "center");
      //window.plugins.toast.show("Tu mensaje ha sido enviado. Gracias.", "short", "center");
    });
  }

  // Guardar el Device Token en el servidor.

  saveToken(token): any {
    console.log("ENTRA");
    alert(token);

    this.http.post("http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/save_devices_into_ddbb?registration_id=", token)
      .subscribe((res: Response) => {
        token = res.json();
        alert(res.json());
      });
  }
}
