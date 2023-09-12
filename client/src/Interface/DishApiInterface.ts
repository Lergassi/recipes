import {QualityApiInterface} from './QualityApiInterface.js';

export interface DishApiInterface {
    id: number;
    name: string;
    alias: string;
    quality: QualityApiInterface;
}