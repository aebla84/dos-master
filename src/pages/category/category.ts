import { Component } from '@angular/core';
import { NavController, NavParams, Platform } from 'ionic-angular';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { SettingsPage } from '../settings/settings';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';
//import { Subcategory } from '../../model/subcategory';
import { Product } from '../../model/product';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-category',
  providers: [Globals],
  templateUrl: 'category.html'
})
export class CategoryPage {
  taxonomies = [];
  products = [];
  dataTaxonomyUrl: string;
  dataProductUrl: string;
  parent_name: string;
  name: string;
  subtitle: string;
  description: HTMLElement;
  image: string;
  info: string;
  pReference: string;
  pType: string;
  pDimensions: string;
  pConveyorWidth: string;
  pConveyorLenght: string;
  pConveyorEntry: string;
  pVolume: string;
  pWeight: string;
  pPower: string;
  pVoltage: string;
  pFrequency: string;
  pPrice: string;
  pDetails: string;
  arrayImgs = [];
  //Form product
  fTitle: string;
  fSubject: string;
  fName: string;
  fCompany: string;
  fMailfrom: string;
  fPhone: string;
  fMessage: string;
  url: string;
  loading: boolean;
  loader: any;
  idCategory: string;
  isCat: boolean;
  lastSlide: boolean;
  firstSlide: boolean;

  extras = [];
  subextras = [];

  categories = [];
  subcategory = [];
  product_name: string;

  dataLength: any;
  counter = 3;


  constructor(public navCtrl: NavController, public globals: Globals, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform) {

    this.loader = this.loadingCtrl.create({
      spinner: 'bubbles',
      content: "Cargando..."
    });
    this.loader.present();
    this.info = "Características técnicas y PVP";
    this.fTitle = "Solicitar presupuesto";
    this.idCategory = params.get("idCategory");
    this.categories = params.get("categories");
    this.isCat = params.get("isCategory");
    console.log(this.isCat);
    console.log(this.categories);

    this.setObjects();
    this.getProductByCategory();

    this.lastSlide = false;
    this.firstSlide = true;
  }

  setObjects() {
    if(!this.isCat){
      this.parent_name = (this.categories["parent_name"] != null) ? this.categories["parent_name"] : "";
      this.name = (this.categories["name"] != null) ? this.categories["name"] : "";
      this.subtitle = (this.categories["subtitle"] != null) ? this.categories["subtitle"] : "";
      this.description = (this.categories["description"] != null) ? this.categories["description"] : "";
      //this.products =  (this.categories["products"] != null) ? this.categories["products"] : "";
    } 
  }

  toggleDetails(p) {
    if (p.showExtras && p.count_extras > 0) {
      p.showExtras = false;
    } else {
      p.showExtras = true;
    }
  }

  getProductByCategory() {
    this.globals.getProductByCategory(this.idCategory).subscribe(data => {
      this.dataLength = data.length;
      this.loader.dismiss();

      // Si hay menos de 3 productos para mostrar o hay 3 o más
      if(data.length < 3){
        this.counter = data.length;
      } else {
        this.counter = 3;
      }

      for (var i = 0; i < this.counter; i++) {
        this.image = (data[i].image != false && data[i].image != null
          && data[i].image.sizes != null
          && data[i].image.sizes != "null"
          && data[i].image.sizes != "undefined"
          && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";
        this.product_name = data[i].product['post_title'];
        this.products.push(new Product(data[i], this.image, this.product_name, data[i].description));
        if(this.image != ""){
          this.arrayImgs.push({name: this.product_name, image: this.image});
        }
      }
    });
  }
  doInfinite(infiniteScroll) {
  console.log('Begin async operation');

  setTimeout(() => {
    this.globals.getProductByCategory(this.idCategory).subscribe(data => {
      this.loader.dismiss();
      if(this.counter == data.length){
        return false;
      }
      for (var i = this.counter; i < this.counter+3; i++) {

        this.image = (data[i].image != false && data[i].image != null
          && data[i].image.sizes != null
          && data[i].image.sizes != "null"
          && data[i].image.sizes != "undefined"
          && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";
        this.product_name = data[i].product['post_title'];
        this.products.push(new Product(data[i], this.image, this.product_name, data[i].description));
        if(this.image != ""){
          this.arrayImgs.push({name: this.product_name, image: this.image});
        }
      }
      this.counter = this.counter+3;
    });

    console.log('Async operation has ended');
    infiniteScroll.complete();
  }, 500);
}

  pedirPresupuesto(){
    this.navCtrl.push(ContactPage);
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goSettings() {
    this.navCtrl.setRoot(SettingsPage);
  }
  slideChanged(Slides) {
    this.firstSlide = Slides.isBeginning();
    this.lastSlide = Slides.isEnd();
  }
}
