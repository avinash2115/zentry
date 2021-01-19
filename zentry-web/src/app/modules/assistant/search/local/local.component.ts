import { Component, OnInit } from '@angular/core';
import { SearchService } from '../search.service';
import { takeUntil } from 'rxjs/operators';
import { BaseDestroyableComponent } from '../../../../shared/classes/abstracts/component/base-destroyable-component';

@Component({
    selector: 'app-assistant-search-local',
    templateUrl: './local.component.html',
    styleUrls: ['./local.component.scss']
})
export class LocalComponent extends BaseDestroyableComponent implements OnInit {
    public term: string | null = null;

    constructor(
        protected searchService: SearchService
    ) {
        super();
    }

    ngOnInit(): void {
        this.searchService
            .term
            .pipe(takeUntil(this._destroy$))
            .subscribe((term: string) => this.term = term);
    }

    submit(): void {
        this.searchService.init({term: this.term || null}).subscribe();
    }

    reset(): void {
        this.term = null;
        this.submit();
    }

    termChange(value: string): void {
        this.term = value;
    }
}
