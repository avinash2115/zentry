import { FormControl, ValidationErrors } from '@angular/forms';

export function WhitespaceValidator(control: FormControl): ValidationErrors | null {
    const isWhitespace: boolean = (control.value || '').trim().length === 0;
    const isValid: boolean = !isWhitespace;

    return isValid ? null : {'whitespace': true};
}
