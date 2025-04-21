function validateForm() {
    let form = document.forms[0];

    let matricule = form["matricule"];
    let brand = form["brand"];
    let type = form["type_t"];
    let dispo = form["dispo"];
    let etat = form["etat"];

    // Matricule : doit être un entier positif
    if (!matricule.value || isNaN(matricule.value) || parseInt(matricule.value) <= 0) {
        alert("Veuillez entrer un matricule valide (nombre positif).");
        matricule.focus();
        return false;
    }

    // Brand : au moins 2 caractères, lettres uniquement
    if (!brand.value || brand.value.trim().length < 2) {
        alert("La marque doit contenir au moins 2 caractères.");
        brand.focus();
        return false;
    }

    let brandRegex = /^[a-zA-Z\s\-]+$/;
    if (!brandRegex.test(brand.value.trim())) {
        alert("La marque ne doit contenir que des lettres, espaces ou tirets.");
        brand.focus();
        return false;
    }

    // Type : doit être une valeur parmi car, scooter, bike
    let validTypes = ["car", "scooter", "bike"];
    if (!validTypes.includes(type.value)) {
        alert("Veuillez sélectionner un type de transport valide.");
        type.focus();
        return false;
    }

    // Dispo : doit être 0 ou 1
    if (dispo.value !== "0" && dispo.value !== "1") {
        alert("Veuillez sélectionner la disponibilité.");
        dispo.focus();
        return false;
    }

    // État : doit être 0 ou 1
    if (etat.value !== "0" && etat.value !== "1") {
        alert("Veuillez sélectionner l’état.");
        etat.focus();
        return false;
    }

    // Si tout est OK
    return true;
}
