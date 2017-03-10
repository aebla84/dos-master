import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { HighlightPage } from '../highlight/highlight';
import { Globals } from '../../providers/globals';
import { Category } from '../../model/category';
//
// interface Subextra {
//   id: number;
//   reference: string;
//   price: string;
// }
//
// interface ExtraID {
//   [label: string]: Subextra;
// }
//
// interface Extras{
//   [id: number]: ExtraID;
// }

interface Product {
  idproduct: number;
  reference: string;
  type: string;
  dimensions: string;
  conveyor_width: string;
  conveyor_length: string;
  conveyor_entry: string;
  volume: string;
  weight: string;
  power: string;
  voltage: string;
  frequency: string;
  price: string;
  details: string;
  count_extras: number;
  //[reference: string]: Extras;
}

type ServerResponse = {
  [name: string]: { [name: string]: Product; } & { name: string };
}

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

        this.categories = new Array<Category>();
        Object.keys(data).forEach(name => {
          this.categories.push(new Category(data[name]));
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
