import DishList from './Components/Dish/DishList.js';
import DishVersionList from './Components/DishVersion/DishVersionList.js';
import RecipeList from './Components/Recipe/RecipeList.js';
import Recipe from './Components/Recipe/Recipe.js';
import {useEffect, useState} from 'react';
import {ReferenceProductApiInterface} from './Interface/ReferenceProductApiInterface.js';
import Api from './Api.js';

interface MainProps {
    api: Api;
}

export default function Main(props: MainProps) {
    const [selectedDishID, setSelectedDishID] = useState<number|null>(null);
    const [selectedDishVersionID, setSelectedDishVersionID] = useState<number|null>(null);
    const [selectedRecipeID, setSelectedRecipeID] = useState<number|null>(null);

    useEffect(() => {

    }, []);

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
        <div>
            <div className={'main__dish-list'}>
                <DishList
                    api={props.api}
                    selectHandler={selectDishHandler}
                />
            </div>
            <div className={'main__dish-version-list'}>
                <DishVersionList
                    api={props.api}
                    dishID={selectedDishID}
                    selectHandler={selectDishVersionHandler}
                />
            </div>
            <div className={'main__recipe-list'}>
                <RecipeList
                    api={props.api}
                    dishVersionID={selectedDishVersionID}
                    selectHandler={selectRecipeHandler}
                />
            </div>
            <div className={'main__recipe'}>
                <Recipe
                    api={props.api}
                    ID={selectedRecipeID}
                />
            </div>
        </div>
    );
}