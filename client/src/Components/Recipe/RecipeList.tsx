import {useEffect, useState} from 'react';
import Api from '../../Api.js';

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
        props.api.request('/recipes?' + new URLSearchParams({
            dish_version_id: String(dishID),
        }), response => {
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
            <h3>Recipes</h3>
            {recipes.length !== 0 && (
                <div className={'item-list simple-block'}>
                    {recipes.map((value, index, array) => {
                        return (
                            <div className={'item-list__item ' + (selectedRecipeID === value.id ? 'item-list__item_selected' : '')} onClick={selectHandler.bind(this, value.id)} key={index}>
                                <span className={'item-list__item-text'}>{value.name}</span>
                                {/*<span className={'item-list__edit-button'}></span>*/}
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}