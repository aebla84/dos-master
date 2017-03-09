import { Injectable } from '@angular/core';
import { Subextras } from '../model/subextras';

@Injectable()
export class Extras {

  label: string;
  subextras: Array<Subextras>

  constructor(data: { label: string }) {
    this.label = data.label;
    this.subextras = [];

    Object.keys(data).forEach(label => {
      if(label != "label"){
        this.subextras.push( new Subextras(data[label]));
      }
    });
  }
}
