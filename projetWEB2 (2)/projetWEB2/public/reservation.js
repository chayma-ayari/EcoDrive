document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reservation-form');
    const dateInput = document.getElementById('date_t');
    const typeTransport = document.querySelector('select[name="type_t"]');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validation JavaScript côté client
        const lieuDepart = form.querySelector('select[name="lieu_depart"]').value;
        const lieuArrive = form.querySelector('select[name="lieu_arrive"]').value;
        const dateValue = dateInput.value;
        const typeValue = typeTransport.value;

        const today = new Date().toISOString().split('T')[0];
        if (!dateValue || !lieuDepart || !lieuArrive || !typeValue) {
            alert("Veuillez remplir tous les champs !");
            return;
        }

        if (dateValue < today) {
            alert("La date ne peut pas être antérieure à aujourd'hui !");
            return;
        }

        // Vérification côté serveur via AJAX
        try {
            const response = await fetch('../controller/verifier_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `date_t=${encodeURIComponent(dateValue)}&type_t=${encodeURIComponent(typeValue)}`
            });

            const result = await response.text();
            if (result.trim() === 'reserved') {
                alert("Ce moyen de transport est déjà réservé pour cette date !");
            } else {
                form.submit(); // Si tout est bon, on soumet le formulaire
            }
        } catch (error) {
            alert("Erreur de communication avec le serveur.");
            console.error(error);
        }
    });
});
