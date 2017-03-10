import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { CatalogPage } from '../catalog/catalog';
import { HighlightPage } from '../highlight/highlight';
import { Globals } from '../../providers/globals';
import { Subcategory } from '../../model/subcategory';
import { Category } from '../../model/category';

@Component({
  selector: 'page-home',
  providers: [Globals],
  templateUrl: 'home.html'
})
export class HomePage {
  category =  [];
  subcategory = [];
  constructor(public navCtrl: NavController, public globals: Globals) {
    this.getCatalog();
  }
  getCatalog() {
    this.globals.getCatalog().subscribe(
      data => {

        this.category =  new Array<Category>();
        this.subcategory = new Array<Subcategory>();

        Object.keys(data).forEach(obj => {
          if (data[obj].parent != 0) {
            this.subcategory.push(new Subcategory(data[obj]));
          }
          else{
            this.category.push(new Category(data[obj]));
          }
        });

        for(var i= 0; i< this.category.length; i++)
        {
          for(var j= 0; j< this.subcategory.length; j++)
          {
            if((this.subcategory[j])['parent'] != undefined)
            {

               let idparent = (this.subcategory[j])['parent'];
              if( idparent ==  this.category[i].term_id)
              {
                  this.category[i].subcategories.push(this.subcategory[j]);
              }
            }
          }
        }
        console.log(this.category);
      },
      err => { console.log(err) }
    );




  }
  goCatalog() {
    this.navCtrl.push(CatalogPage);
  }
  goContact() {
    this.navCtrl.push(HighlightPage);
  }
}
