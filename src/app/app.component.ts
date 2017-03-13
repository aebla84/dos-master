import { Component, ViewChild } from '@angular/core';
import { Nav, Platform, MenuController } from 'ionic-angular';
import { StatusBar } from 'ionic-native';
import {Http} from '@angular/http';
import 'rxjs/add/operator/map';
import { HomePage } from '../pages/home/home';
import { Push, PushToken } from '@ionic/cloud-angular';
import { ProductPage } from '../pages/product/product';
import { SettingsPage } from '../pages/settings/settings';
import { HighlightPage } from '../pages/highlight/highlight';
import { ContactPage } from '../pages/contact/contact';
import { Subcategory } from '../model/subcategory';
import { Category } from '../model/category';
import { Globals } from '../providers/globals';

declare var window;

@Component({
  templateUrl: 'app.html',
  providers: [Globals]
})
export class MyApp {
  @ViewChild(Nav) nav: Nav;
  rootPage = HomePage;
  category = [];
  subcategory = [];

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

  constructor(platform: Platform, private http: Http, public push: Push, public menuCtrl: MenuController, public globals: Globals) {

    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      this.getCatalog();
      StatusBar.styleDefault();
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

  openProduct(id, name) {
    this.nav.push(ProductPage, {
      idCategory: id,
      nameCategory: name
    });
  }

  toggleDetails(c) {
    if (c.showDetails) {
      c.showDetails = false;
    } else {
      c.showDetails = true;
    }
  }

  getCatalog() {
    this.globals.getCatalog().subscribe(
      data => {
        this.category = new Array<Category>();
        this.subcategory = new Array<Subcategory>();

        Object.keys(data).forEach(obj => {
          if (data[obj].parent != 0) {
            this.subcategory.push(new Subcategory(data[obj]));
          }
          else {
            this.category.push(new Category(data[obj]));
          }
        });

        for (var i = 0; i < this.category.length; i++) {
          for (var j = 0; j < this.subcategory.length; j++) {
            if ((this.subcategory[j])['parent'] != undefined) {

              let idparent = (this.subcategory[j])['parent'];
              if (idparent == this.category[i].term_id) {
                this.category[i].subcategories.push(this.subcategory[j]);
              }
            }
          }
        }
        console.log(this.category);
      },
      err => { console.log(err) }
    );
  }

  openHome() {
    this.nav.setRoot(HomePage);
  }
  goBack() {
    this.nav.pop();
  }
  goContact() {
    this.nav.push(ContactPage);
  }
  openMenu() {
    this.menuCtrl.open();
  };
  goHighlight() {
    this.nav.setRoot(HighlightPage);
  }
  goSettings() {
    this.nav.push(SettingsPage);
  }
  call(number) {
    window.location = number;
  }
  closeMenu() {
    this.menuCtrl.close();
  }
}
