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

let annonce = document.querySelector('#annonce');
let annonceShow = document.querySelector('#annonceShow');
let presentation  = document.querySelector('#presentation');
let presentationShow = document.querySelector('#presentationShow');
let contact = document.querySelector('#contact')
let contactShow = document.querySelector('#contactShow')

if (annonce) {
    annonce.addEventListener('click', () => {
        if(annonceShow.classList.contains('hidden')){
            annonceShow.classList.add('hidden')
        }
        annonceShow.classList.toggle('hidden')
        presentationShow.classList.add('hidden')
        contactShow.classList.add('hidden')
    })
    
    presentation.addEventListener('click', () => {
        if(presentationShow.classList.contains('hidden')){
            presentationShow.classList.add('hidden')
            
        }
        presentationShow.classList.toggle('hidden')
        annonceShow.classList.add('hidden')
        contactShow.classList.add('hidden')
    })
    
    contact.addEventListener('click', () => {
        if(contactShow.classList.contains('hidden')){
            contactShow.classList.add('hidden')
            
        }
        contactShow.classList.toggle('hidden')
        annonceShow.classList.add('hidden')
        presentationShow.classList.add('hidden')
    })
}
// Po
setTimeout(() => {
    document.querySelectorAll('.flash-messages').forEach(el => el.remove());
}, 1000);