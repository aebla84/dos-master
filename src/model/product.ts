import { Injectable } from '@angular/core';
import { Extras } from '../model/extras';

@Injectable()
export class Product {
  idproduct: number;
  reference: string;
  type: string;
  dimensions: string;
  conveyor_width: string;
  conveyor_length: string;
  conveyor_entry: string;
  volume: string;
  weight: string;
  power: string;
  voltage: string;
  frequency: string;
  price: string;
  details: string;
  extras: Array<Extras>

  constructor(data: { idproduct: number } & {reference: string} & {type: string} & {dimensions: string} & { conveyor_width: string } & {conveyor_length: string} & {conveyor_entry: string} & {volume: string} & {weight: string} & {power: string} & {voltage: string} & {frequency: string} & {price: string} & {details: string}) {
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
    this.extras = [];

    Object.keys(data).forEach(name => {
      if(name != "name"){
        this.extras.push( new Extras(data[name]));
      }
    });
  }
}
