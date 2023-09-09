import React, { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from './Component/App.js';
import Api from './Api.js';
import {getCookie} from './cookie.js';

let api = new Api(
    process.env.APP_API_URL,
);

//todo: Переделать в хуки.
let apiKey = getCookie('api_key');
if (apiKey) {
    api.apiKey = apiKey;
}

const root = createRoot(document.getElementById("root"));

root.render(
    <StrictMode>
        <App
            api={api}
        />
    </StrictMode>
);