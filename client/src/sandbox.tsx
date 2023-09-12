import React, { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from './Component/App.js';
import {transliterate} from 'transliteration';
import {generateAlias} from './generateAlias.js';

// const root = createRoot(document.getElementById("root"));
//
// root.render(
//     <StrictMode>
//         <App />
//     </StrictMode>
// );

// console.log(transliterate('привет МИР').replace(' ', '_'));

console.log(generateAlias('Плов из КУРИЦЫ!'));
// console.log(alias('1            1             1'));
// console.log(alias('1            1             1'));