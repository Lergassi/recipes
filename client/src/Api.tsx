export default class Api {
    private host: string;

    constructor(host: string) {
        this.host = host;
    }

    //todo: Generic response.
    request(path: string, callback: (response: any) => void) {
        fetch(this.host + path)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ответ от сервера не верный. Ответ не содержит значения response.');

                        callback(value.response);
                    })
                    .catch((reason) => {
                        throw reason;
                    })
                ;
            })
            .catch((reason) => {
                throw reason;
            })
        ;
    }
}