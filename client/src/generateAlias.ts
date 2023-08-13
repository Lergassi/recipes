import {transliterate} from 'transliteration';
import _ from 'lodash';

export function generateAlias(name: string): string {
    name = _.lowerCase(name);
    name = name.replace(/ /g, '_');

    return transliterate(name);
}