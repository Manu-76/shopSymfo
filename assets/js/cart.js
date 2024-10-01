document.addEventListener("DOMContentLoaded", function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const priceId = this.getAttribute('data-price-id');
            const size = document.querySelector(`.size-select[data-id='${priceId}']`).value;
            const quantity = document.querySelector(`.quantity-select[data-id='${priceId}']`).value;

            if (size === 'Sélectionner une taille' || quantity === 'Sélectionner une quantité') {
                alert('Veuillez sélectionner une taille et une quantité.');
                return;
            }

            const data = {
                size: size,
                quantity: quantity
            };

            fetch(`/panier/${priceId}/ajout-d-un-produit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Produit ajouté au panier !');
                } else {
                    alert('Erreur : ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'ajout au panier :', error);
            });
        });
    });
});
