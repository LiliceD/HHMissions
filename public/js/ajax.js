function ajaxPost(url, param, callback) {
    let xhr = new XMLHttpRequest(),
        params = '';

    for (key in param) {
        params += key + '=' + encodeURIComponent(param[key]) + '&';
    }
    params = params.slice(0, params.length - 1);

    xhr.open('POST', url);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.addEventListener('readystatechange', function() {

        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            callback(xhr.responseText);
        } else if (xhr.readyState === XMLHttpRequest.DONE && xhr.status !== 200) {
            console.error('Erreur lors de la requête Ajax');
        }
    });

    xhr.send(params);

    return xhr;
}
