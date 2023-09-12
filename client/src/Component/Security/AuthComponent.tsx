import React, {useState} from 'react';
import RegisterComponent from './RegisterComponent.js';
import LoginComponent from './LoginComponent.js';
import Api from '../../Api.js';

interface AuthComponentProps {
    api: Api;
    setApiKeyHandler: (apiKey: string) => void;
}

export default function AuthComponent(props: AuthComponentProps) {
    const [action, setAction] = useState('login');

    function actionChangeHandler(action: string, event): void {
        setAction(action);
    }

    return (
        <span>
            <div>
                <a href={'#'} onClick={actionChangeHandler.bind(this, 'login')}>Login</a>/
                <a href={'#'} onClick={actionChangeHandler.bind(this, 'register')}>Register</a>
            </div>
            {action === 'register' && <RegisterComponent
                api={props.api}
                setApiKeyHandler={props.setApiKeyHandler}
            />}
            {action === 'login' && <LoginComponent
                api={props.api}
                setApiKeyHandler={props.setApiKeyHandler}
            />}
        </span>
    );
}