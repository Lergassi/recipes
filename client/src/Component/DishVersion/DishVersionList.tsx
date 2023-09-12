import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import EditDishVersionForm from './EditDishVersionForm.js';
import CreateDishVersionForm from './CreateDishVersionForm.js';
import CreateDishForm from '../Dish/CreateDishForm.js';
import _, {values} from 'lodash';
import {DishVersionApiInterface} from '../../Interface/DishVersionApiInterface.js';

interface DishVersionListProps {
    api: Api;
    dishID?: number;
    selectHandler?: (ID: number, event) => void;
}

export default function DishVersionList(props: DishVersionListProps) {
    const [dishVersions, setDishVersions] = useState<DishVersionApiInterface[]>([]);
    const [selectedDishVersionID, setSelectedDishVersionID] = useState<number|null>(null);

    const [createFormVisible, setCreateFormVisible] = useState(false);
    const [selectedDishID, setSelectedDishID] = useState<number|null>(null);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editDishVersionID, setEditDishVersionID] = useState<number|null>(null);

    useEffect(() => {
        if (props.dishID) {
            fetchItems(props.dishID);
        }
        setCreateFormVisible(false);
        setEditFormVisible(false);
    }, [props.dishID]);

    function fetchItems(dishID: number) {
        props.api.request('/dish_versions', {
            dish_id: String(dishID),
        }, (response) => {
            setDishVersions(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
        setSelectedDishVersionID(ID);
        props.selectHandler?.(ID, event);
    }

    function showCreateFormHandler(event) {
        event.preventDefault();
        setEditFormVisible(false);
        setCreateFormVisible(dishSelected());
    }

    function hideCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(false);
    }

    function createHandler(event) {
        hideCreateFormHandler(event);
        fetchItems(props.dishID);
    }

    function createDishVersionHandler(dishID: number, event): void {
        setSelectedDishID(dishID);
        showCreateFormHandler(event);
    }

    function showEditFormHandler(ID: number, event) {
        event.preventDefault();
        setCreateFormVisible(false);
        setEditDishVersionID(ID);
        setEditFormVisible(true);
    }

    function hideEditFormHandler(event) {
        event.preventDefault();
        setEditFormVisible(false);
    }

    function updateHandler(event) {
        hideEditFormHandler(event);
        fetchItems(props.dishID);
    }

    function dishSelected(): boolean {
        return !_.isNil(props.dishID);
    }

    return  (
        <div>
            <h3>Dish versions</h3>
            {dishVersions.length !== 0 && (
                <div className={'item-list simple-block'}>
                    {dishVersions.map((value, index, array) => {
                        return (
                            <div className={'item-list__item ' + 'item-list__item_' + value.quality.alias + (selectedDishVersionID === value.id ? ' item-list__item_selected' : '')} onClick={selectHandler.bind(this, value.id)} key={index}>
                                <span className={'item-list__item-text'}>{value.name}</span>
                                <span className={'item-list__edit-button'} onClick={showEditFormHandler.bind(this, value.id)}></span>
                            </div>
                        );
                    })}
                </div>
            )}
            <div className={'simple-block'}>
                {createFormVisible ? <CreateDishVersionForm
                    api={props.api}
                    dishID={props.dishID}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                /> : <button className={'btn'} onClick={showCreateFormHandler} disabled={!dishSelected()}>Add</button>}
            </div>
            <div className={'simple-block'}>
                {editFormVisible && <EditDishVersionForm
                    api={props.api}
                    ID={editDishVersionID}
                    updateHandler={updateHandler}
                    closeHandler={hideEditFormHandler}
                />}
            </div>
        </div>
    );
}