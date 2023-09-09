import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import Recipe from './Recipe.js';

interface RecipeListProps {
    api: Api;
    dishVersionID?: number;
    selectHandler?: (ID: number, event) => void;
}

export default function RecipeList(props: RecipeListProps) {
    const [recipes, setRecipes] = useState([]);
    const [selectedRecipeID, setSelectedRecipeID] = useState<number|null>(null);

    useEffect(() => {
        if (props.dishVersionID) {
            fetchItems(props.dishVersionID);
        }
    }, [props.dishVersionID]);

    function fetchItems(dishID: number) {
        props.api.request('/recipes', {
            dish_version_id: String(dishID),
        }, (response) => {
            setRecipes(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
        setSelectedRecipeID(ID);
        props.selectHandler?.(ID, event);
    }

    return (
        <div className={'recipe-list-component'}>
            <div className={'component-wrapper recipe-list-component-wrapper'}>
                <h3>Recipes</h3>
                {recipes.length !== 0 && (
                    <div className={'item-list simple-block'}>
                        {recipes.map((value, index, array) => {
                            return (
                                <div className={'item-list__item ' + (selectedRecipeID === value.id ? 'item-list__item_selected' : '')} onClick={selectHandler.bind(this, value.id)} key={index}>
                                    <span className={'item-list__item-text'}>{value.name}</span>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
            <div className={'component-wrapper recipe-component-wrapper'}>
                <Recipe
                    api={props.api}
                    ID={selectedRecipeID}
                    afterBranchCreatedHandler={(value) => {
                        fetchItems(props.dishVersionID);
                    }}
                />
            </div>
        </div>
    );
}