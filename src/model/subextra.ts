import { Injectable } from '@angular/core';

@Injectable()
export class Subextra {

id: number;
reference: string;
dimensions: string;
price: string;

  constructor(data: {id: number} & {reference: string} & {dimensions: string} & {price: string}) {
    this.id = data.id;
    this.reference = data.reference;
    this.dimensions = data.dimensions;
    this.price = data.price;
  }
}
