import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'age'
})
export class AgePipe implements PipeTransform {
  transform(value: string): string {
    const birthday = new Date(value).valueOf();
    const today = new Date().valueOf();
    const age = ((today - birthday) / (31557600000));
    return `${Math.floor(age)} y.o.`;
  }
}
