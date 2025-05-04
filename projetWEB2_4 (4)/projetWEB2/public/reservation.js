document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reservation-form');
    const dateInput = document.getElementById('date_t');
    const typeTransport = document.querySelector('select[name="type_t"]');
    const errorBox = document.getElementById('error-box');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBox.innerHTML = ''; // Reset error display

        const lieuDepart = form.querySelector('select[name="lieu_depart"]').value;
        const lieuArrive = form.querySelector('select[name="lieu_arrive"]').value;
        const dateValue = dateInput.value;
        const typeValue = typeTransport.value;

        const today = new Date().toISOString().split('T')[0];

        let errors = [];

        // Vérifier les champs vides
        if (!dateValue || !lieuDepart || !lieuArrive || !typeValue) {
            alert("❌ Veuillez remplir tous les champs.");
            return;
        }

        // Vérifier si la date est antérieure à aujourd'hui
        if (dateValue < today) {
            errors.push("❌ La date ne peut pas être antérieure à aujourd'hui.");
        }

        // Vérification transport côté serveur SEULEMENT si date remplie
        if (dateValue && typeValue) {
            try {
                const response = await fetch('../controller/verifier_reservation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `date_t=${encodeURIComponent(dateValue)}&type_t=${encodeURIComponent(typeValue)}`
                });

                const result = await response.text();
                if (result.trim() === 'reserved') {
                    errors.push("❌ Ce moyen de transport est déjà réservé pour cette date.");
                }
            } catch (error) {
                errors.push("❌ Erreur de communication avec le serveur.");
                console.error(error);
            }
        }

        // Afficher toutes les erreurs en même temps
        if (errors.length > 0) {
            errorBox.innerHTML = errors.map(e => `<p class="error">${e}</p>`).join('');
            return;
        }

        form.submit(); // Tout est bon, on soumet
    });
});
