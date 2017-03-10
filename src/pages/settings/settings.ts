import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { Globals } from '../../providers/globals';


@Component({
  selector: 'page-settings',
  providers: [Globals],
  templateUrl: 'settings.html'
})
export class SettingsPage {
  status: string;
    description: string;

    constructor(public globals: Globals, public navCtrl: NavController, public navParams: NavParams) {
      this.status = "DESACTIVADAS";
      this.description = "No recibirá ninguna notificación de las promociones.";
      console.log(this.globals.notification);
    }
    ionViewDidLoad() {
      console.log('ionViewDidLoad AdminNotificationsPage');
    }
    toggleNotification(e) {
      if (e.checked) {
        this.globals.registerNotifications();
        this.status = "ACTIVADO";
        this.description = "Recibirá todas las notificaciones de las promociones.";
      }
      else {
        this.globals.unregisterNotifications();
        this.status = "DESACTIVADAS";
        this.description = "No recibirá ninguna notificación de las promociones.";
      }
    }

}
