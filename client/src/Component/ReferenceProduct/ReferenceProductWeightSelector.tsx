import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import {ReferenceProductApiInterface} from '../../Interface/ReferenceProductApiInterface.js';

interface ReferenceProductWeightSelectorProps {
    api: Api;
    addReferenceProductHandler: (ID: number, weight: number) => void;
    removeReferenceProductHandler: (ID: number, weight: number) => void;
    referenceProductID?: number;    //todo: Должны быть в одном объекте вместе с весом.
    weight?: number;
}

export default function ReferenceProductWeightSelector(props: ReferenceProductWeightSelectorProps) {
    const [referenceProducts, setReferenceProducts] = useState<ReferenceProductApiInterface[]>([]);

    const [referenceProductID, setReferenceProductID] = useState<number|null>(null);
    const [weight, setWeight] = useState<number|null>(null);

    useEffect(() => {
        props.api.request('/reference_products', response => {
            setReferenceProducts(response);
        });
    }, []);

    useEffect(() => {
        setReferenceProductID(props.referenceProductID);
        setWeight(props.weight);
    }, [props.referenceProductID, props.weight]);

    function onChangeReferenceProductIDHandle(event) {
        setReferenceProductID(event.target.value);
    }

    function onChangeWeightHandle(event) {
        setWeight(event.target.value);
    }

    function addHandler(): void {
        if (referenceProductID) {
            props.addReferenceProductHandler(referenceProductID, weight);
            reset();
        }
    }

    function removeHandler(event): void {
        if (referenceProductID) {
            props.removeReferenceProductHandler(referenceProductID, weight);
            reset();
        }
    }

    function reset(): void {
        setReferenceProductID(null);
        setWeight(null);
    }

    function enterHandler(event): void {
        switch (event.key) {
            case 'Enter':
                addHandler();
                break;
        }
    }

    return (
        <div className={'simple-block'}>
            <select className={'app-select'} name="" id="" onChange={onChangeReferenceProductIDHandle} value={referenceProductID || ''}>
                <option value={0} key={0}>Select product...</option>
                {referenceProducts.map((value, index, array) => {
                    return <option value={value.id} key={index + 1}>{value.name}</option>;
                })}
            </select>
            <input className={'app-input'} value={weight || ''} type="text" onChange={onChangeWeightHandle} placeholder={'weight'} onKeyDown={enterHandler}/>
            <button className={'btn'} onClick={addHandler}>Add</button>
            <button className={'btn'} onClick={removeHandler}>Remove</button>
        </div>
    );
}