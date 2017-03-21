import { Component } from '@angular/core';
import { NavController, NavParams, MenuController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { Globals } from '../../providers/globals';
import { Storage } from '@ionic/storage';

@Component({
  selector: 'page-settings',
  providers: [Globals],
  templateUrl: 'settings.html'
})
export class SettingsPage {
  status: string;
  description: string;
  storage: any;
  notification: Boolean;
  // firstTime: Boolean;

  constructor(public globals: Globals, public navCtrl: NavController, public navParams: NavParams, public menuCtrl: MenuController) {
    this.storage = new Storage();

    // He comentado la manera que estaba intentando hacer que cuando instalas por primera vez la App, el valor de las notificaciones
    // sea true.

    this.storage.ready().then(() => {
      // this.storage.get('firstTime').then((val) => {
      //   this.firstTime = val;
      //   console.log('First Time: ', val);
      // })

      // if(!this.firstTime){
      this.storage.get('notification').then((val) => {
        this.notification = val;
        console.log('Notifications are: ', val);
      })
      // }
    });

    this.checkStatus();
  }

  checkStatus() {
    if (this.notification) {
      this.status = "ACTIVADO";
      this.description = "Recibirá todas las notificaciones de las promociones.";
    }
    else {
      this.status = "DESACTIVADAS";
      this.description = "No recibirá ninguna notificación de las promociones.";
    }
  }

  toggleNotification(e) {
    if (e.checked) {
      this.globals.registerNotifications();
      this.storage.set('notification', 'true');
      // this.storage.set('firstTime', 'true');
      this.checkStatus();
    }
    else {
      this.globals.unregisterNotifications();
      this.storage.set('notification', 'false');
      // this.storage.set('firstTime', 'true');
      this.checkStatus();
    }
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
