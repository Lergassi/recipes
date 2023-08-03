import {useEffect, useState} from 'react';
import _ from 'lodash';

interface CreateQualityFormProps {
    host: string;
    // createHandler?: (ID: number) => void;
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
        let url = props.host + '/quality/create?' + new URLSearchParams({
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

                        /*
                            Все события пока будут через dom с event в аргументе.
                            Ответ от сервера за пределами компонента пока будет считаться лишним.
                         */
                        // props.createHandler?.(Number(value.response));
                        props.createHandler?.(event);
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