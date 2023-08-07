import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import QualitySelector from '../Quality/QualitySelector.js';

interface CreateDishFormProps {
    api: Api;
    createHandler?: (event) => void;
    closeHandler?: (event) => void;
}

export default function CreateDishForm(props: CreateDishFormProps) {
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [qualityID, setQualityID] = useState<number|null>(null);

    function onChangeNameHandle(event) {
        setName(event.target.value);
    }

    function onChangeAliasHandle(event) {
        setAlias(event.target.value);
    }

    function onChangeQualityIDHandle(ID: number) {
        setQualityID(ID);
    }

    function submitHandle(event) {
        event.preventDefault();
        props.api.request('/dish/create?' + new URLSearchParams({
            name: name,
            alias: alias,
            quality_id: String(qualityID),
        }), (response) => {
            props.createHandler?.(event);
            resetFields();
        });
    }

    function closeHandler(event) {
        event.preventDefault();
        props.closeHandler?.(event);
        resetFields();
    }

    function resetFields(): void {
        setName('');
        setAlias('');
        setQualityID(null);
    }

    return (
        <div className={'modal-form'}>
            <form action="">
                <div className={'input-group'}>
                    <span>name: </span>
                    <input className={'app-input'} type="text" value={name} onChange={onChangeNameHandle}/>
                </div>
                <div className={'input-group'}>
                    <span>alias: </span>
                    <input className={'app-input'} type="text" value={alias} onChange={onChangeAliasHandle}/>
                </div>
                <div className={'input-group'}>
                    <span>quality: </span>
                    <QualitySelector
                        api={props.api}
                        selectHandler={onChangeQualityIDHandle}
                        selectedQualityID={qualityID}
                    />
                </div>
                <div className={'input-group'}>
                    <input className={'btn'} type="submit" value={'Create'} onClick={submitHandle}/>
                    <button className={'btn'} onClick={closeHandler}>Close</button>
                </div>
            </form>
        </div>
    );
}