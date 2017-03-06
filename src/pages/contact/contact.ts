import { Component } from '@angular/core';
import { NavController, Platform } from 'ionic-angular';
import {Http, Response} from "@angular/http";
import { HomePage } from '../home/home';
import { CatalogPage } from '../catalog/catalog';
declare var window: any;
@Component({
  selector: 'page-contact',
  templateUrl: 'contact.html'
})
export class ContactPage {
  subject: string;
  name: string;
  mailfrom: string;
  phone: string;
  message: string;
  loading: boolean;
  url: string;
  constructor(public navCtrl: NavController, private platform: Platform, private http: Http) {
    this.platform = platform;
  }
  send(): void {
    this.subject = this.subject;
    this.name = this.subject;
    this.message = this.message;
    this.phone = this.phone;
    this.mailfrom = this.mailfrom;
    this.url = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/sendmail';
    var data = new FormData();
    data.append('subject', 'Nuevo mensaje de ' + this.subject);
    data.append('message', this.message);
    data.append('mailto', 'jbono@deideasmarketing.com');
    data.append('mailfrom', this.mailfrom);
    data.append('phone', this.phone);
    data.append('name', this.name);
    this.loading = true;
    this.http.post(this.url, data)
      .subscribe((res: Response) => {
        data = res.json();
        this.loading = false;
      });
    //document.getElementById("contactForm").reset();
    this.platform.ready().then(() => {
      window.plugins.toast.show("Tu mensaje ha sido enviado. Gracias.", "short", "center");
    });
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack(){
    this.navCtrl.pop();
  }
  goCatalog() {
    this.navCtrl.push(CatalogPage);
  }
}
