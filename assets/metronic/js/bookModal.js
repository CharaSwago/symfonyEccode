document.addEventListener('DOMContentLoaded', () => {
    const openModalButton = document.getElementById('openModal');
    const modal = document.getElementById('book_modal');
    const closeModalButton = modal.querySelector('.modal-close');
    const form = document.getElementById('updateDescriptionForm');
    const bookSelect = document.getElementById('book');
    const finishedCheckbox = document.getElementById('finished');
    const descriptionField = document.getElementById('description');

    // Ouvrir le modal lorsque le bouton est cliqué
    if (openModalButton) {
        openModalButton.addEventListener('click', (event) => {
            event.preventDefault();
            modal.style.display = 'block'; // Ouvrir le modal
            loadBooks(); // Charger les livres via AJAX
        });
    }

    // Fermer le modal lorsque l'utilisateur clique sur la croix
    if (closeModalButton) {
        closeModalButton.addEventListener('click', () => {
            modal.style.display = 'none'; // Fermer le modal
        });
    }

    // Charger les livres dans le modal via AJAX et remplir le select
    function loadBooks() {
        fetch('/book/modal-data') // Remplacez par l'URL adéquate qui retourne les livres
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.books) {
                    bookSelect.innerHTML = '<option value="" disabled selected>Choisissez un livre</option>'; // Réinitialiser
                    data.books.forEach(book => {
                        const option = document.createElement('option');
                        option.value = book.id;
                        option.textContent = book.name;
                        bookSelect.appendChild(option);
                    });
                } else {
                    console.error('Erreur lors du chargement des livres');
                    alert('Erreur lors du chargement des livres.');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Erreur lors de la communication avec le serveur.');
            });
    }

    // Lorsqu'un livre est sélectionné dans le modal, charger les détails
    if (bookSelect) {
        bookSelect.addEventListener('change', (event) => {
            const bookId = event.target.value;

            // Charger les détails du livre sélectionné (description et statut terminé)
            fetch(`/book/details/${bookId}`) // Remplacez par l'URL adéquate
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.book) {
                        descriptionField.value = data.book.description || '';
                        finishedCheckbox.checked = data.book.finished || false;
                    } else {
                        console.error('Erreur lors du chargement des détails du livre');
                    }
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    alert('Erreur lors du chargement des détails du livre.');
                });
        });
    }

    // Soumettre le formulaire de mise à jour
    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const bookId = bookSelect.value;
            const description = descriptionField.value;
            const finished = finishedCheckbox.checked;
            const submitButton = form.querySelector('button[type="submit"]');

            if (!bookId || !description) {
                alert('Veuillez sélectionner un livre et entrer une description.');
                return;
            }

            // Désactivation du bouton de soumission pour éviter les soumissions multiples
            submitButton.disabled = true;

            // Envoi des données via AJAX
            fetch('/book/update-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ book_id: bookId, description: description, finished: finished })
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false; // Réactiver le bouton
                if (data.status === 'success') {
                    alert(data.message || 'Description mise à jour avec succès.');
                    modal.style.display = 'none'; // Fermer le modal
                } else {
                    alert(data.message || 'Erreur lors de la mise à jour.');
                }
            })
            .catch(error => {
                submitButton.disabled = false; // Réactiver le bouton
                console.error('Erreur :', error);
                alert('Erreur lors de la mise à jour de la description.');
            });
        });
    }
});
