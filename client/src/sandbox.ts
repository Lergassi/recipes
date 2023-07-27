import _ from 'lodash';

// let data = {
//     name: 'Common',
//     alias: 'common',
//     sort: '500',
// };
import {defaultsDeep, random} from 'lodash';

let data = {
    name: 'Uncommon',
    alias: 'uncommon',
    sort: '510',
};
console.log(data);

let host = 'http://api.recipes.sd44.ru';
let createUrl = host + '/quality/create?' + new URLSearchParams(data);
// let url = host + '/quality/create?' + new URLSearchParams({
//     name: 'Common',
//     alias: 'common',
//     sort: 500,
// });
// console.log(createUrl);
// console.log(new URLSearchParams({name: 'asd'}));
// console.log((new URLSearchParams({name: 'asd'})).toString());
// console.log((new URLSearchParams(data)).toString());
// request(createUrl);

// let ID = 1;
let ID = 42;
let r = random(0, 42000, false);
let updateData = {
    id: ID.toString(),
    name: 'alias' + r,
    alias: 'alias' + r,
    sort: '500' + r,
};

let updateUrl = host + '/quality/update?' + new URLSearchParams(updateData);
// console.log(updateUrl);
request(updateUrl);

function request(url: string) {
    fetch(url)
        .then((response) => {
            response.text()
                .then((value) => {
                    console.log(value);
                })
                .catch((reason) => {
                    console.log(reason);
                });
        })
        .catch((reason) => {
            console.log(reason);
        });
}