import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import {QualityApiInterface} from '../../Interface/QualityApiInterface.js';
import {ReferenceProductApiInterface} from '../../Interface/ReferenceProductApiInterface.js';

interface ReferenceProductWeightSelectorProps {
    api: Api;
    addProductHandler: (ID: number, weight: number) => void;
    removeProductHandler: (ID: number, weight: number) => void;
    referenceProductID?: number;
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

    function addHandler(event): void {
        props.addProductHandler(referenceProductID, weight);
        reset();
    }

    function removeHandler(event): void {
        props.removeProductHandler(referenceProductID, weight);
        reset();
    }

    function reset(): void {
        setReferenceProductID(null);
        setWeight(null);
    }

    return (
        <div>
            <select name="" id="" onChange={onChangeReferenceProductIDHandle} value={referenceProductID || ''}>
                <option value={0} key={0}>Select...</option>
                {referenceProducts.map((value, index, array) => {
                    return <option value={value.id} key={index + 1}>{value.name}</option>;
                })}
            </select>
            <input value={weight || ''} type="text" onChange={onChangeWeightHandle}/>
            <button onClick={addHandler}>Add</button>
            <button onClick={removeHandler}>Remove</button>
        </div>
    );
}