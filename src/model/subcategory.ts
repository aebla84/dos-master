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

  constructor(data: { term_id: number } & { name: string } & { slug: string } & { term_group: number } & { term_taxonomy_id: number } & { taxonomy: string } & { description: string } & { parent: number } & { count: number } & { filter: string } & { term_order: string } & { showProducts: Boolean }) {
    this.term_id = data.term_id;
    this.name = data.name;
    this.slug = data.slug;
    this.term_group = data.term_group;
    this.term_taxonomy_id = data.term_taxonomy_id;
    this.taxonomy = data.taxonomy;
    this.description = data.description;
    this.parent = data.parent;
    this.count = data.count;
    this.filter = data.filter;
    this.term_order = data.term_order;

    this.products = [];
    this.showProducts = true;

    Object.keys(data).forEach(name => {
      if(name != "name"){
        this.products.push( new Product(data[name]));
      }
    });
  }
}
