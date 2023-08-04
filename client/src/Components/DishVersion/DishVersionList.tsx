import {useEffect, useState} from 'react';
import Api from '../../Api.js';

interface DishVersionListProps {
    api: Api;
    dishID?: number;
    selectHandler?: (ID: number, event) => void;
}

export default function DishVersionList(props: DishVersionListProps) {
    const [dishVersions, setDishVersions] = useState([]);

    useEffect(() => {
        if (props.dishID) {
            fetchItems(props.dishID);
        }
    }, [props.dishID]);

    function fetchItems(dishID: number) {
        props.api.request('/dish_versions?' + new URLSearchParams({
            dish_id: String(dishID),
        }), response => {
            setDishVersions(response);
        });
    }

    function selectHandler(ID: number, event): void {
        event.preventDefault();
        props.selectHandler?.(ID, event);
    }

    return (
        <div>
            <h3>Dish versions</h3>
            <div>
                <table className={'base-table'}>
                    <tbody>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>alias</th>
                        <th>quality</th>
                        <th>control</th>
                    </tr>
                    {dishVersions.map((value, index, array) => {
                        return (
                            <tr key={index}>
                                <td>{value.id}</td>
                                <td>{value.name}</td>
                                <td>{value.alias}</td>
                                <td>{value.quality_id}</td>
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