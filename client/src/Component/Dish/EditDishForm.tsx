import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';
import QualitySelector from '../Quality/QualitySelector.js';
import {generateAlias} from '../../generateAlias.js';

interface EditDishFormProps {
    api: Api;
    ID: number;
    updateHandler?: (event) => void;
    closeHandler?: (event) => void;
}

export default function EditDishForm(props: EditDishFormProps) {
    const [ID, setID] = useState(0);
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [qualityID, setQualityID] = useState<number|null>(null);

    const [originalName, setOriginalName] = useState('');
    const [autoGenerateAlias, setAutoGenerateAlias] = useState(true);

    useEffect(() => {
        props.api.request('/dish/get?' + new URLSearchParams({
            id: String(props.ID),
        }), response => {
            setID(response.id);
            setName(response.name);
            setAlias(response.alias);
            setQualityID(response.quality.id);

            setOriginalName(response.name);
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
        props.api.request('/dish/update?' + new URLSearchParams({
            id: String(ID),
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
        <div className={'modal-form'}>
            <h3>Update {originalName} ({ID})</h3>
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
                        selectedQualityID={qualityID}
                    />
                </div>
                <div className={'input-group'}>
                    <input className={'btn'} type="submit" value={'Update'} onClick={submitHandle}/>
                    <button className={'btn'} onClick={closeHandler}>Close</button>
                </div>
            </form>
        </div>
    );
}