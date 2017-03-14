import { Component } from '@angular/core';
import { NavController, NavParams, Platform } from 'ionic-angular';
import {Http, Response} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-category',
  providers: [Globals],
  templateUrl: 'category.html'
})
export class CategoryPage {
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

  //Form product
  fTitle: string;
  fSubject: string;
  fName: string;
  fCompany: string;
  fMailfrom: string;
  fPhone: string;
  fMessage: string;
  url: string;
  loading: boolean;
  loader: any;
  idCategory: string;

  extras = [];
  subextras = [];

  categories = [];

  constructor(public navCtrl: NavController, public globals: Globals, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform) {
    // this.loader = this.loadingCtrl.create({
    //   spinner: 'bubbles',
    //   content: "Cargando..."
    // });
    // this.loader.present();

    this.info = "Características técnicas y PVP";
    this.fTitle = "Solicitar presupuesto";
    this.idCategory = params.get("idCategory");
    this.categories = params.get("categories");
    console.log( this.categories);

    this.setObjects();


    //console.log("uoo");
      //console.log( this.categories);

    //this.getTaxonomyDataByCategory();
    //this.getProductByCategory();
  }

  setObjects(){
    this.parent_name = (this.categories["parent_name"] != null) ?this.categories["parent_name"] : "";
    this.name = (this.categories["name"] != null) ? this.categories["name"] : "";
    this.subtitle = (this.categories["subtitle"] != null) ? this.categories["subtitle"] : "";
    this.description = (this.categories["description"] != null) ? this.categories["description"] : "";
    this.products =  (this.categories["products"] != null) ? this.categories["products"] : "";

    console.log(this.products);
    console.log(this.products[0].extras);
    console.log(this.products[0].extras[0]);
  }
  getTaxonomyDataByCategory(){
    this.globals.getTaxonomyDataByCategory(this.idCategory).subscribe(data => {
      this.taxonomies[0] = data[0];
      this.parent_name = (this.taxonomies[0].parent_name != null) ? this.taxonomies[0].parent_name : "";
      this.name = (this.taxonomies[0].name != null) ? this.taxonomies[0].name : "";
      this.subtitle = (this.taxonomies[0].subtitle != null) ? this.taxonomies[0].subtitle : "";
      this.description = (this.taxonomies[0].description != null) ? this.taxonomies[0].description : "";
    });
  }

  getProductByCategory(){
    this.globals.getProductByCategory(this.idCategory).subscribe(data => {
      this.loader.dismiss();
      for (var i = 0; i < data.length; i++) {
        console.log(data[i].image);
        this.image = (data[i].image != false    && data[i].image != null
          && data[i].image.sizes!= null
           && data[i].image.sizes!= "null"
           && data[i].image.sizes!= "undefined"
           && data[i].image.sizes.medium != "undefined") ? data[i].image.sizes.medium : "";

        // //console.log(data[i].extras);
        // let t = data[i].extras;
        // console.log("t");
        // console.log(t);
        // console.log(t.length);
        // for (var j = 0; j < t.length; j++) {
        //   console.log("t[j].extras");
        //   console.log(t[j].extras);
        //    let ext =  t[j].extras;
        //    console.log(ext.length);
        //    for(var k = 0; k < ext.length; k++) {
        //      console.log("ext[k]");
        //      console.log(ext[k]);
        //       let extext = ext[k];
        //       this.subextras.push({extra_id :ext[k].id, extra_reference :ext[k].reference, extra_price :ext[k].price });
        //    }
        //    this.extras.push({name :  ext.label , lstextras:   this.subextras});
        // }
        //
        // console.log("bea");
        // console.log(this.extras);

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
                                pDetails: data[i].details,
                                pExtras : this.extras})
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
    });
  }

  send(): void {
    this.fName = this.fName;
    this.fCompany = this.fCompany;
    this.fMailfrom = this.fMailfrom;
    this.fPhone = this.fPhone;
    this.fMessage = this.fMessage;
    this.url = 'http://dosilet.deideasmarketing.solutions/wp-json/wp/v2/sendmail';
    var data = new FormData();
    data.append('fName', 'Nuevo mensaje de ' + this.fName);
    data.append('fMessage', this.fMessage);
    data.append('fMailto', 'ocoll@deideasmarketing.com');
    data.append('fMailfrom', this.fMailfrom);
    data.append('fPhone', this.fPhone);
    data.append('fName', this.fName);
    this.loading = true;
    this.http.post(this.url, data)
      .subscribe((res: Response) => {
        data = res.json();
        this.loading = false;
      });

    this.platform.ready().then(() => {
      window.plugins.toast.show("Tu mensaje ha sido enviado. Gracias.", "short", "center");
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
