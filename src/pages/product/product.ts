import { Component } from '@angular/core';
import { NavController, NavParams, Platform } from 'ionic-angular';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { ContactPage } from '../contact/contact';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-product',
  templateUrl: 'product.html'
})
export class ProductPage {
  taxonomies = [];
  products = [];
  dataTaxonomyUrl: string;
  dataProductUrl: string;
  parent_name: string;
  name: string;
  subtitle: string;
  description: HTMLElement;
  image: string;
  info: string;
  pReference:string;
  pType : string;
  pDimensions: string;
  pConveyorWidth: string;
  pConveyorLenght: string;
  pConveyorEntry: string;
  pVolume: string;
  pWeight: string;
  pPower: string;
  pVoltage: string;
  pFrequency: string;
  pPrice: string;
  pDetails: string;

  pExtras = [];
  pELabel: string;
  pEReference: string;
  pEPrice: string;

  constructor(public navCtrl: NavController, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform) {
    let loader = this.loadingCtrl.create({
      content: "Cargando...",
      duration: 1500
    });

    this.info = "Características técnicas y PVP";
    loader.present();
    this.dataTaxonomyUrl = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_taxonomy_data_bycategory?idcategory=' + params.get("idCategory");
    this.dataProductUrl = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/get_products_bycategory?idcategory=' + params.get("idCategory");
    this.http.get(this.dataTaxonomyUrl)
      .map(res => res.json())
      .subscribe(data => {
        this.taxonomies[0] = data[0];
        this.parent_name = (this.taxonomies[0].parent_name != null) ? this.taxonomies[0].parent_name : "";
        this.name = (this.taxonomies[0].name != null) ? this.taxonomies[0].name : "";
        this.subtitle = (this.taxonomies[0].subtitle != null) ? this.taxonomies[0].subtitle : "";
        this.description = (this.taxonomies[0].description != null) ? this.taxonomies[0].description : "";
      });
    this.http.get(this.dataProductUrl)
      .map(res => res.json())
      .subscribe(data => {
        for (var i = 0; i < data.length; i++) {
          console.log(data[i].image);
          this.image = (data[i].image != false    && data[i].image != null
            && data[i].image.sizes!= null
             && data[i].image.sizes!= "null"
             && data[i].image.sizes!= "undefined"
             && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";
             this.products.push({ id: data[i].idproduct,
                                  image: this.image,
                                  name: data[i].product.post_title,
                                  pReference : data[i].reference,
                                  pType: data[i].type,
                                  pDimensions: data[i].dimensions,
                                  pConveyorWidth: data[i].conveyor_width,
                                  pConveyorLenght: data[i].conveyor_length,
                                  pConveyorEntry: data[i].conveyor_entry,
                                  pVolume: data[i].volume,
                                  pWeight: data[i].weight,
                                  pPower: data[i].power,
                                  pVoltage: data[i].voltage,
                                  pFrequency: data[i].frequency,
                                  pPrice: data[i].price,
                                  pDetails: data[i].details})

             this.pReference = data[i].reference;
             this.pType = data[i].type;
             this.pDimensions = data[i].dimensions;
             this.pConveyorWidth = data[i].conveyor_width;
             this.pConveyorLenght = data[i].conveyor_length;
             this.pConveyorEntry = data[i].conveyor_entry;
             this.pVolume = data[i].volume;
             this.pWeight = data[i].weight;
             this.pPower = data[i].power;
             this.pVoltage = data[i].voltage;
             this.pFrequency = data[i].frequency;
             this.pPrice = data[i].price;
             this.pDetails = data[i].details;
        }
        console.log(this.products);
      });
  }

  openCatalogPDF() {
    window.location.href = "http://dosilet.deideasmarketing.solutions/wp-content/uploads/2017/01/Diagrama-2-1.pdf";
  }
  openHome() {
    this.navCtrl.setRoot(HomePage);
  }
  goBack() {
    this.navCtrl.pop();
  }
  goContact() {
    this.navCtrl.push(ContactPage);
  }
}
