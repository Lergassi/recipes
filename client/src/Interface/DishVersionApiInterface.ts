import {QualityApiInterface} from './QualityApiInterface.js';

export interface DishVersionApiInterface {
    id: number;
    name: string;
    alias: string;
    dish_id: number;
    quality: QualityApiInterface;
}