const path = require('path');

module.exports = {
    mode: 'development',
    // mode: 'production',
    entry: {
        app: './client/dist/index.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'client/public/js/libs'),
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