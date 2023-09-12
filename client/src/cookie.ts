function parseCookie(source: string): object {
    let cookieArray = source.split(';')
    cookieArray = cookieArray.map((value, index, array) => {
        return value.trim();
    });

    let cookie = {};
    for (let i = 0; i < cookieArray.length; i++) {
        let cookieItem = cookieArray[i].split('=');
        cookie[cookieItem[0]] = cookieItem[1];
    }

    return cookie;
}

function setCookie(key: string, value: string): void {
    document.cookie = key + '=' + value;
}

function getCookie(key: string): string {
    return parseCookie(document.cookie)[key] ?? null;
}

export {parseCookie, setCookie, getCookie};