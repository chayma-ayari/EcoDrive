<?php
require_once '../../model/config.php';


// Connexion à la base
$pdo = config::getConnexion();

// Récupérer les types de transport disponibles (dispo=1 et etat=1)
$stmt = $pdo->query("SELECT DISTINCT type_t FROM transport WHERE dispo = 1 AND etat = 1");
$types = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Réservation</title>
    <link rel="stylesheet" href="style.css"> <!-- ton style ici si nécessaire -->
</head>
<body>
    <div class="form-container">
        <h2>Formulaire de Réservation</h2>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">Réservation ajoutée avec succès !</p>
        <?php endif; ?>

        <form action="../../reservation/ajouterReservation.php" method="POST">
            <label for="date_t">Date :</label>
            <input type="date" name="date_t" id="date_t" required><br><br>

            <label for="lieu_depart">Lieu de départ :</label>
            <select name="lieu_depart" id="lieu_depart" required>
                <option value="">-- Choisir --</option>
                <option value="tunis">Tunis</option>
                <option value="bardou">Bardou</option>
                <option value="marsa">Marsa</option>
                <option value="ghazela">Ghazela</option>
            </select><br><br>

            <label for="lieu_arrive">Lieu d’arrivée :</label>
            <select name="lieu_arrive" id="lieu_arrive" required>
                <option value="">-- Choisir --</option>
                <option value="tunis">Tunis</option>
                <option value="bardou">Bardou</option>
                <option value="marsa">Marsa</option>
                <option value="ghazela">Ghazela</option>
            </select><br><br>

            <label for="type_t">Moyenne de transport :</label>
            <select name="type_t" id="type_t" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="submit" value="Réserver">
        </form>
    </div>
</body>
</html>
