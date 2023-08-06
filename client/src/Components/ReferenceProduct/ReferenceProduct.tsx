import {useEffect, useState} from 'react';
import _ from 'lodash';
import CreateReferenceProductForm from './CreateReferenceProductForm.js';
import EditReferenceProductForm from './EditReferenceProductForm.js';
import {ReferenceProductApiInterface} from '../../Interface/ReferenceProductApiInterface.js';
import Api from '../../Api.js';

interface ReferenceProductProps {
    api: Api;
}

export default function ReferenceProduct(props: ReferenceProductProps) {
    const [referenceProducts, setReferenceProducts] = useState<ReferenceProductApiInterface[]>();

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editReferenceProductID, setEditReferenceProductID] = useState(0);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        props.api.request('/reference_products', response => {
            setReferenceProducts(response);
        });
    }

    function onClickDeleteHandle(ID, event) {
        props.api.request('/reference_product/delete?' + new URLSearchParams({
            id: ID,
        }), response => {
            fetchItems();
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
                    {_.map(referenceProducts, (value, index, array) => {
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
                    api={props.api}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                />) : (<button onClick={showCreateFormHandler}>Create</button>)}
                {editFormVisible && <EditReferenceProductForm
                    api={props.api}
                    ID={editReferenceProductID}
                    updateHandler={updateHandler}
                    closeHandler={closeEditFormHandler}
                />}
            </div>
            {/*end block__content*/}
        </div>
    );
}