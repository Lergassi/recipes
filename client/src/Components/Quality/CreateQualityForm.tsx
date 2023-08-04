import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';

interface CreateQualityFormProps {
    api: Api;
    createHandler?: (event) => void;
    closeHandler?: (event) => void;
}

export default function CreateQualityForm(props: CreateQualityFormProps) {
    const sortDefault = 500;

    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [sort, setSort] = useState(sortDefault);

    function onChangeNameHandle(event) {
        setName(event.target.value);
    }

    function onChangeAliasHandle(event) {
        setAlias(event.target.value);
    }

    function onChangeSortHandle(event) {
        setSort(event.target.value);
    }

    function submitHandle(event) {
        event.preventDefault();
        props.api.request('/quality/create?' + new URLSearchParams({
            name: name,
            alias: alias,
            sort: String(sort),
        }), (response) => {
            props.createHandler?.(event);
            resetFields();
        });
    }

    function closeHandler(event) {
        event.preventDefault();
        props.closeHandler?.(event);
    }

    function resetFields(): void {
        setName('');
        setAlias('');
        setSort(sortDefault);
    }

    return (
        <div>
            <h3>Create quality</h3>
            <form action="">
                <div>
                    <input name={'name'} type="text" value={name} onChange={onChangeNameHandle}/>
                </div>
                <div>
                    <input name={'sort'} type="text" value={alias} onChange={onChangeAliasHandle}/>
                </div>
                <div>
                    <input name={'sort'} type="text" value={sort} onChange={onChangeSortHandle}/>
                </div>
                <input type="submit" value={'Create'} onClick={submitHandle}/>
                <button onClick={closeHandler}>Close</button>
            </form>
        </div>
    );
}