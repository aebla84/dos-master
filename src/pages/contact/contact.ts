import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import {Http, Response} from "@angular/http";
import { HomePage } from '../home/home';
import { Globals } from '../../providers/globals'
@Component({
  selector: 'page-contact',
  providers: [Globals],
  templateUrl: 'contact.html'
})
export class ContactPage {
  constructor(public navCtrl: NavController, private http: Http, public globals: Globals) {}
  openHome() { this.navCtrl.setRoot(HomePage); }
  goBack() { this.navCtrl.pop(); }
}
