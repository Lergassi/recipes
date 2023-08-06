import {useEffect, useState} from 'react';
import {RecipeApiInterface} from '../../Interface/RecipeApiInterface.js';
import _ from 'lodash';
import Api from '../../Api.js';
import ReferenceProductWeightSelector from '../ReferenceProduct/ReferenceProductWeightSelector.js';

interface RecipeProps {
    api: Api;
    ID?: number;
}

export default function Recipe(props: RecipeProps) {
    const [recipe, setRecipe] = useState<RecipeApiInterface>(null);
    const [newName, setNewName] = useState('');

    useEffect(() => {
        if (props.ID) {
            fetchRecipe(props.ID);
        }
    }, [props.ID]);

    function fetchRecipe(ID: number): void {
        props.api.request('/recipe/get?' + new URLSearchParams({
            id: String(ID),
        }), response => {
            setRecipe(response);
        });
    }

    function addProductHandler(ID: number, weight: number): void {
        props.api.request('/recipe/add_product?' + new URLSearchParams({
            id: String(recipe.id),
            reference_product_id: String(ID),
            weight: String(weight),
        }), response => {
            fetchRecipe(props.ID);
        });
    }

    function removeProductHandler(ID: number, weight: number): void {
        props.api.request('/recipe/remove_product?' + new URLSearchParams({
            id: String(recipe.id),
            reference_product_id: String(ID),
            weight: String(weight),
        }), response => {
            fetchRecipe(props.ID);
        });
    }

    function commitHandler(event): void {
        props.api.request('/recipe/commit?' + new URLSearchParams({
            id: String(recipe.id),
        }), response => {
            console.log('Commit created!');
        });
    }

    function branchHandler(event): void {
        props.api.request('/recipe/branch?' + new URLSearchParams({
            id: String(recipe.id),
            name: newName,
        }), response => {
            console.log('Branch created!');
        });
    }

    function branchNameOnChangeHandler(event): void {
        setNewName(event.target.value);
    }

    return recipe && (
        <div>
            <h3>Recipe {recipe.name} ({recipe.id})</h3>
            <div>
                <ReferenceProductWeightSelector
                    api={props.api}
                    addProductHandler={addProductHandler}
                    removeProductHandler={removeProductHandler}
                />
            </div>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>reference product id</th>
                        <th>weight</th>
                        <th>control</th>
                    </tr>
                    {recipe.products.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                {/*<td>{props.referenceProducts[value.reference_product_id].name}</td>*/}
                                <td>{value.reference_product.name}</td>
                                <td>{value.weight}</td>
                                <td>
                                    {/*<button onClick={deleteFullProductWeightHandler.bind(this, value.reference_product.id, value.weight)}>Remove</button>*/}
                                    <button onClick={removeProductHandler.bind(this, value.reference_product.id, value.weight)}>Remove</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
            <div><button onClick={commitHandler}>Commit</button>, last commit: {recipe.head_commit_id ?? 'uncommitted'}</div>
            <div>
                <input value={newName} type="text" onChange={branchNameOnChangeHandler}/>
                <button onClick={branchHandler}>Branch</button>
                <button disabled={true} >DishVersion (indev)</button>
            </div>
        </div>
    );
}