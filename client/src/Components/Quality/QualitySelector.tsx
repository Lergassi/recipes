import {useEffect, useState} from 'react';
import Api from '../../Api.js';
import {QualityApiInterface} from '../../Interface/QualityApiInterface.js';

interface QualitySelectorProps {
    api: Api;
    selectHandler: (ID: number) => void;
    selectedQualityID?: number;
}

export default function QualitySelector(props: QualitySelectorProps) {
    const [qualities, setQualities] = useState<QualityApiInterface[]>([]);
    const [selectedQualityID, setSelectedQualityID] = useState<number|null>(null);

    useEffect(() => {
        props.api.request('/qualities', response => {
            setQualities(response);
            if (!selectedQualityID) {
                setSelectedQualityID(response[0]?.id);
                props.selectHandler(response[0]?.id);
            }
        });
    }, []);

    useEffect(() => {
        if (props.selectedQualityID) {
            setSelectedQualityID(props.selectedQualityID);
        }
    }, [props.selectedQualityID]);

    function onChangeQualityIDHandle(event) {
        setSelectedQualityID(event.target.value);
        props.selectHandler(event.target.value);
    }

    return (
        <select className={'app-select'} name="" id="" onChange={onChangeQualityIDHandle} value={selectedQualityID || ''}>
            {qualities.map((value, index, array) => {
                return <option value={value.id} key={index + 1}>{value.name}</option>;
            })}
        </select>
    );
}