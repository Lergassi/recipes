requirejs.config({
        baseUrl: '/js/libs',
        paths: {
            app: '../app',  //todo: Что это?
        },
        urlArgs: "v=" + (new Date()).getTime(),
});

require(
    [
        'app.bundle',
    ],
    function(
        appBundle,
        ) {
        // console.log(appBundle);
    }
);