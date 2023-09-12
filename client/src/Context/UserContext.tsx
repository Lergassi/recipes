import {createContext} from 'react';
import {UserInterface} from '../Interface/UserInterface.js';

// const UserContext = createContext<string[]>(null);
// const UserContext = createContext<any>(null);
const UserContext = createContext<UserInterface>(null);

export default UserContext;

