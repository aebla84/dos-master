import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { HighlightPage } from '../highlight/highlight';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';
// import { Category } from '../../model/category';

@Component({
  selector: 'page-home',
  providers: [Globals],
  templateUrl: 'home.html'
})
export class HomePage {
  categories = [];

  constructor(public navCtrl: NavController, public globals: Globals) {
    this.getCatalog();
  }
  getCatalog() {
      this.globals.getCatalog().subscribe(
        data => {
          console.log(data);
          // this.categories = new Array<Category>();
          // Object.keys(data).forEach(name => {
          //   this.categories.push(new Category(data[name]));
          // });
          // console.log(this.categories);
        },
        err => { console.log(err) }
      );
    }
  goCatalog() {
    this.navCtrl.push(CatalogPage);
  }
  goContact() {
    this.navCtrl.push(HighlightPage);
  }
}
