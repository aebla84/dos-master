import { Component, ViewChild } from '@angular/core';
import { Nav, Platform, MenuController } from 'ionic-angular';
import { StatusBar, Splashscreen } from 'ionic-native';
import {Http} from '@angular/http';
import 'rxjs/add/operator/map';
import { HomePage } from '../pages/home/home';
import { Push, PushToken } from '@ionic/cloud-angular';
import { CategoryPage } from '../pages/category/category';
import { SettingsPage } from '../pages/settings/settings';
import { HighlightPage } from '../pages/highlight/highlight';
import { ContactPage } from '../pages/contact/contact';
import { Subcategory } from '../model/subcategory';
import { Category } from '../model/category';
import { Globals } from '../providers/globals';
import { AlertController } from 'ionic-angular';

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

  constructor(platform: Platform, private http: Http, public push: Push, public menuCtrl: MenuController, public globals: Globals, private alertCtrl: AlertController) {

    platform.ready().then(() => {
      Splashscreen.show();
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      StatusBar.styleDefault();
      this.getCatalog();
    });

    this.push.register().then((t: PushToken) => {
      globals.saveToken(t.token);
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
    this.subCategories = this.subcategory.find(cat => cat.term_id === id);

    this.nav.push(CategoryPage, {
      idCategory: id,
      nameCategory: name,
      categories: this.subCategories
    });
  }
  openProductCategory(id, name, isCat){
    this.nav.push(CategoryPage, {
      idCategory: id,
      nameCategory: name,
      isCategory: isCat
    })
  }
  toggleDetails(c) {
    if (c.showDetails) {
      c.showDetails = false;
      for(var i = 0; i < this.category.length; i++){
        if(this.category[i].term_id != c.term_id){
          this.category[i].showDetails = true;
        }
      }
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
            this.subcategory.push(new Subcategory("appComponentPage", data[obj]));
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
        Splashscreen.hide();
        console.log(this.category);
      },
      err => { console.log(err) }
    );
  }

  confirmCall(number) {
    let alert = this.alertCtrl.create({
      title: 'Confirmar llamada',
      message: '¿Estás seguro de llamar por teléfono a Dosilet?',
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
          handler: () => {
            console.log('Cancel clicked');
          }
        },
        {
          text: 'Llamar',
          handler: () => {
            window.location = number;
            console.log('Call clicked');
          }
        }
      ]
    });
    alert.present();
  }

  resetMenu(){
    for(var i = 0; i < this.category.length; i++){
        this.category[i].showDetails = true;
    }
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
  goHighlight() {
    this.nav.setRoot(HighlightPage);
  }
  goSettings() {
    this.nav.push(SettingsPage);
  }
}
