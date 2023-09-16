const path = require('path');
const { DefinePlugin } = require('webpack');

require('dotenv').config({
    path: path.resolve(__dirname, 'client/.env'),
});

module.exports = {
    mode: 'production',
    entry: {
        app: './client/dist/index.js',
        sandbox: './client/dist/sandbox.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'static/js/libs'),
        libraryTarget: 'amd',
    },
    plugins: [
        new DefinePlugin({
            'process.env.APP_API_URL': JSON.stringify(process.env.APP_API_URL),
        })
    ],
    module: {
        rules: [

        ],
    },
    target: ['web', 'es5']
};