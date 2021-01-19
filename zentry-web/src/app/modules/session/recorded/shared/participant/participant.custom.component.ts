import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    Input,
    Output,
    EventEmitter,
    OnChanges,
    OnInit,
    SimpleChanges,
    ViewChild
} from '@angular/core';
import {
    ParticipantJsonapiResource,
    ParticipantJsonapiResource as UserParticipantJsonapiResource
} from '../../../../../resources/user/participant/participant.jsonapi.service';
import { PerfectScrollbarConfigInterface, PerfectScrollbarDirective } from 'ngx-perfect-scrollbar';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../../../resources/session/poi/participant/participant.jsonapi.service';
import { RecordedService } from '../../recorded.service';
import { map, switchMap, takeUntil } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { DataError } from '../../../../../shared/classes/data-error';
import { NgSelectComponent } from '@ng-select/ng-select';
import { throttleable } from '../../../../../shared/decorators/throttleable.decorator';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../../shared/consts/form/patterns';
import { Converter } from '../../../../../../vendor/vp-ngx-jsonapi/services/converter';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SwalService } from '../../../../../shared/services/swal.service';
import { Observable } from 'rxjs';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../../../resources/session/poi/poi.jsonapi.service';
import { of } from 'rxjs/internal/observable/of';
import { ParticipantComponent } from './participant.component';

@Component({
    selector: 'app-session-recorded-shared-participant-custom',
    templateUrl: './participant.custom.component.html',
    styleUrls: ['./participant.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantCustomComponent extends ParticipantComponent implements OnInit {
    private _participant: UserParticipantJsonapiResource | null = null;
    @Output() onParticipantClick: EventEmitter<UserParticipantJsonapiResource> = new EventEmitter<UserParticipantJsonapiResource>();

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected modalService: NgbModal,
        protected _recordedService: RecordedService
    ) {
        super(cdr, loaderService, modalService, _recordedService);

        cdr.detach();
    }

    ngOnInit(): void {
        super.ngOnInit();

        this.recordedService
            .participantService
            .entity
            .subscribe((entity: UserParticipantJsonapiResource) => {
                this._participant = entity;
                this.detectChanges();
            });
    }

    isActive(entity: UserParticipantJsonapiResource): boolean {
        return this._participant instanceof UserParticipantJsonapiResource && this._participant.id === entity.id;
    }

    handleParticipantClick(participant: UserParticipantJsonapiResource): void {
        this.onParticipantClick.next(participant)
    }
}
