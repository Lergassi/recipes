import Quality from './Components/Quality/Quality.js';
import ReferenceProduct from './Components/ReferenceProduct/ReferenceProduct.js';
import RecipeManager from './RecipeManager.js';
import Api from './Api.js';
import {useState} from 'react';
import {EntityID} from './Types/EntityID.js';

const params = {
    host: 'http://api.recipes.sd44.ru',
};

let api = new Api(
    params.host,
);

export default function App() {
    const [appDataSelectedID, setAppDataSelectedID] = useState('');

    function appDataSelectHandler(key: string, event): void {
        setAppDataSelectedID(key);
    }

    return (
        <div>
            <RecipeManager
                api={api}
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
                        api={api}
                    />}
                    {appDataSelectedID === EntityID.ReferenceProduct && <ReferenceProduct
                        api={api}
                    />}
                </div>
            </div>
        </div>
    );
}