import { Injectable } from '@angular/core';
import { Product } from '../model/product';
// import { Subcategory } from '../model/subcategory';

@Injectable()
export class Category {
  count_products: number;
  description: string;
  name: string;
  parent: number;
  parent_name: string;
  slug: string;
  subtitle: string;
  term_id: number;
  products: Array<Product>

  // subcategories: Array<Subcategory>
  // showSubcategories: Boolean;

  constructor(data: {count_products: number } & {description: string } & { name: string } & { parent: number } & { parent_name: string } & { slug: string } & { subtitle: string } & { term_id: number }
    & { showSubcategories: Boolean } &   {products: Array<Product>} ) {

    this.count_products = data.count_products;
    this.description = data.description;
    this.name = data.name;
    this.parent = data.parent;
    this.parent_name = data.parent_name;
    this.slug = data.slug;
    this.subtitle = data.subtitle;
    this.term_id = data.term_id;

    this.products = [];

    if (data.count_products > 0) {
      let t = data.products;
      Object.keys(t).forEach(prod => {
        //if (name != "name") {
         //console.log(t);
          this.products.push(new Product(t[prod]));
        //}
      });
      // for(var i = 1; i <= data.products.length; i++){
      // {
      //     console.log(i.products);
      // }
    }

     console.log(this.products);

    // this.subcategories = [];
    // this.showSubcategories = true;
    //
    // if (data.parent != 0) {
    //   Object.keys(data).forEach(name => {
    //     if (name != "name") {
    //       this.subcategories.push(new Subcategory(data[name]));
    //     }
    //   });
    // }
    // Object.keys(data).forEach(name => {
    //   if (name != "name") {
    //     if (this.count_products > 0) {
    //       this.products.push(new Product(data[name]));
    //     }
    //   }
    // });

    // this.subcategories = [];
    // this.showSubcategories = true;

    // Object.keys(data).forEach(name => {
    //   if (name != "name") {
    //     this.subcategories.push(new Subcategory(data[name]));
    //   }
    // });
  }
}
