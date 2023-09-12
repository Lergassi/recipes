export default class Api {
    private host: string;
    private _apiKey: string;    //todo: Нужно переделать в состояние для изменения при получении. hook?

    constructor(host: string) {
        this.host = host;
    }

    get apiKey(): string {
        return this._apiKey;
    }

    set apiKey(value: string) {
        this._apiKey = value;
    }

    //todo: Generic response.
    request(path: string, params: any, callback: (response: any) => void) {
        if (this._apiKey) {
            params['api_key'] = this._apiKey;
        }

        let url = this.host + path + '?' + new URLSearchParams(params);
        // console.log(url);   //todo: debug
        fetch(url)
            .then((value) => {
                value.json()
                    .then((value) => {
                        if (value.hasOwnProperty('error')) throw new Error(value.error);
                        // if (value.hasOwnProperty('error')) return callback(null, value.error);
                        if (!value.hasOwnProperty('response')) throw new Error('Ответ от сервера не верный. Ответ не содержит значения response.');
                        // if (!value.hasOwnProperty('response')) return callback(null, 'Ответ от сервера не верный. Ответ не содержит значения response.');

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