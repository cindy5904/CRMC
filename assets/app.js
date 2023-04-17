/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import 'flowbite';

var app = document.getElementById('app');

var typewriter = new Typewriter(app, {
    loop: true
});

typewriter.typeString('[ La reconversion c\'est pas une option... ]')
    .pauseFor(1000)
    .deleteAll()
    .typeString('Si t\'es pas Geek ')
    .pauseFor(1000)
    .deleteAll()
    .typeString('<strong> Click !</strong>')
    .pauseFor(1000)
    .start();    

// Po
setTimeout(() => {
    document.querySelectorAll('.flash-messages').forEach(el => el.remove());
}, 1000);

import { initTabs } from 'flowbite';

document.addEventListener('swup:pageView', () => {
    initTabs();
});

import { initAccordions } from 'flowbite';

document.addEventListener('swup:pageView', () => {
    initAccordions();
});

import { initCarousels } from 'flowbite';

document.addEventListener('swup:pageView', () => {
    initCarousels();
});


// const themeButton = document.querySelector('.theme i');
// const isHomepage = themeButton.getAttribute('data-is-homepage') === '1';

// if (isHomepage && document.body.classList.contains('dark')) {
//     themeButton.classList.remove('bx-moon');
//     themeButton.classList.add('bx-sun');
// }

// themeButton.addEventListener('click', () => {
//     if (isHomepage) {
//         document.body.classList.toggle('dark');
//         themeButton.classList.toggle('bx-moon');
//         themeButton.classList.toggle('bx-sun');
//     }
// });