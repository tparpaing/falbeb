document.querySelector('#menu-opener').addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();

    document.querySelector('.menu').setAttribute('data-opened', true);
});

document.querySelector('#menu-closer').addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();

    document.querySelector('.menu').setAttribute('data-opened', false);
});