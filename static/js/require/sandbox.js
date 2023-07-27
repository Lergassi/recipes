requirejs.config({
        baseUrl: '/js/libs',
        paths: {
            app: '../app',  //todo: Что это?
        },
        urlArgs: "v=" + (new Date()).getTime(),
});

require(
    [
        'sandbox.bundle',
    ],
    function(
        appBundle,
        ) {

    }
);