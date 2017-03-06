import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { NavController } from 'ionic-angular';
import 'rxjs/add/operator/map';
import { CatalogPage } from '../pages/catalog/catalog';
import { ContactPage } from '../pages/contact/contact';

@Injectable()
export class Globals {
  pages: Array<{ title: string, component: any }>;

  constructor(public navCtrl: NavController, private http: Http) {
    this.http = http;
    this.pages = [
      { title: 'CATÁLOGO', component: CatalogPage },
      { title: 'CONTACTO', component: ContactPage }
    ];
  }

  openPage(p) {
    this.navCtrl.push(p);
  }

}
