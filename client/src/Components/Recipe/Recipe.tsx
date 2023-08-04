import {useEffect, useState} from 'react';
import {RecipeInterface} from '../../Interface/RecipeInterface.js';
import {ReferenceProductInterface} from '../../Interface/ReferenceProductInterface.js';
import _ from 'lodash';
import Api from '../../Api.js';

interface RecipeProps {
    api: Api;
    // referenceProducts: {[ID: number]: ReferenceProductInterface}; //todo: Отдельный select компонент?
    referenceProducts: ReferenceProductInterface[]; //todo: Отдельный select компонент?
    ID?: number;
}

export default function Recipe(props: RecipeProps) {
    const [recipe, setRecipe] = useState<RecipeInterface>(null);

    const [selectedReferenceProductID, setSelectedReferenceProductID] = useState<number|null>(null);
    const [selectedReferenceWeight, setSelectedReferenceWeight] = useState<number|null>(null);

    const [branchName, setBranchName] = useState('');

    // useEffect(() => {
    //     fetchReferenceProducts();
    // }, []);

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

    function addProductHandler(event): void {
        if (recipe && selectedReferenceProductID && selectedReferenceWeight) {
            props.api.request('/recipe/add_product?' + new URLSearchParams({
                id: String(recipe.id),
                reference_product_id: String(selectedReferenceProductID),
                weight: String(selectedReferenceWeight),
            }), response => {
                fetchRecipe(props.ID);
            });
        }
    }

    function removeProductHandler(event): void {
        if (recipe && selectedReferenceProductID && selectedReferenceWeight) {
            props.api.request('/recipe/remove_product?' + new URLSearchParams({
                id: String(recipe.id),
                reference_product_id: String(selectedReferenceProductID),
                weight: String(selectedReferenceWeight),
            }), response => {
                fetchRecipe(props.ID);
            });
        }
    }

    function updateHandler(): void {

    }

    function deleteProductHandler(ID: number, weight: number, event): void {
        event.preventDefault();
        props.api.request('/recipe/remove_product?' + new URLSearchParams({
            id: String(recipe.id),
            reference_product_id: String(ID),
            weight: String(weight),
        }), response => {
            fetchRecipe(props.ID);
        });
    }

    function selectReferenceProductIDOnChangeHandler(event): void {
        setSelectedReferenceProductID(event.target.value);
    }

    function selectReferenceProductWeightOnChangeHandler(event): void {
        setSelectedReferenceWeight(event.target.value);
    }

    function commitHandler(event): void {
        if (!recipe) return;

        props.api.request('/recipe/commit?' + new URLSearchParams({
            id: String(recipe.id),
        }), response => {
            console.log('Commit created!');
        });
    }

    function branchHandler(event): void {
        if (!recipe) return;

        props.api.request('/recipe/branch?' + new URLSearchParams({
            id: String(recipe.id),
            name: branchName,
        }), response => {
            console.log('Branch created!');
        });
    }

    function branchNameOnChangeHandler(event): void {
        setBranchName(event.target.value);
    }

    return recipe && (
        <div>
            <h3>Recipe {recipe.name} ({recipe.id})</h3>
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
                                    <button onClick={deleteProductHandler.bind(this, value.reference_product.id, value.weight)}>Remove</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
                <div>
                    <select value={selectedReferenceProductID || ''} name="" id="" onChange={selectReferenceProductIDOnChangeHandler}>
                        <option value="0" key={0}>Select product</option>
                        {_.map(props.referenceProducts, (value, index, collection) => {
                            return (
                                <option key={index + 1} value={value.id}>{value.name}</option>
                            );
                        })}
                    </select>
                    <input value={selectedReferenceWeight || ''} type="text" onChange={selectReferenceProductWeightOnChangeHandler}/>
                    <button onClick={addProductHandler}>Add</button>
                    <button onClick={removeProductHandler}>Remove</button>
                </div>
                <div><button onClick={commitHandler}>Commit</button></div>
                <div>
                    <input value={branchName} type="text" onChange={branchNameOnChangeHandler}/>
                    <button onClick={branchHandler}>Branch</button>
                </div>
            </div>
        </div>
    );
}