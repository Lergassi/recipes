import {useEffect, useState} from 'react';
import Api from '../../Api.js';

interface DishListProps {
    api: Api;
    selectHandler?: (ID: number, event) => void;
}

// todo: Возможно стоит переименовать в Selector.
export default function DishList(props: DishListProps) {
    const [dishes, setDishes] = useState([]);

    useEffect(() => {
        fetchItems();
    }, []);

    function fetchItems() {
        props.api.request('/dishes', response => {
            setDishes(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
        props.selectHandler?.(ID, event);
    }

    return (
        <div>
            <h3>Dishes</h3>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>control</th>
                    </tr>
                    {dishes.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>
                                    <button onClick={selectHandler.bind(this, value.id)}>Select</button>
                                    <button>Detail</button>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    );
}