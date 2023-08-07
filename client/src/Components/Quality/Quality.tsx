import {useEffect, useState} from 'react';
import _ from 'lodash';
import CreateQualityForm from './CreateQualityForm.js';
import EditQualityForm from './EditQualityForm.js';
import Api from '../../Api.js';
import {QualityApiInterface} from '../../Interface/QualityApiInterface.js';

interface QualityProps {
    api: Api;
}

export default function Quality(props: QualityProps) {
    const [qualities, setQualities] = useState<QualityApiInterface[]>([]);

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editQualityID, setEditQualityID] = useState(0);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        props.api.request('/qualities', (response) => {
            setQualities(response);
        });
    }

    function deleteHandle(ID: number, event) {
        event.preventDefault();

        props.api.request('/quality/delete?' + new URLSearchParams({
            id: String(ID),
        }), (response) => {
            if (Number(response) === 1) {
                let newQualities = [...qualities];
                _.remove(newQualities, (value, index, collection) => {
                    return value.id === ID;
                });
                setQualities(newQualities);
            }
        });
    }

    function showCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(true);
    }

    function hideCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(false);
    }

    function toggleCreateFormHandler(event) {
        event.preventDefault();
        setCreateFormVisible(!createFormVisible);
    }

    //todo: Надо придумать алгоритм для различия обработчиков событий dom и приложения.
    function createHandler(event) {
        setCreateFormVisible(false);
        fetchItems();   //todo: Не очень удобно: список сортируется с учетом ного элемента. Возможно стоит добавлять в конец таблицы.
    }

    function showEditFormHandler(ID, event) {
        event.preventDefault();
        setEditQualityID(ID);
        setEditFormVisible(true);
    }

    function updateHandler(event) {
        fetchItems();
        setEditFormVisible(false);
    }

    function hideEditFormHandler(event): void {
        setEditFormVisible(false);
    }

    return (
        <div>
            <h3>Qualities</h3>
            <div className={'simple-block'}>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>sort</th>
                        <th>control</th>
                    </tr>
                    {qualities.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>{value.sort}</td>
                                <td>
                                    <button className={'btn'} onClick={showEditFormHandler.bind(this, value.id)}>Edit</button>
                                    <button className={'btn'} onClick={deleteHandle.bind(this, value.id)}>Delete</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
            <div className={'simple-block'}>
                {createFormVisible ? (<CreateQualityForm
                    api={props.api}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                />) : (<button className={'btn'} onClick={showCreateFormHandler}>Create</button>)}
                {editFormVisible && <EditQualityForm
                    api={props.api}
                    ID={editQualityID}
                    updateHandler={updateHandler}
                    closeHandler={hideEditFormHandler}
                />}
            </div>
        </div>
    );
}