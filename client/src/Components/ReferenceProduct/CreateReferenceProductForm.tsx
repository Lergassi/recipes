import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import {generateAlias} from '../../generateAlias.js';

interface CreateReferenceProductFormProps {
    api: Api;
    createHandler?: (ID: number) => void;
    closeHandler?: any;
}

export default function CreateReferenceProductForm(props: CreateReferenceProductFormProps) {
    const sortDefault = 500;

    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [sort, setSort] = useState(sortDefault);
    const [autoGenerateAlias, setAutoGenerateAlias] = useState(true);

    function onChangeNameHandle(event) {
        setName(event.target.value);
                if(autoGenerateAlias) {
            setAlias(generateAlias(event.target.value));
        }
    }

    function onChangeAliasHandle(event) {
        setAlias(event.target.value);
        setAutoGenerateAlias(false);
    }

    function onChangeSortHandle(event) {
        setSort(event.target.value);
    }

    function submitHandle(event) {
        event.preventDefault();
        props.api.request('/reference_product/create?' + new URLSearchParams({
            name: name,
            alias: alias,
            sort: String(sort),
        }), response => {
            props.createHandler?.(Number(response));
        });
    }

    function resetFields(): void {
        setName('');
        setAlias('');
        setSort(sortDefault);
    }

    function closeHandler(event) {
        event.preventDefault();
        props.createHandler?.(event);
    }

    return (
        <div>
            <h3>Create reference product</h3>
            <form action="">
                <div className={'input-group'}>
                    <span className={'input-group__label'}>name: </span>
                    <input className={'app-input'} type="text" value={name} onChange={onChangeNameHandle}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>alias: </span>
                    <input className={'app-input'} type="text" value={alias} onChange={onChangeAliasHandle}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>sort: </span>
                    <input className={'app-input'} name={'sort'} type="text" value={sort} onChange={onChangeSortHandle}/>
                </div>
                <div className={'input-group'}>
                    <input className={'btn'} type="submit" value={'Create'} onClick={submitHandle}/>
                    <button className={'btn'} onClick={closeHandler}>Close</button>
                </div>
            </form>
        </div>
    );
}