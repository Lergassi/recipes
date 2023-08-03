import {useEffect, useState} from 'react';
import _ from 'lodash';
import CreateQualityForm from './CreateQualityForm.js';
import EditQualityForm from './EditQualityForm.js';

interface QualityProps {
    host: string;
}

export default function Quality(props: QualityProps) {
    const [qualities, setQualities] = useState([]);

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editQualityID, setEditQualityID] = useState(0);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        let url = props.host + '/qualities';
        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        setQualities(value.response);
                    })
                    .catch((reason) => {
                        console.log('error', reason);
                    })
            })
            .catch((reason) => {
                console.log('error', reason);
            })
        ;
    }

    function deleteHandle(ID, event) {
        event.preventDefault();
        let url = props.host + '/quality/delete?' + new URLSearchParams({
            id: ID,
        });

        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        if (Number(value.response) === 1) {
                            let newQualities = [...qualities];
                            _.remove(newQualities, (value, index, collection) => {
                                return value.id === ID;
                            });
                            setQualities(newQualities);
                        }
                    })
                    .catch((reason) => {
                        console.log('error', reason);
                    })
            })
            .catch((reason) => {
                console.log('error', reason);
            })
        ;
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
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>{/* todo: Только для dev. */}
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
                                    <button onClick={deleteHandle.bind(this, value.id)}>delete</button>
                                    <button onClick={showEditFormHandler.bind(this, value.id)}>Edit</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
                {createFormVisible ? (<CreateQualityForm
                    host={props.host}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                />) : (<button onClick={showCreateFormHandler}>Create</button>)}
                {editFormVisible && <EditQualityForm
                    host={props.host}
                    ID={editQualityID}
                    updateHandler={updateHandler}
                    closeHandler={hideEditFormHandler}
                />}
            </div>
            {/*end block__content*/}
        </div>
    );
}