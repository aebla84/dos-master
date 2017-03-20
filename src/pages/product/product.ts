import { Component } from '@angular/core';
import { NavController, NavParams, Platform } from 'ionic-angular';
import {Http} from '@angular/http';
import { LoadingController } from 'ionic-angular';
import { HomePage } from '../home/home';
import { ContactPage } from '../contact/contact';
import { Globals } from '../../providers/globals';

declare var cordova: any;
declare var window: any;

@Component({
  selector: 'page-product',
  providers: [Globals],
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
  productShow =  this.params.get('product');
  test2:string;

  reference : string;
  type :string;
  dimensions : string;
  conveyor_width :string;
  conveyor_length : string;
  conveyor_entry :string;
  volume : string;
  weight :string;
  power : string;
  voltage : string;
  frequency :string;
  price : string;
  details : string;
  extras = [];

   aux =[];
   productextra = [];
   productextra2 = [];

   extra_id : string;
   extra_reference : string;
   extra_price:string;
   extra_dimensions:string;
   extra_name : string;

   info : string;
  constructor(public navCtrl: NavController, private http: Http, public params: NavParams, public loadingCtrl: LoadingController, public platform: Platform) {

    //Constants
    this.info = "Características técnicas y PVP";


    this.products = this.productShow;
    //console.log("beaprod");
    //console.log(this.products[0].extras);

    this.aux = this.products[0].extras;
    console.log("aux");
    console.log(this.aux);
    this.addextras(this.aux);


    //this.products[0].extras.extras = this.productextra;
    //console.log(this.products[0].extras);
    console.log( this.productextra);
    console.log(this.products);

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad Product2Page');
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

    addextras(aux){
      for(var i=0; i<aux.length; i++) {
        let t = aux[i].extras;
        console.log("ttt");
        console.log(t);

        for(var j=0; j<t.length; j++) {
          let ext2 = t[j];
          this.productextra.push({extra_name : aux[i].label, extra_id :ext2.id, extra_reference :ext2.reference,  extra_dimensions :ext2.dimensions, extra_price :ext2.price });
        }
      }



      // let name = this.productextra[0].extra_name;
      // this.productextra.forEach(element => {
      //         if (element.extra_name == name) {
      //               this.productextra2.push({extra_name : element.extra_name ,  })
      //             }
      //         });
      //         return this.commentspart;
      // this.productextra2.push
    }


}
