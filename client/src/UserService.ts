import {UserInterface} from './Interface/UserInterface.js';

export default class UserService implements UserInterface {
    private email: string;
    private userGroups: string[];

    constructor(
        email: string,
        userGroups: string[],
    ) {
        this.email = email;
        this.userGroups = userGroups;
    }

    hasGroup(userGroupID: string): boolean {
        return this.userGroups.indexOf(userGroupID) !== -1;
    }
}