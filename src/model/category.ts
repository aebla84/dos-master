import { Injectable } from '@angular/core';
import { Product } from '../model/product';
import { Subcategory } from '../model/subcategory';

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

  subcategories: Array<Subcategory>
  showSubcategories: Boolean;

  constructor(data: {count_products: number } & { term_id: number } & { name: string } & { slug: string } & { term_group: number } & { term_taxonomy_id: number } & { taxonomy: string } & { description: string } & { parent: number } & { count: number } & { filter: string } & { term_order: string } & { showSubcategories: Boolean }) {
    this.count_products = data.count_products;
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

    if (data.count > 0) {
      Object.keys(data).forEach(name => {
        if (name != "name") {
          this.products.push(new Product(data[name]));
        }
      });
    }

    this.subcategories = [];
    this.showSubcategories = true;

    if (data.parent != 0) {
      Object.keys(data).forEach(name => {
        if (name != "name") {
          this.subcategories.push(new Subcategory(data[name]));
        }
      });
    }
    Object.keys(data).forEach(name => {
      if (name != "name") {
        if (this.count > 0) {
          this.products.push(new Product(data[name]));
        }
      }
    });

    // this.subcategories = [];
    this.showSubcategories = true;

    // Object.keys(data).forEach(name => {
    //   if (name != "name") {
    //     this.subcategories.push(new Subcategory(data[name]));
    //   }
    // });
  }
}
