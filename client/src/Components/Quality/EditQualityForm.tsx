import {useEffect, useState} from 'react';
import _ from 'lodash';

interface EditQualityFormProps {
    host: string;
    ID: number;
    closeHandler?: (event) => void;
    updateHandler?: (event) => void;
}

export default function EditQualityForm(props: EditQualityFormProps) {
    const [ID, setID] = useState(0);
    const [name, setName] = useState('');
    const [alias, setAlias] = useState('');
    const [sort, setSort] = useState(0);

    useEffect(() => {
        console.log('EditQualityForm useEffect');

        let url = props.host + '/quality/get?' + new URLSearchParams({
            id: String(props.ID),
        });
        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        //todo: Проверка наличия данных.

                        setID(value.response.id);
                        setName(value.response.name);
                        setAlias(value.response.alias);
                        setSort(value.response.sort);
                    })
                    .catch((reason) => {
                        console.log('error', reason);
                    })
            })
            .catch((reason) => {
                console.log('error', reason);
            })
        ;
    }, [props.ID]);

    function submitHandle(event) {
        event.preventDefault();
        let url = props.host + '/quality/update?' + new URLSearchParams({
            id: String(props.ID),
            name: name,
            alias: alias,
            sort: String(sort),
        });

        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ошибка. Ответ от сервера не верный. Ответ не содержит значения response.');

                        props.updateHandler?.(event);  //todo: Можно сделать один метод close на все действия.
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
            <h3>Edit quality {name} ({ID})</h3>
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