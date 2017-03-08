import { Injectable } from '@angular/core';
import { Subcategory } from '../model/subcategory';

@Injectable()
export class Category {
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

  subcategories: Array<Subcategory>
  showSubcategories: Boolean;

  constructor(data: { name: string } & { showSubcategories: Boolean }) {
    this.name = data.name;
    this.subcategories = [];
    this.showSubcategories = true;

    Object.keys(data).forEach(name => {
      if(name != "name"){
        this.subcategories.push( new Subcategory(data[name]));
      }
    });
  }
}
