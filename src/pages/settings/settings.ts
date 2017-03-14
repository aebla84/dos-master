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
    this.notificationStatus();
  }
  notificationStatus(){
    if(this.globals.notification){
      this.status = "ACTIVADO";
      this.description = "Recibirá todas las notificaciones de las promociones.";
    } else {
      this.status = "DESACTIVADAS";
      this.description = "No recibirá ninguna notificación de las promociones.";
    }
  }
  toggleNotification(e) {
    if (e.checked) {
      this.globals.notification = true;
      this.globals.registerNotifications();
      this.notificationStatus();
    }
    else {
      this.globals.notification = false;
      this.globals.unregisterNotifications();
      this.notificationStatus();
    }
  }
}
