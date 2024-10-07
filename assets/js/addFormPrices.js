window.onload = function() {
    document
        .querySelectorAll('.add_item_link')
        .forEach(btn => {
            btn.addEventListener("click", addFormToCollection)
        });

    document
        .querySelectorAll('.delete-price')
        .forEach(btn => {
            btn.addEventListener("click", removePriceContainer)
        });

    function addFormToCollection(e) {
        const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
        const item = document.createElement('div');

        item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(
                /__name__/g,
                collectionHolder.dataset.index
            );

        collectionHolder.appendChild(item);
        collectionHolder.dataset.index++;

        addTagFormDeleteLink(item);
    };

    function addTagFormDeleteLink(item) {
        const removeFormButton = document.createElement('button');
        removeFormButton.innerText = 'Supprimer le formulaire';
        removeFormButton.classList = 'btn btn-danger mb-3'

        item.append(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

    // Supprimer le conteneur du prix associé au bouton de suppression
    function removePriceContainer(e) {
        e.preventDefault();
        const container = e.currentTarget.closest('.existing-price');
        if (container) {
            container.remove(); // Supprimer le conteneur entier
        }
    }
}