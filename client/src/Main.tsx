import DishList from './Components/Dish/DishList.js';
import DishVersionList from './Components/DishVersion/DishVersionList.js';
import RecipeList from './Components/Recipe/RecipeList.js';
import Recipe from './Components/Recipe/Recipe.js';
import {useEffect, useState} from 'react';
import {ReferenceProductInterface} from './Interface/ReferenceProductInterface.js';
import Api from './Api.js';

interface MainProps {
    api: Api;
}

export default function Main(props: MainProps) {
    const [referenceProducts, setReferenceProducts] = useState<ReferenceProductInterface[]>([]);

    const [selectDishID, setSelectDishID] = useState<number|null>(null);
    const [selectDishVersionID, setSelectDishVersionID] = useState<number|null>(null);
    const [selectRecipeID, setSelectRecipeID] = useState<number|null>(null);

    useEffect(() => {
        fetchReferenceProducts();
    }, []);

    function selectDishHandler(ID: number, event): void {
        setSelectDishID(ID);
    }

    function selectDishVersionHandler(ID: number, event): void {
        setSelectDishVersionID(ID);
    }

    function selectRecipeHandler(ID: number, event): void {
        setSelectRecipeID(ID);
    }

    function fetchReferenceProducts(): void {
        props.api.request('/reference_products?', response => {
            setReferenceProducts(response);
        });
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
                    dishID={selectDishID}
                    selectHandler={selectDishVersionHandler}
                />
            </div>
            <div className={'main__recipe-list'}>
                <RecipeList
                    api={props.api}
                    dishVersionID={selectDishVersionID}
                    selectHandler={selectRecipeHandler}
                />
            </div>
            <div className={'main__recipe'}>
                <Recipe
                    api={props.api}
                    ID={selectRecipeID}
                    referenceProducts={referenceProducts}
                />
            </div>
        </div>
    );
}