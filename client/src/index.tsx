import React, { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from './Component/App.js';
import Api from './Api.js';

let api = new Api(
    process.env.APP_API_URL,
);

if (process.env.NODE_ENV !== 'production') {
    console.log('This dev environment.');
}

const root = createRoot(document.getElementById("root"));

root.render(
    <App
        api={api}
    />
);