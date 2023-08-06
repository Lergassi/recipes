import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import QualitySelector from '../Quality/QualitySelector.js';

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
        props.api.request('/dish_version/create?' + new URLSearchParams({
            dish_id: String(props.dishID),
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
    }

    function resetFields(): void {
        setName('');
        setAlias('');
        setQualityID(null);
    }

    return (
        <div>
            <h3>Create dish version for {props.dishID}</h3>
            <form action="">
                <div>
                    <span>name: </span>
                    <input type="text" value={name} onChange={onChangeNameHandle}/>
                </div>
                <div>
                    <span>alias: </span>
                    <input type="text" value={alias} onChange={onChangeAliasHandle}/>
                </div>
                <div>
                    <QualitySelector
                        api={props.api}
                        selectHandler={onChangeQualityIDHandle}
                    />
                </div>
                <input type="submit" value={'Create'} onClick={submitHandle}/>
                <button onClick={closeHandler}>Close</button>
            </form>
        </div>
    );
}