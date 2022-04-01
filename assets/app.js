/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import 'bootstrap';

const formBed = document.querySelector('.form-bed');
const ul = document.querySelector('.beds');
const saveButton = document.querySelector("#save");

function addFormToCollection(e){
    const li = document.createElement('li');
    li.innerHTML = formBed.innerHTML.replace(/__name__/g, ul.dataset.index)

    ul.appendChild(li);
    ul.dataset.index++;
}

const addItem = document.querySelector("#add_item_link");

addItem.addEventListener("click", addFormToCollection);
saveButton.addEventListener("click", () => {
    formBed.remove();
})
