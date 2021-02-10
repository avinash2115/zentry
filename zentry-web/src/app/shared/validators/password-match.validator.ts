import { AbstractControl, FormGroup, ValidatorFn } from '@angular/forms';

export function PasswordMathValidator(name: string, nameRepeated: string): ValidatorFn {
    return (control: FormGroup) => {
        const password: AbstractControl = control.get(name);
        const repeatedPassword: AbstractControl = control.get(nameRepeated);

        if (repeatedPassword.value && password.value !== repeatedPassword.value) {
            return {'mismatch': true};
        }

        return null;
    };
}
