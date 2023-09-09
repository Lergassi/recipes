import {useState} from 'react';
import Api from '../Api.js';

interface RegisterComponentProps {
    api: Api;
}

export default function RegisterComponent(props: RegisterComponentProps) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordRepeat, setPasswordRepeat] = useState('');

    function emailChangeHandler(event) {
        setEmail(event.target.value);
    }

    function passwordChangeHandler(event) {
        setPassword(event.target.value);
    }

    function passwordRepeatChangeHandler(event) {
        setPasswordRepeat(event.target.value);
    }

    function registerHandler(event): void {
        event.preventDefault();
        if (!email) throw Error('Поле "Почта" не может быть пустым.');
        if (!password) throw Error('Поле "Пароль" не может быть пустым.');
        if (password !== passwordRepeat) throw Error('Пароли не совпадают.');

        props.api.request('/register', {
            email: email,
            password: password,
        }, (response) => {

        });
    }

    return (
        <div className={'simple-block'}>
            <h3>Register</h3>
            <form action="">
                <div className={'input-group'}>
                    <span className={'input-group__label'}>email: </span>
                    <input className={'app-input'} type="text" onChange={emailChangeHandler}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>password: </span>
                    <input className={'app-input'} type="text" onChange={passwordChangeHandler}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>password (repeat): </span>
                    <input className={'app-input'} type="text" onChange={passwordRepeatChangeHandler}/>
                </div>
                <div className={'input-group'}>
                    <input className={'btn'} type="submit" value={'Регистрация'} onClick={registerHandler}/>
                </div>
            </form>
        </div>
    );
}