import {useEffect, useState} from 'react';
import {RecipeApiInterface} from '../../Interface/RecipeApiInterface.js';
import _ from 'lodash';
import Api from '../../Api.js';
import ReferenceProductWeightSelector from '../ReferenceProduct/ReferenceProductWeightSelector.js';

interface RecipeProps {
    api: Api;
    ID?: number;
    afterBranchCreatedHandler?: any;
}

export default function Recipe(props: RecipeProps) {
    const [recipe, setRecipe] = useState<RecipeApiInterface>(null);
    const [branchName, setBranchName] = useState('');

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
            // console.log('Commit created!');
            fetchRecipe(props.ID);
        });
    }

    function branchHandler(): void {
        if (!branchName) return;

        props.api.request('/recipe/branch?' + new URLSearchParams({
            id: String(recipe.id),
            name: branchName,
        }), response => {
            resetBranchControl();
            props.afterBranchCreatedHandler?.(response);
        });
    }

    function branchNameOnChangeHandler(event): void {
        setBranchName(event.target.value);
    }

    function resetBranchControl(): void {
        setBranchName('');
    }

    function enterBranchHandler(event): void {
        switch (event.key) {
            case 'Enter':
                branchHandler();
                break;
        }
    }

    return recipe && (
        <div>
            <h3>Recipe</h3>
            <div>
                <ReferenceProductWeightSelector
                    api={props.api}
                    addReferenceProductHandler={addProductHandler}
                    removeReferenceProductHandler={removeProductHandler}
                />
            </div>
            <div className={'simple-block'}>
                <table className={'base-table base-table_fullwidth'}>
                    <tbody>
                    <tr>
                        <th>product</th>
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
                                    <button className={'btn'} onClick={removeProductHandler.bind(this, value.reference_product.id, value.weight)}>Remove</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
            <div className={'simple-block'}>
                <button className={'btn'} onClick={commitHandler}>Commit</button>, last commit: {recipe.head_commit_id ?? 'uncommitted'}
            </div>
            <div className={'simple-block'}>
                <input className={'app-input'} value={branchName} type="text" onChange={branchNameOnChangeHandler} placeholder={'name'} onKeyDown={enterBranchHandler}/>
                <button className={'btn'} onClick={branchHandler}>Branch</button>
                <button className={'btn'} disabled={true} >DishVersion (indev)</button>
            </div>
        </div>
    );
}