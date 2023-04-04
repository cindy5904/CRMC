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

let annonce = document.querySelector('#annonce');
let annonceHidden = document.querySelector('#annonceHidden');
let presentation  = document.querySelector('#presentation');
let presentationShow = document.querySelector('#presnetationShow');
let contact = document.querySelector('#contact')
let contactShow = document.querySelector('#contactShow')

let activesToHidden = [annonce, presentation, contact]
let targetToHidden = [annonceHidden, presentationShow, contactShow]

for (let activeToHidden in activesToHidden){
    activeToHidden.addEventListener('click', () => {
        if(annonceHidden.classList.contains('hidden')){
            annonceHidden.classList.add('hidden')
        }
        annonceHidden.classList.toggle('hidden')
       
    })
}


