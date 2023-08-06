import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import QualitySelector from '../Quality/QualitySelector.js';

interface EditDishFormProps {
    api: Api;
    ID: number;
    updateHandler?: (event) => void;
    closeHandler?: (event) => void;
}

export default function EditDishVersionForm(props: EditDishFormProps) {
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [qualityID, setQualityID] = useState<number|null>(null);

    useEffect(() => {
        props.api.request('/dish_version/get?' + new URLSearchParams({
            id: String(props.ID),
        }), response => {
            setName(response.name);
            setAlias(response.alias);
            setQualityID(response.quality_id);
        });
    }, [props.ID]);

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
        props.api.request('/dish_version/update?' + new URLSearchParams({
            id: String(props.ID),
            name: name,
            alias: alias,
            quality_id: String(qualityID),
        }), (response) => {
            resetFields();
            props.updateHandler?.(event);
        });
    }

    function closeHandler(event) {
        event.preventDefault();
        props.closeHandler?.(event);
    }

    function resetFields(): void {
        setName('');
        setAlias('');
        setQualityID(0);
    }

    return (
        <div>
            <h3>Update {name} ({props.ID})</h3>
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
                        selectedQualityID={qualityID}
                    />
                </div>
                <input type="submit" value={'Update'} onClick={submitHandle}/>
                <button onClick={closeHandler}>Close</button>
            </form>
        </div>
    );
}