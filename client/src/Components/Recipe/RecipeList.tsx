import {useEffect, useState} from 'react';
import Api from '../../Api.js';

interface RecipeListProps {
    api: Api;
    dishVersionID?: number;
    selectHandler?: (ID: number, event) => void;
}

export default function RecipeList(props: RecipeListProps) {
    const [recipes, setRecipes] = useState([]);

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
        props.selectHandler?.(ID, event);
    }

    return (
        <div>
            <h3>Recipes</h3>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>control</th>
                    </tr>
                    {recipes.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>
                                    <button onClick={selectHandler.bind(this, value.id)}>Select</button>
                                    {/*<button>Delete</button>*/}
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    );
}