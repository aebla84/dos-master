import { Injectable } from '@angular/core';
import {Http, Response} from "@angular/http";
import 'rxjs/add/operator/map';
import { Push, PushToken } from '@ionic/cloud-angular';
import { Toast } from 'ionic-native';

@Injectable()
export class Globals {
notification: Boolean;

  constructor(public push: Push, private http: Http) {
    this.http = http;
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
      return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_products_bycategory?idcategory='+ idCategory)
      .map(res => res.json())
  }

  // POST del Token de los dispositivos que abren la App.
  postDevicetoken(deviceToken): void {
    this.http.post('', deviceToken)
      .subscribe((res: Response) => {
        console.log(deviceToken);
        deviceToken = res.json();
      });
  }

  //Activar notificaciones.
  registerNotifications() {
    this.push.register().then((t: PushToken) => {
      return this.push.saveToken(t);
    }).then((t: PushToken) => {
      console.log('Token saved:', t.token);
      this.postDevicetoken(t.token);
      Toast.show('Notificaciones activadas', '5000', 'center').subscribe(
        toast => {
          console.log(toast);
        }
      );
    });
  }

  //Desactivar notificaciones.
  unregisterNotifications() {
    this.push.unregister();
    Toast.show('Notificaciones desactivadas', '5000', 'center').subscribe(
      toast => {
        console.log(toast);
      }
    );
  }

}
