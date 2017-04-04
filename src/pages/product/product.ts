import { Component } from '@angular/core';
import { NavController, NavParams, Platform } from 'ionic-angular';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-product',
  providers: [Globals],
  templateUrl: 'product.html'
})
export class ProductPage {
  taxonomies = [];
  products = [];
  dataTaxonomyUrl: string;
  dataProductUrl: string;
  parent_name: string;
  name: string;
  subtitle: string;
  description: HTMLElement;
  image: string;
  productShow = this.params.get('product');
  test2: string;

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

  aux = [];
  productextra = [];
  productextra2 = [];

  extra_id: string;
  extra_reference: string;
  extra_price: string;
  extra_dimensions: string;
  extra_name: string;

  info: string;
  constructor(public navCtrl: NavController, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform) {

    //Constants
    this.info = "Características técnicas y PVP";
    this.products = this.productShow;
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad Product2Page');
  }

  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack() {
    this.navCtrl.pop();
  }
  goContact() {
    this.navCtrl.push(ContactPage);
  }

  pedirPresupuesto(nameCategory) {
    this.navCtrl.push(ContactPage, {
      nameCategory: this.name
    });
  }
}
