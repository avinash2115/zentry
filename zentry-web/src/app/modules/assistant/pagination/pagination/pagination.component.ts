import { ChangeDetectorRef, Component, ElementRef, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { PerfectScrollbarComponent, PerfectScrollbarConfigInterface } from 'ngx-perfect-scrollbar';
import { finalize, take, takeUntil } from 'rxjs/operators';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { throttle } from '../../../../shared/decorators/throttleable.decorator';
import { ResizeSensor } from 'css-element-queries';
import { UtilsService } from '../../../../shared/services/utils.service';
import { PaginationService } from '../pagination.service';
import { IBagState } from '../abstractions/base.abstract';

const FOOTER_HEIGHT: number = 24;
const CARD_BOTTOM_MARGIN: number = 30;
const MIN_SCROLLABLE_AREA: number = 550;

enum EDirection {
    up = 'up',
    down = 'down'
}

@Component({
    selector: 'app-assistant-pagination',
    templateUrl: './pagination.component.html',
    styleUrls: ['./pagination.component.scss']
})
export class PaginationComponent extends BaseDetachedComponent implements OnInit, OnDestroy {
    @Input() selector: string;
    @Input() buttonShift: number = 20;
    @Input() extendArea: boolean = false;

    @ViewChild('ps', {static: true}) ps: PerfectScrollbarComponent;
    @ViewChild('bodyElem', {static: true}) bodyElem: ElementRef;

    public scrollHeight: number;
    public readonly scrollConfig: PerfectScrollbarConfigInterface = {
        swipeEasing: true,
        suppressScrollX: true
    };
    private limit: number;
    private lastPage: number;
    private lastPageReached: boolean = false;
    private firstPageReached: boolean = false;
    private lastScrollDirection: EDirection;
    private resizeSensor: ResizeSensor;
    private topElemId: string;
    private bodyElemObserver: IntersectionObserver;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected elRef: ElementRef,
        protected paginationService: PaginationService
    ) {
        super(cdr);

        this.cdr.detach();
    }

    get isLoadingTop(): boolean {
        return this.lastScrollDirection === EDirection.up && this.isLoading;
    }

    get isLoadingBottom(): boolean {
        return this.lastScrollDirection === EDirection.down && this.isLoading;
    }

    get upDirectionReached(): boolean {
        return this.lastScrollDirection === EDirection.up && this.firstPageReached;
    }

    get downDirectionReached(): boolean {
        return this.lastScrollDirection === EDirection.down && this.lastPageReached;
    }

    get isReachedTop(): boolean {
        return this.paginationService.reachedTop();
    }

    ngOnInit(): void {
        this._destroy$
            .asObservable()
            .pipe(
                finalize(() => {
                    this.bodyElemObserver.disconnect();
                    this.resizeSensor.detach();
                })
            )
            .subscribe();

        this.getScrollHeight();

        this.resizeSensor = new ResizeSensor(this.elRef.nativeElement, throttle(() => {
            setTimeout(() => {
                this.getScrollHeight();
            }, 50);
        }, 300));

        this.paginationService
            .bag
            .pipe(take(1))
            .subscribe(() => this.directionObserver());

        this.paginationService
            .bag
            .pipe(takeUntil(this._destroy$))
            .subscribe(({pages, page}: IBagState) => {
                if (pages.length === 1 && page > 1) {
                    this.onScrollUp();
                    this.paginationService.up();
                }

                const offset: number = this.ps.directiveRef.position(true).y as number;

                this.limit = this.paginationService.limit();

                this.firstPageReached = this.paginationService.reachedTop() && this.paginationService.firstPage() === 1;

                this.lastPageReached = this.lastPage === this.paginationService.lastPage();
                this.lastPage = this.paginationService.lastPage();

                this.detectChanges();

                setTimeout(() => {
                    this.dropPageReached();
                }, 2000);

                setTimeout(() => {
                    if (!!this.topElemId && this.lastScrollDirection === EDirection.up) {
                        const element = this.ps.directiveRef.elementRef.nativeElement.querySelector(`#${UtilsService.cssEscape(this.topElemId)}`);
                        if (element) {
                            const elementPos = element.getBoundingClientRect();
                            const scrollerPos = this.ps.directiveRef.elementRef.nativeElement.getBoundingClientRect();
                            const currentPos = this.ps.directiveRef.elementRef.nativeElement['scrollTop'];
                            const position = elementPos.top - scrollerPos.top + currentPos;
                            this.ps.directiveRef.scrollToTop(position + offset);
                        }
                    }
                    this.numerateBodyElements();
                });
            });

        this.paginationService
            .processing
            .pipe(takeUntil(this._destroy$))
            .subscribe((value: boolean) => {
                if (value) {
                    this.loadingTrigger();
                } else {
                    this.loadingCompleted();
                }
            });
    }

    onScrollUp(): void {
        this.lastScrollDirection = EDirection.up;
        this.dropPageReached();
    }

    onScrollDown(): void {
        this.lastScrollDirection = EDirection.down;
        this.dropPageReached();
    }

    dropPageReached(): void {
        if (this.firstPageReached) {
            this.firstPageReached = false;
            this.detectChanges();
        }

        if (this.lastPageReached) {
            this.lastPageReached = false;
            this.detectChanges();
        }
    }

    jumpUp(): void {
        this.paginationService.goToFirstPage();
    }

    getScrollHeight(): void {
        const windowHeight: number = window.innerHeight;
        const top: number = this.elRef.nativeElement.getBoundingClientRect().top;
        const height: number = windowHeight - top - FOOTER_HEIGHT - CARD_BOTTOM_MARGIN;
        if (this.extendArea) {
            this.scrollHeight = height > MIN_SCROLLABLE_AREA ? height : MIN_SCROLLABLE_AREA;
        } else {
            this.scrollHeight = height;
        }
        this.detectChanges();
    }

    private directionObserver(): void {
        const options: object = {
            root: this.ps.directiveRef.elementRef.nativeElement,
            threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9]
        };

        const limit: number = this.paginationService.limit();

        this.bodyElemObserver = new IntersectionObserver((entries: Array<IntersectionObserverEntry>) => {
            const firstHit: IntersectionObserverEntry = entries.find((entry: IntersectionObserverEntry) => {
                return entry.target.getAttribute('pagination-index-first') === 'true' && entry.isIntersecting;
            });

            const lastHit: IntersectionObserverEntry = entries.find((entry: IntersectionObserverEntry) => {
                return entry.target.getAttribute('pagination-index-last') === 'true' && entry.isIntersecting;
            });

            this.detectChanges();

            entries.filter((entry: IntersectionObserverEntry) => {
                return entry.target.getAttribute('pagination-index-break') === 'true';
            }).forEach((entry: IntersectionObserverEntry) => {
                const index: number = Number(entry.target.getAttribute('pagination-index'));

                switch (this.lastScrollDirection) {
                    case EDirection.up:
                        if (entry.isIntersecting && entry.intersectionRatio >= 0.9) {
                            this.paginationService.upSoft(index / limit);
                        }

                        break;
                    case EDirection.down:
                        if (!entry.isIntersecting && entry.intersectionRatio <= 0) {
                            this.paginationService.downSoft(index / limit);
                        }
                        break;
                }
            });

            if (firstHit || lastHit) {
                switch (this.lastScrollDirection) {
                    case EDirection.up:
                        if (firstHit && firstHit.intersectionRatio >= 0.9) {
                            this.paginationService.up();
                        }
                        break;
                    case EDirection.down:
                        if (lastHit && lastHit.intersectionRatio >= 0.9) {
                            this.paginationService.down();
                        }
                        break;
                }
            }

        }, options);
    }

    private numerateBodyElements(): void {
        if (!!this.selector) {
            const elements: NodeListOf<Element> = this.bodyElem.nativeElement.querySelectorAll(this.selector);
            const limit: number = this.paginationService.limit();
            const firstPage: number = this.paginationService.firstPage();
            const margin: number = firstPage > 1 ? limit * (firstPage - 1) : 0;

            elements.forEach((element: Element, index: number) => {
                const order: number = margin + index + 1;

                element.setAttribute('id', `${this.selector}-${String(order)}`);
                element.setAttribute('pagination-index', `${String(order)}`);
                element.setAttribute('pagination-index-first', `${index === 0}`);
                element.setAttribute('pagination-index-break', `${(order % limit === 0)}`);
                element.setAttribute('pagination-index-last', `${index === elements.length - 1}`);

                if (index === 0) {
                    this.topElemId = element.getAttribute('id');
                }

                if (element.getAttribute('pagination-index-break') === 'true'
                    || element.getAttribute('pagination-index-first') === 'true'
                    || element.getAttribute('pagination-index-last') === 'true'
                ) {
                    this.bodyElemObserver.observe(element);
                } else {
                    this.bodyElemObserver.unobserve(element);
                }
            });

            this.detectChanges();
        }
    }
}
