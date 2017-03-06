import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { ContactPage } from '../contact/contact';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  constructor(public navCtrl: NavController) {}

  goCatalog() {
    this.navCtrl.push(CatalogPage);
  }
  goContact() {
    this.navCtrl.push(ContactPage);
  }
}
