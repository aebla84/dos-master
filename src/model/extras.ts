import { Injectable } from '@angular/core';

@Injectable()
export class Extras {

name: string;

  constructor(data: {name: string}) {}
}
