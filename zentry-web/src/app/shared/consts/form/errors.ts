import { InjectionToken } from '@angular/core';

export const defaultErrors = {
    required: (error: string) => `This field is required`,
    minlength: ({requiredLength, actualLength}: { requiredLength: number, actualLength: number }) => `Minimum of ${requiredLength} characters required`,
    maxlength: ({requiredLength, actualLength}: { requiredLength: number, actualLength: number }) => `Maximum of ${requiredLength} characters is reached`,
    email: (error: string) => `Please provide a valid email`,
    pattern: (error: string) => `Please provide a valid email`,
    whitespace: (error: string) => `Please provide valid value`
};

export const FORM_ERRORS = new InjectionToken('FORM_ERRORS', {
    providedIn: 'root',
    factory: () => defaultErrors
});
