import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { NavController } from 'ionic-angular';
import 'rxjs/add/operator/map';

@Injectable()
export class Globals {

  constructor(public navCtrl: NavController, private http: Http) {
    this.http = http;
  }

  // GET de Categorías: Categorías y Productos.
  getCatalog(): any {
    return this.http.get('http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_taxonomy_alldata')
      .map(res => res.json());
  }

}
