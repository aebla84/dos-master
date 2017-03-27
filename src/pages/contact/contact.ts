import { Component } from '@angular/core';
import { NavController, NavParams, MenuController } from 'ionic-angular';
import { Http } from "@angular/http";
import { HomePage } from '../home/home';
import { Globals } from '../../providers/globals'
import { SettingsPage } from '../settings/settings';

@Component({
  selector: 'page-contact',
  providers: [Globals],
  templateUrl: 'contact.html'
})
export class ContactPage {
  nameCategory: string;

  constructor(public navCtrl: NavController, public params: NavParams, private http: Http, public globals: Globals, public menuCtrl: MenuController) {
    this.nameCategory = params.get("nameCategory");
    console.log(this.nameCategory);
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack() {
    this.navCtrl.pop();
  }
  goSettings() {
    this.navCtrl.push(SettingsPage);
  }
  closeMenu() {
    this.menuCtrl.close();
  }
}
