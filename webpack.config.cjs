const path = require('path');

module.exports = {
    mode: 'development',
    // mode: 'production',
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

    ],
    module: {
        rules: [

        ],
    },
    target: ['web', 'es5']
};