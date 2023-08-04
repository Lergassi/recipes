import Quality from './Components/Quality/Quality.js';
import ReferenceProduct from './Components/ReferenceProduct/ReferenceProduct.js';
import Main from './Main.js';
import Api from './Api.js';

const params = {
    host: 'http://api.recipes.sd44.ru',
};

let api = new Api(
    params.host,
);

export default function App() {
    return (
        <div>
            <Main
                api={api}
            />
            <Quality
                api={api}
            />
            <ReferenceProduct
                api={api}
            />
        </div>
    );
}