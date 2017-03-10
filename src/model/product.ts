import { Injectable } from '@angular/core';
import 'rxjs/add/operator/map';
import { Extra } from '../model/extra';

@Injectable()
export class Product {
  idproduct: number;
  conveyor_entry: string;
  conveyor_length: string;
  conveyor_width: string;
  count_extras: number;
  details: string;
  dimensions: string;
  frequency: string;
  reference: string;
  type: string;
  volume: string;
  weight: string;
  power: string;
  price: string;
  voltage: string;
  extras: Array<Extra>


  constructor(data: { extras: Array<Extra> } & { idproduct: number } & { reference: string } & { type: string } & { dimensions: string } & { conveyor_width: string } & { conveyor_length: string } &
    { conveyor_entry: string } & { volume: string } & { weight: string } & { power: string } &
    { voltage: string } & { frequency: string } & { price: string } & { details: string } & { count_extras: number }) {
    this.idproduct = data.idproduct;
    this.reference = data.reference;
    this.type = data.type;
    this.dimensions = data.dimensions;
    this.conveyor_width = data.conveyor_width;
    this.conveyor_length = data.conveyor_length;
    this.conveyor_entry = data.conveyor_entry;
    this.volume = data.volume;
    this.weight = data.weight;
    this.power = data.power;
    this.voltage = data.voltage;
    this.frequency = data.frequency;
    this.price = data.price;
    this.details = data.details;
    this.count_extras = data.count_extras;
    this.extras = [];

    if (data.count_extras > 0) {
      let extra = data.extras;
      Object.keys(extra).forEach(ext => {
          this.extras.push(new Extra(extra[ext]));
      });
    }
  }
}
