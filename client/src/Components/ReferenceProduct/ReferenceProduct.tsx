import {useEffect, useState} from 'react';
import _ from 'lodash';
import CreateReferenceProductForm from './CreateReferenceProductForm.js';
import EditReferenceProductForm from './EditReferenceProductForm.js';

interface ReferenceProductProps {
    host: string;
}

export default function ReferenceProduct(props: ReferenceProductProps) {
    const [referenceProducts, setReferenceProducts] = useState([]);

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editReferenceProductID, setEditReferenceProductID] = useState(0);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        let url = props.host + '/reference_products';
        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        setReferenceProducts(value.response);
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

    function onClickDeleteHandle(ID, event) {
        let url = props.host + '/reference_product/delete?' + new URLSearchParams({
            id: ID,
        });

        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        if (Number(value.response) === 1) {
                            let newQualities = [...referenceProducts];
                            _.remove(newQualities, (value, index, collection) => {
                                return value.id === ID;
                            });
                            setReferenceProducts(newQualities);
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

    function toggleCreateFormHandler(event): void {
        event.preventDefault();
        setCreateFormVisible(!createFormVisible);
    }

    function createHandler() {
        setCreateFormVisible(false);
        fetchItems();
    }

    function showEditFormHandler(ID, event) {
        event.preventDefault();
        setEditReferenceProductID(ID);
        setEditFormVisible(true);
    }

    function closeEditFormHandler(event): void {
        setEditFormVisible(false);
    }

    function updateHandler(event) {
        fetchItems();
        setEditFormVisible(false);
    }

    return (
        <div>
            <h3>Reference products</h3>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>sort</th>
                        <th>control</th>
                    </tr>
                    {referenceProducts.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>{value.sort}</td>
                                <td>
                                    <button onClick={onClickDeleteHandle.bind(this, value.id)}>delete</button>
                                    <button onClick={showEditFormHandler.bind(this, value.id)}>Edit</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
                {createFormVisible ? (<CreateReferenceProductForm
                    host={props.host}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                />) : (<button onClick={showCreateFormHandler}>Create</button>)}
                {editFormVisible && <EditReferenceProductForm
                    host={props.host}
                    ID={editReferenceProductID}
                    updateHandler={updateHandler}
                    closeHandler={closeEditFormHandler}
                />}
            </div>
            {/*end block__content*/}
        </div>
    );
}