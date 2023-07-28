// import App from './App.js';
//
// let app = new App();

import React, { StrictMode } from "react";
// import React from "react";
import { createRoot } from "react-dom/client";
import App from './App.js';

const root = createRoot(document.getElementById("root"));

root.render(
    // <App/>
    <StrictMode>
        <App />
        {/*asd*/}
    </StrictMode>
);