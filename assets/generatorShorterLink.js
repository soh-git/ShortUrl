const form = document.querySelector("#shortForm");
const shortenCard= document.querySelector("#shortenCard");
const btnShortenUrl = document.querySelector("#btnShortenUrl");
const inputUrl = document.querySelector("#url");

const errorMessages = {
    'INVALID_ARG_URL': "Impossible de raccourcir ce lien. Ce n'est pas une URL valide",
    'MISSING_ARG_URL': "Veuillez fournir une URL valide"
}

const URL_SHORTEN="/ajax/shorten";
form.addEventListener('submit',function(e) {
    e.preventDefault();
    fetch(URL_SHORTEN,{
        method: 'POST',
        body: new FormData(e.target )
    })
    .then(Response=>Response.json())
    .then(handleData)
});


const handleData = function(data) {
    if (data.statusCode >= 400) {
        return handleError(data);
    }

    inputUrl.value = data.link;
    btnShortenUrl.innerText = "Copy";

    btnShortenUrl.addEventListener('click', function(e) {
       e.preventDefault();

       inputUrl.select();
       document.execCommand('copy');

       this.innerText = "shorten";
    }, { once: true });
}

const handleError = function(data) {
    const alert = document.createElement('div');
    alert.classList.add('alert', 'alert-danger', 'mt-2');
    console.log(data);
    alert.innerText = errorMessages[data.statusText];

    shortenCard.after(alert);
}