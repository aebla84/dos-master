
import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import {Http} from '@angular/http';
import { HomePage } from '../home/home';
import { SettingsPage } from '../settings/settings';
import { ProductPage } from '../product/product';
import { Globals } from '../../providers/globals';
import { LoadingController } from 'ionic-angular';
import { Product } from '../../model/product';

@Component({
  selector: 'page-highlight',
  providers: [Globals],
  templateUrl: 'highlight.html'
})
export class HighlightPage {
  dataHighlightUrl: string;
  ofertas = [];
  image: string;
  products = [];
  description: string;
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
  extras = [];

  name: string;
  extras2 = [];

  extra_id: string;
  extra_reference: string;
  extra_prince: string;
  aux = [];
  loader: any;
  categories = [];
  subcategory = [];
  product_name: string;

  constructor(public navCtrl: NavController, public navParams: NavParams, private http: Http, public globals: Globals, public loadingCtrl: LoadingController) {
    this.loader = this.loadingCtrl.create({
      spinner: 'bubbles',
      content: "Cargando ofertas..."
    });
    this.loader.present();
    this.getHighlights();
  }

  getHighlights() {
    this.globals.getHighlights().subscribe(data => {
      this.loader.dismiss();
      for (var i = 0; i < data.length; i++) {
        //this.ofertas[0] = data[0];
        this.image = (data[i].image != false && data[i].image != null
          && data[i].image.sizes != null
          && data[i].image.sizes != "null"
          && data[i].image.sizes != "undefined"
          && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";

        this.product_name = data[i].product['post_title'];

        console.log("extras");
        console.log(data[i].extras);
        console.log(data[i].description);
        this.products.push(new Product(data[i], this.image, this.product_name, data[i].description));


      }
    });
  }



  ionViewDidLoad() {
    console.log('ionViewDidLoad HighlightPage');
  }

  openProduct(idselected) {
    console.log(idselected);
    let prod = [];
    prod = this.products.filter((item => { return (item.idproduct == idselected); }));
    if (prod.length > 0) {
      this.navCtrl.push(ProductPage, {
        product: prod
      });
      console.log(prod);
    }
    else {
      alert("No se ha seleccionado ningún producto.");
    }
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goSettings() {
    this.navCtrl.setRoot(SettingsPage);
  }
}
