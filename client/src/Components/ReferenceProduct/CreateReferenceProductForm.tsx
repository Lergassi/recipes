import {useEffect, useState} from 'react';
import _ from 'lodash';

interface CreateReferenceProductFormProps {
    host: string;
    createHandler?: (ID: number) => void;
    closeHandler?: any;
}

export default function CreateReferenceProductForm(props: CreateReferenceProductFormProps) {
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
        let url = props.host + '/reference_product/create?' + new URLSearchParams({
            name: name,
            alias: alias,
            sort: String(sort),
        });
        resetFields();

        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        props.createHandler?.(Number(value.response));
                    })
                    .catch((reason) => {
                        console.log('error', reason);
                    })
            })
            .catch((reason) => {
                console.log('error', reason);
            })
        ;
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
                <div>
                    <input name={'name'} type="text" value={name} onChange={onChangeNameHandle}/>
                </div>
                <div>
                    <input name={'sort'} type="text" value={alias} onChange={onChangeAliasHandle}/>
                </div>
                <div>
                    <input name={'sort'} type="text" value={sort} onChange={onChangeSortHandle}/>
                </div>
                <input type="submit" value={'create'} onClick={submitHandle}/>
                <button onClick={closeHandler}>close</button>
            </form>
        </div>
    );
}