import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import CreateDishForm from './CreateDishForm.js';
import EditDishForm from './EditDishForm.js';

interface DishListProps {
    api: Api;
    selectHandler?: (ID: number, event) => void;
}

export default function DishList(props: DishListProps) {
    const [dishes, setDishes] = useState([]);
    const [selectedDishID, setSelectedDishID] = useState<number|null>(null);

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
        setSelectedDishID(ID);
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
        event.stopPropagation();
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
        <div className={'dish-list-component'}>
            <h3>Dishes</h3>
            <div className={'item-list simple-block'}>
                {dishes.map((value, index, array) => {
                    return (
                        <div className={'item-list__item ' + (selectedDishID === value.id ? 'item-list__item_selected' : '')} onClick={selectHandler.bind(this, value.id)} key={index}>
                            <span className={'item-list__item-text'}>{value.name}</span>
                            <span className={'item-list__edit-button'} onClick={showEditFormHandler.bind(this, value.id)}></span>
                        </div>
                    );
                })}
            </div>
            <div className={'simple-block'}>
                {createFormVisible ? <CreateDishForm
                    api={props.api}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                /> : <button className={'btn'} onClick={showCreateFormHandler}>Add</button>}
            </div>
            <div className={'simple-block'}>
                {editFormVisible && <EditDishForm
                    api={props.api}
                    ID={editDishID}
                    updateHandler={updateHandler}
                    closeHandler={hideEditFormHandler}
                />}
            </div>
        </div>
    );
}