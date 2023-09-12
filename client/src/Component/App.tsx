import Quality from './Quality/Quality.js';
import ReferenceProduct from './ReferenceProduct/ReferenceProduct.js';
import RecipeManager from './RecipeManager.js';
import Api from '../Api.js';
import {useContext, useEffect, useState} from 'react';
import {EntityID} from '../Type/EntityID.js';
import AuthComponent from './Security/AuthComponent.js';
import {getCookie, setCookie} from '../cookie.js';
import UserContext from '../Context/UserContext.js';
import UserService from '../UserService.js';
import {UserInterface} from '../Interface/UserInterface.js';

interface AppProps {
    api: Api;
}

export default function App(props: AppProps) {
    const [user, setUser] = useState<UserInterface>(null);
    const [appDataSelectedID, setAppDataSelectedID] = useState('');

    useEffect(() => {
        let apiKey = getCookie('api_key');
        if (apiKey) {
            getUser(apiKey);
        }
    }, []);

    function appDataSelectHandler(key: string, event): void {
        setAppDataSelectedID(key);
    }

    function getUser(apiKey: string): void {
        props.api.request('/user', {
            api_key: apiKey,
        }, (response) => {
            if (response) {
                setUser(new UserService(response.email, response.user_groups));
                props.api.apiKey = apiKey;
            }
        });
    }

    return (
        <UserContext.Provider value={user}>
            {!user && <AuthComponent
                api={props.api}
                setApiKeyHandler={(apiKey) => {
                    setCookie('api_key', apiKey);
                    getUser(apiKey);
                }}
            />}
            {user && <div>
                <RecipeManager
                    api={props.api}
                />
                <div className={'component-group'}>
                    <div className={'component-wrapper app-data-list'}>
                        <h3>App data</h3>
                        <div className={'item-list simple-block'}>
                            <div className={'item-list__item'} onClick={appDataSelectHandler.bind(this, EntityID.Quality)}>
                                <span className={'item-list__item-text'}>Qualities</span>
                            </div>
                            <div className={'item-list__item'} onClick={appDataSelectHandler.bind(this, EntityID.ReferenceProduct)}>
                                <span className={'item-list__item-text'}>Reference products</span>
                            </div>
                        </div>
                    </div>
                    <div className={'component-wrapper'}>
                        {appDataSelectedID === EntityID.Quality && <Quality
                            api={props.api}
                        />}
                        {appDataSelectedID === EntityID.ReferenceProduct && <ReferenceProduct
                            api={props.api}
                        />}
                    </div>
                </div>
            </div>}
        </UserContext.Provider>
    );
}