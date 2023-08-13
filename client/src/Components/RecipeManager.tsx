import DishList from './Dish/DishList.js';
import DishVersionList from './DishVersion/DishVersionList.js';
import RecipeList from './Recipe/RecipeList.js';
import Recipe from './Recipe/Recipe.js';
import {useEffect, useState} from 'react';
import Api from '../Api.js';

interface RecipeManagerProps {
    api: Api;
}

export default function RecipeManager(props: RecipeManagerProps) {
    const [selectedDishID, setSelectedDishID] = useState<number|null>(null);
    const [selectedDishVersionID, setSelectedDishVersionID] = useState<number|null>(null);
    const [selectedRecipeID, setSelectedRecipeID] = useState<number|null>(null);

    function selectDishHandler(ID: number, event): void {
        setSelectedDishID(ID);
    }

    function selectDishVersionHandler(ID: number, event): void {
        setSelectedDishVersionID(ID);
    }

    function selectRecipeHandler(ID: number, event): void {
        setSelectedRecipeID(ID);
    }

    return (
        <div className={'component-group'}>
            <div className={'component-wrapper dish-list-component-wrapper'}>
                <DishList
                    api={props.api}
                    selectHandler={selectDishHandler}
                />
            </div>
            <div className={'component-wrapper dish-version-list-component-wrapper'}>
                <DishVersionList
                    api={props.api}
                    dishID={selectedDishID}
                    selectHandler={selectDishVersionHandler}
                />
            </div>
            <RecipeList
                api={props.api}
                dishVersionID={selectedDishVersionID}
                selectHandler={selectRecipeHandler}
            />
            {/* todo: Recipe перемещен внутрь списка для обновления после создания ветки. Найти решение без вложения. */}
        </div>
    );
}