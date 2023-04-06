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
    .pauseFor(2500)
    .deleteAll()
    .typeString('Si t\es pas Geek ')
    .pauseFor(2500)
    .deleteChars(7)
    .typeString('<strong>Click !</strong>')
    .pauseFor(2500)
    .start();
