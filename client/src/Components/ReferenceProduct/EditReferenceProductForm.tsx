import {useEffect, useState} from 'react';
import _ from 'lodash';
import Api from '../../Api.js';

interface EditQualityFormProps {
    api: Api;
    ID: number;
    closeHandler?: (event) => void;
    updateHandler?: (event) => void;
}

export default function EditReferenceProductForm(props: EditQualityFormProps) {
    const [ID, setID] = useState(0);
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [sort, setSort] = useState(0);

    useEffect(() => {
        props.api.request('/reference_product/get?' + new URLSearchParams({
            id: String(props.ID),
        }), response => {
            //todo: Проверка наличия данных.

            setID(response.id);
            setName(response.name);
            setAlias(response.alias);
            setSort(response.sort);
        });
    }, [props.ID]);

    function submitHandle(event) {
        event.preventDefault();
        props.api.request('/reference_product/update?' + new URLSearchParams({
            id: String(props.ID),
            name: name,
            alias: alias,
            sort: String(sort),
        }), response => {
            props.updateHandler?.(event);  //todo: Можно сделать один метод close на все действия.
        });
    }

    function onChangeNameHandle(event) {
        setName(event.target.value);
    }

    function onChangeAliasHandle(event) {
        setAlias(event.target.value);
    }

    function onChangeSortHandle(event) {
        setSort(event.target.value);
    }

    function closeHandler(event) {
        event.preventDefault();
        props.closeHandler?.(event);
    }

    return (
        <div>
            <h3>Edit reference product {name} ({ID})</h3>
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
                <input type="submit" value={'Update'} onClick={submitHandle}/>
                <button onClick={closeHandler}>Close</button>
            </form>
        </div>
    );
}