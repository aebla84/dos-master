import { Injectable } from '@angular/core';
import { Subextra } from '../model/subextra';

@Injectable()
export class Extra {

  label: string;
  extras: Array<Subextra>;

  constructor(data: { extras: Array<Subextra> } & { label: string }) {
    this.label = data.label;
    this.extras = [];

    let subextra = data.extras;
    Object.keys(subextra).forEach(ext => {
      this.extras.push(new Subextra(subextra[ext]));
    });
  }
}
