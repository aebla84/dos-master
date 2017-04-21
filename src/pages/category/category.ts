import { Component, ElementRef, ViewChild} from '@angular/core';
import { NavController, NavParams, Platform, Content } from 'ionic-angular';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { SettingsPage } from '../settings/settings';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';
//import { Subcategory } from '../../model/subcategory';
import { Product } from '../../model/product';
import { Slides } from 'ionic-angular';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-category',
  providers: [Globals],
  templateUrl: 'category.html'
})
export class CategoryPage {
  @ViewChild(Slides) slides: Slides;
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

  arrayImgs = [];

  url: string;
  loading: boolean;
  loader: any;
  idCategory: string;
  isCat: boolean;
  lastSlide: boolean;
  firstSlide: boolean;

  fTitle: string;
  extras = [];
  subextras = [];

  categories = [];
  subcategory = [];
  product_name: string;

  dataResults: any;
  dataLength: any;
  counter = 3;
  divider = 0;

  first_product_term: string;
  product_term: string;
  count_products = 0;
  showTypeProductHeader = true;
  @ViewChild(Content) content: Content;

  constructor(public navCtrl: NavController, public globals: Globals, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform, public myElement: ElementRef) {

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

    this.parent_name = (this.categories["parent_name"] != null) ? this.categories["parent_name"] : "";
    this.name = (this.categories["name"] != null) ? this.categories["name"] : "";
    this.subtitle = (this.categories["subtitle"] != null) ? this.categories["subtitle"] : "";
    this.description = (this.categories["description"] != null) ? this.categories["description"] : "";

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
      this.dataResults = data;
      this.dataLength = data.length;
      this.loader.dismiss();



      if (data.length < 3) {
        this.counter = data.length;
      }
      else {
        this.counter = 3;
      }


      for (var i = 0; i < this.counter; i++) {

        this.showTypeProductHeader = (data[i].type_term != false && this.product_term != "" && this.product_term != data[i].type_term) ? true : false;
        this.product_term = data[i].type_term;

        this.product_name = data[i].product['post_title'];
        this.products.push(new Product(data[i], this.image, this.product_name, data[i].description, this.showTypeProductHeader));
      }

      for (var i = 0; i < data.length; i++) {
        this.image = (data[i].image != false && data[i].image != null
          && data[i].image.sizes != null
          && data[i].image.sizes != "null"
          && data[i].image.sizes != "undefined"
          && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";
        this.product_name = data[i].product['post_title'];
        if (this.image != "") {
          this.arrayImgs.push({ name: this.product_name, image: this.image });
        }
      }

    });
    console.log(this.products);
  }
  doInfinite(infiniteScroll) {
    console.log('Begin async operation');

    setTimeout(() => {
      let paramTo_Length = (this.counter + 3 < this.dataLength) ? this.counter + 3 : this.dataLength;

      for (var i = this.counter; i < paramTo_Length; i++) {
        this.showTypeProductHeader = (this.dataResults[i].type_term != false && this.product_term != "" && this.product_term != this.dataResults[i].type_term) ? true : false;
        this.product_term = this.dataResults[i].type_term;

        this.product_name = this.dataResults[i].product['post_title'];
        this.products.push(new Product(this.dataResults[i], this.image, this.product_name, this.dataResults[i].description, this.showTypeProductHeader));
      }
      if (paramTo_Length < this.dataLength) {
        this.counter = this.counter + 3;
      }
      else { this.counter = this.dataLength }

      console.log('Async operation has ended');
      infiniteScroll.complete();
    }, 500);
  }

  pedirPresupuesto(nameCategory) {
    this.navCtrl.push(ContactPage, {
      nameCategory: this.name
    });
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
  scrollToTop() {
    this.content.scrollToTop();
  }
  prevSlide() {
    this.slides.slidePrev(500, true);
  }
  nextSlide() {
    this.slides.slideNext(500, true);
  }
}
