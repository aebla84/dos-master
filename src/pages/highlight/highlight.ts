
import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import {Http} from '@angular/http';
import { ProductPage } from '../product/product';
import { Globals } from '../../providers/globals';
import { LoadingController } from 'ionic-angular';

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

        this.products.push({
          id: data[i].idproduct, image: this.image, name: data[i].product['post_title'], description: data[i].description, reference: data[i].reference,
          type: data[i].type, dimensions: data[i].dimensions, conveyor_width: data[i].conveyor_width, conveyor_length: data[i].conveyor_length,
          conveyor_entry: data[i].conveyor_entry, volume: data[i].volume, weight: data[i].weight, power: data[i].power, voltage: data[i].voltage,
          frequency: data[i].frequency, price: data[i].price, details: data[i].details, extras: data[i].extras
        });
      }
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad HighlightPage');
  }

  openProduct(idselected) {
    this.navCtrl.push(ProductPage, {
      product: this.products.filter((item => { return (item.id == idselected); }))
    });
  }

}
