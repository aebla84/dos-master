import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { HighlightPage } from '../highlight/highlight';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {
  category =  [];
  subcategory = [];
  constructor(public navCtrl: NavController) {
  }
  goCatalog() {
    this.navCtrl.push(CatalogPage);
  }
  goContact() {
    this.navCtrl.push(HighlightPage);
  }
}
