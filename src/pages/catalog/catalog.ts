import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { HomePage } from '../home/home';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { ProductPage } from '../product/product';
import { ContactPage } from '../contact/contact';
@Component({
  selector: 'page-catalog',
  templateUrl: 'catalog.html'
})
export class CatalogPage {
  selectedItem: string;
  productCategory: string;
  subCategories = [];
  prodCategories = [];
  url: string;
  products = [];
  allCategories = [];
  categories = [];
  pages: Array<{ id: any, title: string, component: any, parent: any, subcategories: Array<{}> }>;
  categoriesUrl: string;
  isCat: boolean;
  constructor(public navCtrl: NavController, private http: Http, public loadingCtrl: LoadingController) { }
  ionViewDidLoad() {
  // GET al Wordpress para obtener y guardar las categorías.
    let loader = this.loadingCtrl.create({
      content: "Cargando...",
      duration: 1500
    });
    loader.present();
    this.categories = [];
    this.categoriesUrl = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/gettaxonomies';
    this.http.get(this.categoriesUrl)
      .map(res => res.json())
      .subscribe(data => {
        for (var i = 0; i < data.length; i++) {
          this.allCategories[i] = data[i];
          if (data[i].parent == 0) {
            this.categories.push({ id: data[i].term_id, title: data[i].name, slug: data[i].slug,  items: [] , showDetails: Boolean});
          }
        }
        for (var i = 0; i < data.length; i++) {
              for (var j = 0; j < this.categories.length; j++) {
                  if(data[i].parent == this.categories[j].id)
                  {
                    this.categories[j].items.push({id: data[i].term_id, title: data[i].name, slug: data[i].slug });
                  }
              }
        }
      });
      console.log(this.categories);
  }
  openProduct(id, name) {
    this.navCtrl.push(ProductPage, {
      idCategory: id,
      nameCategory: name
    });
  }
  // openProduct(id, title, image, electronicorgas, mm, ancho_banda, largo_banda, h_boca_entrada, m3, kg, kw, v, hz, pvp, observaciones) {
  //   console.log(id)
  //   this.navCtrl.push(ProductPage, {
  //     id: id, title: title, image: image, electronicorgas: electronicorgas, mm: mm, ancho_banda: ancho_banda, largo_banda: largo_banda,
  //     h_boca_entrada: h_boca_entrada, m3: m3, kg: kg, kw: kw, v: v, hz: hz, pvp: pvp, observaciones: observaciones
  //   });
  // }
  toggleDetails(c) {
    if (c.showDetails) {
        c.showDetails = false;
    } else {
        c.showDetails = true;
    }
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack(){
    this.navCtrl.pop();
  }
  goContact() {
    this.navCtrl.push(ContactPage);
  }
}
