import { Injectable } from '@angular/core';
import {Http, Response} from "@angular/http";
import 'rxjs/add/operator/map';
import { Push, PushToken } from '@ionic/cloud-angular';
import { Toast } from 'ionic-native';
import { Platform } from 'ionic-angular';
import { AlertController } from 'ionic-angular';
import { Storage } from '@ionic/storage';

declare var window: any;

@Injectable()
export class Globals {
  subject: string;
  name: string;
  company: string;
  category: string;
  mailfrom: string;
  phone: string;
  message: string;
  loading: boolean;
  url: string;
  notification: Boolean;
  data: {};
  storage: any;
  tokenSave : string;

  constructor(public push: Push, private http: Http, private platform: Platform, private alertCtrl: AlertController) {
    this.http = http;
    this.platform = platform;
    this.data = {};
    this.storage = new Storage();
    this.storage.ready().then(() => {
      this.storage.get('token').then((val) => {
        this.tokenSave = val;
        console.log('tokenSave are: ', val);
      })
    });
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

  getProductByCategory(idCategory): any {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_products_bycategory?idcategory=' + idCategory)
      .map(res => res.json())
  }

  // POST del Token de los dispositivos que abren la App.
  postDeviceToken(deviceToken): void {
    this.http.post('', deviceToken)
      .subscribe((res: Response) => {
        this.saveToken(deviceToken);
        console.log(deviceToken);
        deviceToken = res.json();
      });
  }

  //Activar notificaciones.
  registerNotifications() {
    //alert("registerNotifications");
    this.push.register().then((t: PushToken) => {
      //alert("then");
      return this.push.saveToken(t);
    }).then((t: PushToken) => {
        //alert("then2");
      console.log('Token saved:', t.token);
      this.storage.set('token', t.token);
      this.storage.get('token').then((val) => {
        this.tokenSave = val;
        //alert("registerNotifications " + this.tokenSave);
      })
      this.postDeviceToken(t.token);
    });
  }

  //Desactivar notificaciones.
  unregisterNotifications() {
    this.push.unregister();
    this.storage.get('token').then((val) => {
      this.tokenSave = val;
      //alert("unregisterNotifications " + this.tokenSave);
    })
    if(this.tokenSave != undefined &&  this.tokenSave != "") this.updateStatusDevice(this.tokenSave);
  }

  //Enviar MAIL de contacto
  send(nameCategory): any {
    this.subject = this.subject;
    this.name = this.subject;
    this.category = nameCategory;
    this.company = this.company;
    this.message = this.message;
    this.phone = this.phone;
    this.mailfrom = this.mailfrom;
    this.url = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/sendmail';
    var data = new FormData();
    console.log("Categoría: " + this.category);
    if (this.category != undefined && this.category != "") {
      data.append('subject', 'Solicitud de información ' + this.category);
    } else {
      data.append('subject', 'Solicitud de información ');
    }
    data.append('message', this.message);
    data.append('mailto', 'bnavarro@deideasmarketing.com');
    data.append('mailfrom', this.mailfrom);
    data.append('phone', this.phone);
    data.append('name', this.name);
    data.append('category', this.category);
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
    var link = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/save_devices_into_ddbb';
    var data = new FormData();
    data.append('registration_id', token);
    this.http.post(link, data)
      .subscribe(data2 => {
      }, error => {
        console.log("Oooops!");
      });
  }

  updateStatusDevice(token): any {
    //alert("updateStatusDevice");
    //alert(token);
    var link = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/update_status_in_ddbb';
    var data = new FormData();
    data.append('registration_id', token);
    this.http.post(link, data)
      .subscribe(data2 => {
          //this.storage.set('notification',false);
      }, error => {
        console.log("Oooops!");
      });
  }
}
