import {useContext, useEffect, useState} from 'react';
import _ from 'lodash';
import CreateReferenceProductForm from './CreateReferenceProductForm.js';
import EditReferenceProductForm from './EditReferenceProductForm.js';
import {ReferenceProductApiInterface} from '../../Interface/ReferenceProductApiInterface.js';
import Api from '../../Api.js';
import UserContext from '../../Context/UserContext.js';
import {UserGroupID} from '../../Type/UserGroupID.js';

interface ReferenceProductProps {
    api: Api;
}

export default function ReferenceProduct(props: ReferenceProductProps) {
    const [referenceProducts, setReferenceProducts] = useState<ReferenceProductApiInterface[]>([]);

    const [createFormVisible, setCreateFormVisible] = useState(false);

    const [editFormVisible, setEditFormVisible] = useState(false);
    const [editReferenceProductID, setEditReferenceProductID] = useState(0);

    const userContext = useContext(UserContext);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        props.api.request('/reference_products', {}, response => {
            setReferenceProducts(response);
        });
    }

    function onClickDeleteHandle(ID, event) {
        props.api.request('/reference_product/delete', {
            id: ID,
        }, (response) => {
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
            <div className={'simple-block'}>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>sort</th>
                        {userContext?.hasGroup(UserGroupID.Admin) && (
                            <th>control</th>
                        )}
                    </tr>
                    {_.map(referenceProducts, (value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>{value.sort}</td>
                                {userContext?.hasGroup(UserGroupID.Admin) && (
                                    <td>
                                        <button className={'btn'} onClick={showEditFormHandler.bind(this, value.id)}>Edit</button>
                                        <button className={'btn'} onClick={onClickDeleteHandle.bind(this, value.id)}>Delete</button>
                                    </td>
                                )}
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
            <div className={'simple-block'}>
                {userContext?.hasGroup(UserGroupID.Admin) && (createFormVisible ? (<CreateReferenceProductForm
                    api={props.api}
                    createHandler={createHandler}
                    closeHandler={hideCreateFormHandler}
                />) : (<button className={'btn'} onClick={showCreateFormHandler}>Create</button>))}
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