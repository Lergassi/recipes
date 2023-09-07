import React, {useEffect, useState} from 'react';

export default function Example() {
    const [count, setCount] = useState(0);

    useEffect(() => {
        console.log('useEffect example');
        // _update();
        // setCount(42);
        // document.title = `Вы нажали ${count} раз`;

        return () => {
            console.log('return example');
        };
    }, []);
    // });

    function _update() {
        console.log('update');
        setCount(count + 1);
    }

    return (
        <div>
            <button onClick={() => {
                setCount(count + 1);
            }}>count</button>
            {count}
        </div>
    );
}