import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import EditDishVersionForm from './EditDishVersionForm.js';
import CreateDishVersionForm from './CreateDishVersionForm.js';
import CreateDishForm from '../Dish/CreateDishForm.js';
import _ from 'lodash';

interface DishVersionListProps {
    api: Api;
    dishID?: number;
    selectHandler?: (ID: number, event) => void;
}

export default function DishVersionList(props: DishVersionListProps) {
    const [dishVersions, setDishVersions] = useState([]);

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
        props.api.request('/dish_versions?' + new URLSearchParams({
            dish_id: String(dishID),
        }), response => {
            setDishVersions(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
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
            <h3>Dish versions for {props.dishID}</h3>
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
                    {dishVersions.map((value, index, array) => {
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
                    {createFormVisible ? <CreateDishVersionForm
                        api={props.api}
                        dishID={props.dishID}
                        createHandler={createHandler}
                        closeHandler={hideCreateFormHandler}
                    /> : <button onClick={showCreateFormHandler} disabled={!dishSelected()}>Create dish version</button>}
                </div>
                <div>
                    {editFormVisible && <EditDishVersionForm
                        api={props.api}
                        ID={editDishVersionID}
                        updateHandler={updateHandler}
                        closeHandler={hideEditFormHandler}
                    />}
                </div>
            </div>
        </div>
    );
}