import { IPage } from '../interfaces/page';

export class Page implements IPage {
    public page = 0;

    public total_resources = 0;
    public resources_per_page = 0;
}
