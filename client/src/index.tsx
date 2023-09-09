import React, { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from './Component/App.js';
import RegisterComponent from './Component/RegisterComponent.js';
import Api from './Api.js';

const params = {
    host: 'http://dev.api.recipes.sd44.ru',
};

let api = new Api(
    params.host,
);

const root = createRoot(document.getElementById("root"));

root.render(
    <StrictMode>
        {/*
        проверка аутентификации
            запрос на сервер с ключом из куки
        если нет
            форма регистрации и входа
        иначе основные компоненты
        */}
        <RegisterComponent
            api={api}
        />
        <App
            api={api}
        />
    </StrictMode>
);