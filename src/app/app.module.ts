import { NgModule, ErrorHandler } from '@angular/core';
import { IonicApp, IonicModule, IonicErrorHandler } from 'ionic-angular';
import { MyApp } from './app.component';
import { ContactPage } from '../pages/contact/contact';
import { ProductPage } from '../pages/product/product';
import { HomePage } from '../pages/home/home';
import { CategoryPage } from '../pages/category/category';
import { TabsPage } from '../pages/tabs/tabs';
import { CloudSettings, CloudModule } from '@ionic/cloud-angular';
import { HighlightPage } from '../pages/highlight/highlight';
import { SettingsPage } from '../pages/settings/settings';
// import { Storage } from '@ionic/storage';

const cloudSettings: CloudSettings = {
  'core': {
    'app_id': 'f7269dff'
  },
  'push': {
    'sender_id': '522225482273',
    'pluginConfig': {
      'ios': {
        'badge': true,
        'sound': true
      },
      'android': {
        'iconColor': '#343434'
      }
    }
  }
};

@NgModule({
  declarations: [
    MyApp,
    ContactPage,
    ProductPage,
    HighlightPage,
    HomePage,
    TabsPage,
    SettingsPage,
    CategoryPage
  ],
  imports: [
     IonicModule.forRoot(MyApp,{
       backButtonText: '',
       backButtonIcon: 'ios-arrow-back',
       iconMode: 'md'
     }),
     CloudModule.forRoot(cloudSettings)
   ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    ContactPage,
    ProductPage,
    HighlightPage,
    HomePage,
    TabsPage,
    SettingsPage,
    CategoryPage
  ],
  // providers: [{ provide: ErrorHandler, useClass: IonicErrorHandler },Storage]
   providers: [{ provide: ErrorHandler, useClass: IonicErrorHandler }]
})
export class AppModule { }
