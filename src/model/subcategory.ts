import { Injectable } from '@angular/core';
import { Product } from '../model/product';

@Injectable()
export class Subcategory {
  term_id: number;
  name: string;
  slug: string;
  term_group: number;
  term_taxonomy_id: number;
  taxonomy: string;
  description: string;
  parent: number;
  count: number;
  filter: string;
  term_order: string;

  products: Array<Product>
  showProducts: Boolean;

  constructor(data: { name: string } & { showProducts: Boolean }) {
    this.name = data.name;
    this.products = [];
    this.showProducts = true;

    Object.keys(data).forEach(name => {
      if(name != "name"){
        this.products.push( new Product(data[name]));
      }
    });
  }
}
