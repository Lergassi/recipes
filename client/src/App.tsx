import Quality from './Components/Quality/Quality.js';
import {useState} from 'react';
import TestEffect from './Components/Test/TestEffect.js';
import Example from './Components/Test/Example.js';
import ReferenceProduct from './Components/ReferenceProduct/ReferenceProduct.js';

const params = {
    host: 'http://api.recipes.sd44.ru',
};

export default function App() {
    return (
        <div>
            <Quality
                host={params.host}
            />
            <ReferenceProduct
                host={params.host}
            />
        </div>
    );
}