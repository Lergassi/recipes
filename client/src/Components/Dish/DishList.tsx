import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import CreateDishForm from './CreateDishForm.js';
import EditDishForm from './EditDishForm.js';
import EditQualityForm from '../Quality/EditQualityForm.js';

interface DishListProps {
    api: Api;
    selectHandler?: (ID: number, event) => void;
}

export default function DishList(props: DishListProps) {
    const [dishes, setDishes] = useState([]);

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editDishID, setEditDishID] = useState<number|null>(null);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        props.api.request('/dishes', response => {
            setDishes(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
        props.selectHandler?.(ID, event);
    }

    function showCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(true);
    }

    function hideCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(false);
    }

    function createHandler(event) {
        hideCreateFormHandler(event);
        fetchItems();
    }

    function showEditFormHandler(ID: number, event) {
        event.preventDefault();
        setEditDishID(ID);
        setEditFormVisible(true);
    }

    function hideEditFormHandler(event) {
        event.preventDefault();
        setEditFormVisible(false);
    }

    function updateHandler(event) {
        hideEditFormHandler(event);
        fetchItems();
    }

    return (
        <div>
            <h3>Dishes</h3>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>quality</th>
                        <th>control</th>
                    </tr>
                    {dishes.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>{value.quality_id}</td>
                                <td>
                                    <button onClick={selectHandler.bind(this, value.id)}>Select</button>
                                    <button onClick={showEditFormHandler.bind(this, value.id)}>Edit</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
                <div>
                    {createFormVisible ? <CreateDishForm
                        api={props.api}
                        createHandler={createHandler}
                        closeHandler={hideCreateFormHandler}
                    /> : <button onClick={showCreateFormHandler}>Create dish</button>}
                </div>
                <div>
                    {editFormVisible && <EditDishForm
                        api={props.api}
                        ID={editDishID}
                        updateHandler={updateHandler}
                        closeHandler={hideEditFormHandler}
                    />}
                </div>
            </div>
        </div>
    );
}