// const addFormToCollection = (e) => {
//     const collectionHolder = document.querySelector(('.') + e.currentTarget.dataset.collectionHolderClass);
//     const item = document.createElement('li');
//     item.innerHtml = collectionHolder
//         .dataset
//         .prototype
//         .replace(
//             /__name__/g,
//             collectionHolder.dataset.index
//         );
//     collectionHolder.appendChild(item);
//     collectionHolder.dataset.index++;
// };
import './calendar'

let formBed = document.querySelector('.form-bed')
let ul = document.querySelector('.beds')

function addFormToCollection(e) {
    const li = document.createElement('li')
    li.innerHTML = formBed.innerHTML.replace(/__name__/g, ul.dataset.index)

    ul.appendChild(li)
    ul.dataset.index++
}

const addItem = document.querySelector(".add_item_link");
addItem.addEventListener('click', addFormToCollection)

const btnSubmit = document.querySelector('.submit')
btnSubmit.addEventListener('click', () => { formBed.remove()})

