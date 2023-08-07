import DishList from './Components/Dish/DishList.js';
import DishVersionList from './Components/DishVersion/DishVersionList.js';
import RecipeList from './Components/Recipe/RecipeList.js';
import Recipe from './Components/Recipe/Recipe.js';
import {useEffect, useState} from 'react';
import Api from './Api.js';

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
            <div className={'component-wrapper recipe-list-component-wrapper'}>
                <RecipeList
                    api={props.api}
                    dishVersionID={selectedDishVersionID}
                    selectHandler={selectRecipeHandler}
                />
            </div>
            <div className={'component-wrapper recipe-component-wrapper'}>
                <Recipe
                    api={props.api}
                    ID={selectedRecipeID}
                />
            </div>
        </div>
    );
}