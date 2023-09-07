import {useEffect, useState} from 'react';

export default function TestEffect() {
    const [count, setCount] = useState(0);

    useEffect(() => {
        console.log('useEffect TestEffect');
        // setCount(42);
        // Обновляем заголовок документа с помощью API браузера
        document.title = `Вы нажали ${count} раз`;

        // return () => {
        //     console.log('return TestEffect');
        // };
    }, []);
    // });

    function _update() {
        console.log('update');
        setCount(count + 1);
    }

    return (
        <div>
            <p>Вы нажали {count} раз</p>
            <button onClick={() => setCount(count + 1)}>
                Нажми на меня
            </button>
            {/*<button onClick={_update}>*/}
            {/*    Нажми на меня*/}
            {/*</button>*/}
        </div>
    );
}