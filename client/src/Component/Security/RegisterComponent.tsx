import {useState} from 'react';
import Api from '../../Api.js';
import {setCookie} from '../../cookie.js';

interface RegisterComponentProps {
    api: Api;
    setApiKeyHandler: (apiKey: string) => void;
}

export default function RegisterComponent(props: RegisterComponentProps) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordRepeat, setPasswordRepeat] = useState('');

    const [registerState, setRegisterState] = useState('form');

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

        setRegisterState('register');

        // props.api.request('/register', {
        //     email: email,
        //     password: password,
        // }, (response) => {
        //     props.setApiKeyHandler(response);
        // }, error => {
        //     setRegisterState('form');
        // });
    }

    return (
        <div className={'simple-block'}>
            <h3>Register</h3>
            <form action=".">
                <div className={'input-group'}>
                    <span className={'input-group__label'}>email: </span>
                    <input className={'app-input'} type="text" onChange={emailChangeHandler} disabled={registerState === 'register'}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>password: </span>
                    <input className={'app-input'} type="password" onChange={passwordChangeHandler} disabled={registerState === 'register'}/>
                </div>
                <div className={'input-group'}>
                    <span className={'input-group__label'}>password (repeat): </span>
                    <input className={'app-input'} type="password" onChange={passwordRepeatChangeHandler} disabled={registerState === 'register'}/>
                </div>
                <div className={'input-group'}>
                    <input className={'btn'} type="submit" value={'Регистрация'} onClick={registerHandler} disabled={registerState === 'register'}/>
                </div>
            </form>
        </div>
    );
}