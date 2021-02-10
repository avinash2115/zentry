import {
    ChangeDetectorRef,
    Component,
    OnInit,
    ElementRef,
    ViewChild,
    Input,
    Output,
    EventEmitter
} from '@angular/core';
import { BaseDetachedComponent } from '../../../classes/abstracts/component/base-detached-component';
import SignaturePad from 'signature_pad';

@Component({
  selector: 'app-signature-pad',
  templateUrl: './signature-pad.component.html',
  styleUrls: ['./signature-pad.component.scss']
})
export class SignaturePadComponent extends BaseDetachedComponent implements OnInit {
    @ViewChild('canvas', { static: true }) canvas: ElementRef<HTMLCanvasElement>;

    @Input() placeholder: string = 'Draw your signature here';

    @Output() onDrawEnd: EventEmitter<string> = new EventEmitter<string>();

    private _pad: SignaturePad | null  = null;

    constructor(
        protected cdr: ChangeDetectorRef,
    ) {
        super(cdr);
    }

    get pad(): SignaturePad {
        return this._pad;
    }

    ngOnInit(): void {
        window.addEventListener('resize', this.resizeCanvas);
        this.resizeCanvas();

        this.loadSignatruePad();
    }

    loadSignatruePad(canvas: HTMLCanvasElement = this.canvas.nativeElement): void {
        this._pad = new SignaturePad(canvas, {
          onBegin: (): void => this.detectChanges(),
          onEnd: (): void => {
              this.detectChanges()
              this.handleEndDraw()
          }
        })
    }

    resizeCanvas(): void {
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        this.canvas.nativeElement.width = this.canvas.nativeElement.offsetWidth * ratio;
        this.canvas.nativeElement.height = this.canvas.nativeElement.offsetHeight * ratio;
        this.canvas.nativeElement.getContext('2d').scale(ratio, ratio);
        if (this._pad) {
            this._pad.clear();
        }
    }

    clear(): void {
        this._pad.clear();
    }

    handleEndDraw(): void {
        this.onDrawEnd.next(this.canvas.nativeElement.toDataURL())
    }
}
