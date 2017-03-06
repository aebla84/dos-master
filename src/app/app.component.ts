import { Component, ViewChild } from '@angular/core';
import { Nav, Platform,MenuController } from 'ionic-angular';
import { StatusBar } from 'ionic-native';
import {Http} from '@angular/http';
import 'rxjs/add/operator/map';
import { HomePage } from '../pages/home/home';
import { Push, PushToken } from '@ionic/cloud-angular';
import { CatalogPage } from '../pages/catalog/catalog';
import { LoadingController } from 'ionic-angular';
import { ProductPage } from '../pages/product/product';
import { ContactPage } from '../pages/contact/contact';

@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  @ViewChild(Nav) nav: Nav;
  rootPage = HomePage;

  //catalog
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

  constructor(platform: Platform, private http: Http, public push: Push, public loadingCtrl: LoadingController, public menuCtrl: MenuController ) {
    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      StatusBar.styleDefault();
      this.ionViewDidLoad();
    });

    this.push.register().then((t: PushToken) => {
      return this.push.saveToken(t);
    }).then((t: PushToken) => {
      console.log('Token saved:', t.token);
    });

    this.push.rx.notification()
      .subscribe((msg) => {
      // para navegar a cierta página al clicar en la notificación.
      // this.nav.push(CatalogPage);
        alert(msg.title + ': ' + msg.text);
      });


  }
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
    this.nav.push(ProductPage, {
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
      this.nav.setRoot(HomePage);
    }
    goBack(){
      this.nav.pop();
    }
    goContact() {
      this.nav.push(ContactPage);
    }
    openMenu() {
      this.menuCtrl.open();
    };

    closeMenu() {
      this.menuCtrl.close();
    };
}
