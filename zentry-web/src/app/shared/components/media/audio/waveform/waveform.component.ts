import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    Input,
    OnChanges,
    OnInit,
    SimpleChanges
} from '@angular/core';
import { BaseDetachedComponent } from '../../../../classes/abstracts/component/base-detached-component';
import { UtilsService } from '../../../../services/utils.service';
import { filter, first, take } from 'rxjs/operators';
import * as moment from 'moment';

export interface ILog {
    xAxisPosition: number,
    decibels: number,
    capturedAt: number,
}

export interface IHighlight {
    startedAtUnix: number
    endedAtUnix?: number
}

@Component({
    selector: 'app-media-audio-waveform',
    templateUrl: './waveform.component.html',
    styleUrls: ['./waveform.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class WaveformComponent extends BaseDetachedComponent implements OnInit, OnChanges {
    @Input() stream: MediaStream;
    @Input() highlight: IHighlight | null = null;
    @Input() highlights: Array<IHighlight> = [];

    private context: AudioContext;
    private timer: any;

    constructor(
        protected cdr: ChangeDetectorRef
    ) {
        super(cdr);

        cdr.detach();
    }

    ngOnInit(): void {
        this._destroy$
            .pipe(filter((value: boolean) => value), take(1))
            .subscribe(() => {
                if (this.context instanceof AudioContext) {
                    this.context.close();
                }

                if (!!this.timer) {
                    clearInterval(this.timer);
                }
            });

        this.context = new AudioContext();

        const analyserNode: AnalyserNode = this.context.createAnalyser();

        analyserNode.smoothingTimeConstant = 0.85;
        analyserNode.fftSize = 8192;

        const mediaStreamAudioSourceNode: MediaStreamAudioSourceNode = this.context.createMediaStreamSource(this.stream);

        mediaStreamAudioSourceNode.connect(analyserNode);

        const audioStreamDecibels: Uint8Array = new Uint8Array(analyserNode.frequencyBinCount);

        const htmlCanvasElement: HTMLCanvasElement = document.getElementById('waveform') as HTMLCanvasElement;
        const canvasContext = htmlCanvasElement.getContext('2d');

        const defaultStrokeStyle: string = 'rgb(217,218,234)';
        const poiStrokeStyle: string = UtilsService.propertyFromCSSClass('waveform__stroke', 'color');

        canvasContext.fillStyle = 'rgb(255,255,255)';
        canvasContext.strokeStyle = defaultStrokeStyle;
        canvasContext.lineWidth = 2;

        const audioStreamLogs: Array<ILog> = [];
        const audioStreamDistance: number = 5;
        let audionStreamDistancePassed: number = 0;

        const draw = () => {
            audionStreamDistancePassed += audioStreamDistance;

            analyserNode.getByteFrequencyData(audioStreamDecibels);

            const yAxisPosition: number = htmlCanvasElement.height / 2;
            const xAxisPosition: number = audionStreamDistancePassed;

            let averageDecibels: number = (audioStreamDecibels.reduce((result: number, val: number) => result + val, 0) / audioStreamDecibels.length);

            if (averageDecibels < 1) {
                averageDecibels = 1;
            }

            if (averageDecibels > htmlCanvasElement.offsetHeight / 2) {
                averageDecibels = htmlCanvasElement.offsetHeight / 2;
            }

            if (xAxisPosition > htmlCanvasElement.offsetWidth) {
                audioStreamLogs.forEach((tile, index: number) => {
                    if (index === audioStreamLogs.length - 1) {
                        audioStreamLogs[index].decibels = averageDecibels;
                        audioStreamLogs[index].capturedAt = moment().unix();
                    } else {
                        audioStreamLogs[index].decibels = audioStreamLogs[index + 1].decibels;
                        audioStreamLogs[index].capturedAt = audioStreamLogs[index + 1].capturedAt;
                    }
                });
            } else {
                const existingIndex: number = audioStreamLogs.findIndex((audionStreamLog: ILog) => audionStreamLog.xAxisPosition === xAxisPosition);

                if (existingIndex !== -1) {
                    if (audioStreamLogs[existingIndex].decibels < averageDecibels) {
                        audioStreamLogs[existingIndex].decibels = averageDecibels
                    }
                } else {
                    audioStreamLogs.push({
                        xAxisPosition: xAxisPosition,
                        decibels: averageDecibels,
                        capturedAt: moment().unix(),
                    });
                }
            }

            if (xAxisPosition >= htmlCanvasElement.offsetWidth) {
                canvasContext.clearRect(0, 0, htmlCanvasElement.width, htmlCanvasElement.height);
            }

            audioStreamLogs.forEach((audioStreamLog: ILog) => {
                const isAtPoi: number = this.highlights.findIndex((h: IHighlight) => h.startedAtUnix <= audioStreamLog.capturedAt && h.endedAtUnix >= audioStreamLog.capturedAt);

                if (isAtPoi !== -1 || (this.highlight && this.highlight.startedAtUnix <= audioStreamLog.capturedAt)) {
                    canvasContext.strokeStyle = poiStrokeStyle;
                } else {
                    canvasContext.strokeStyle = defaultStrokeStyle;
                }

                canvasContext.beginPath();
                canvasContext.moveTo(audioStreamLog.xAxisPosition, yAxisPosition);
                canvasContext.lineTo(audioStreamLog.xAxisPosition, yAxisPosition - audioStreamLog.decibels);
                canvasContext.lineTo(audioStreamLog.xAxisPosition, yAxisPosition + audioStreamLog.decibels);
                canvasContext.stroke();
            });
        }

        this.timer = setInterval(() => {
            draw();
        }, 100);
    }

    ngOnChanges(changes: SimpleChanges): void {
        this.detectChanges();
    }
}
