import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import QualitySelector from '../Quality/QualitySelector.js';
import {generateAlias} from '../../generateAlias.js';

interface CreateDishFormProps {
    api: Api;
    dishID: number;
    createHandler?: (event) => void;
    closeHandler?: (event) => void;
}

export default function CreateDishVersionForm(props: CreateDishFormProps) {
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [qualityID, setQualityID] = useState<number|null>(null);

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

    function onChangeQualityIDHandle(ID: number) {
        setQualityID(ID);
    }

    function submitHandle(event) {
        event.preventDefault();
        props.api.request('/dish_version/create', {
            dish_id: String(props.dishID),
            name: name,
            alias: alias,
            quality_id: String(qualityID),
        }, (response) => {
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
        setQualityID(null);
    }

    return (
        <div className={'modal-form'}>
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
                    <span className={'input-group__label'}>quality: </span>
                    <QualitySelector
                        api={props.api}
                        selectHandler={onChangeQualityIDHandle}
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