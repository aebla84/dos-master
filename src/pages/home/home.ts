import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { HighlightPage } from '../highlight/highlight';
import { Globals } from '../../providers/globals';
import { Subcategory } from '../../model/subcategory';

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

        this.categories = new Array<Subcategory>();

        Object.keys(data).forEach(name => {
          if (data[name].parent != 0) {
            this.categories.push(new Subcategory(data[name]));
          }
        });
        console.log(this.categories);
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
