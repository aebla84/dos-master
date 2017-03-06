import { Component } from '@angular/core';

import { HomePage } from '../home/home';
import { CatalogPage } from '../catalog/catalog';
import { ContactPage } from '../contact/contact';
import { NavController } from 'ionic-angular';

@Component({
  templateUrl: 'tabs.html'
})
export class TabsPage {
  // this tells the tabs component which Pages
  // should be each tab's root Page
  tab1Root: any = HomePage;
  tab2Root: any = CatalogPage;
  tab3Root: any = ContactPage;

  constructor(public navCtrl: NavController) {

  }
}
