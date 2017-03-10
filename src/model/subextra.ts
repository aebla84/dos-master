import { Injectable } from '@angular/core';

@Injectable()
export class Subextra {

id: number;
reference: string;
price: string;

  constructor(data: {id: number} & {reference: string} & {price: string}) {
    this.id = data.id;
    this.reference = data.reference;
    this.price = data.price;
  }
}
