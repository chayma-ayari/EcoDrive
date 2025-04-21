<?php
require_once '../../model/config.php';
require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';

$pdo = config::getConnexion();

$id_r = $_GET['id_r'] ?? null;
$success = false;
$errorMessage = null;

if (!$id_r) {
    die("ID de réservation manquant.");
}

$stmt = $pdo->prepare("SELECT r.*, t.type_t FROM reservation r JOIN transport t ON r.id_t = t.matricule WHERE r.id_r = ?");
$stmt->execute([$id_r]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Réservation introuvable.");
}

$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE id_t = ? AND id_r != ?");
$stmt2->execute([$reservation['id_t'], $id_r]);
$isUsedElsewhere = $stmt2->fetchColumn() > 0;

$stmtTypes = $pdo->query("SELECT DISTINCT type_t FROM transport WHERE dispo = 1 AND etat = 1");
$types = $stmtTypes->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$isUsedElsewhere) {
    try {
        $date_t = $_POST['date_t'];
        $lieu_depart = $_POST['lieu_depart'];
        $lieu_arrive = $_POST['lieu_arrive'];
        $type_t = $_POST['type_t'];

        $stmt3 = $pdo->prepare("SELECT matricule FROM transport WHERE type_t = :type_t AND dispo = 1 AND etat = 1");
        $stmt3->execute(['type_t' => $type_t]);
        $transports = $stmt3->fetchAll(PDO::FETCH_COLUMN);

        $id_t_dispo = null;
        foreach ($transports as $matricule) {
            $stmt4 = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE id_t = :id_t AND id_r != :id_r");
            $stmt4->execute(['id_t' => $matricule, 'id_r' => $id_r]);
            if ($stmt4->fetchColumn() == 0) {
                $id_t_dispo = $matricule;
                break;
            }
        }

        if ($id_t_dispo) {
            $stmt5 = $pdo->prepare("UPDATE reservation SET date_t = :date_t, lieu_depart = :lieu_depart, lieu_arrive = :lieu_arrive, id_t = :id_t WHERE id_r = :id_r");
            $stmt5->execute([
                'date_t' => $date_t,
                'lieu_depart' => $lieu_depart,
                'lieu_arrive' => $lieu_arrive,
                'id_t' => $id_t_dispo,
                'id_r' => $id_r
            ]);
            $success = true;
        } else {
            $errorMessage = "Aucun transport disponible pour ce type.";
        }

    } catch (PDOException $e) {
        $errorMessage = "Erreur SQL : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Réservation</title>
    <link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/fontawesome.css" rel="stylesheet">
    <link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/templatemo-finance-business.css" rel="stylesheet">
    <link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/owl.css" rel="stylesheet">
    <link href="http://localhost/projetWEB2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .form-container {
            background-color: #ffffff;
            margin-top: 60px;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-title {
            font-size: 2.8rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin-bottom: 40px;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
            font-size: 1.1rem;
        }

        .form-control {
            height: 50px;
            font-size: 1rem;
            border-radius: 15px;
            padding-left: 20px;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            padding: 12px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
        }

        .btn-secondary {
            background-color: #000;
            border-color: #000;
            padding: 12px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
        }

        .navbar {
            background-color: #003366;
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff;
        }

        .navbar-light .navbar-nav .nav-link {
            color: #ffffff;
            font-size: 1.1rem;
        }

        .navbar-nav .nav-item .nav-link:hover {
            color: #ffcb00;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<!-- ... (tout le code précédent reste identique jusqu’à la navbar) -->

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #28a745;">
    <div class="container">
        <a href="#" class="navbar-brand text-white">Eco<span>Drive</span></a>
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
            <div class="navbar-nav">
                <a href="../../index.html" class="nav-item nav-link text-white">Accueil</a>
                <a href="#" class="nav-item nav-link text-white">Services</a>
                <a href="#" class="nav-item nav-link text-white">Contact</a>
                <a href="#" class="nav-item nav-link text-white">Réclamation</a>
            </div>
        </div>
    </div>
</nav>

<!-- ... (tout le formulaire reste identique) -->

<!-- Footer Réseaux sociaux -->
<footer style="background-color: #28a745; padding: 30px 0; margin-top: 50px;">
    <div class="container text-center text-white">
        <h5>Suivez-nous</h5>
        <div style="font-size: 1.5rem;">
            <a href="#" class="text-white mx-3"><i class="fa fa-facebook"></i></a>
            <a href="#" class="text-white mx-3"><i class="fa fa-twitter"></i></a>
            <a href="#" class="text-white mx-3"><i class="fa fa-instagram"></i></a>
        </div>
    </div>
</footer>

<!-- Footer copy -->
<div class="container-fluid bg-dark text-white py-3">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 EcoDrive. Tous droits réservés.</p>
    </div>
</div>


<!-- Formulaire -->
<div class="form-container">
    <h2 class="form-title">Modifier la Réservation</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">Réservation modifiée avec succès !</div>
    <?php elseif (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
    <?php elseif ($isUsedElsewhere): ?>
        <div class="alert alert-warning text-center">Impossible de modifier : ce transport est déjà utilisé.</div>
    <?php endif; ?>

    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Date de transport</label>
                <input type="date" name="date_t" class="form-control" value="<?= htmlspecialchars($reservation['date_t']) ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label>Type de transport</label>
                <select name="type_t" class="form-control" required>
                    <option value="">-- Choisir un type --</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $type == $reservation['type_t'] ? "selected" : "" ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Lieu de départ</label>
                <select name="lieu_depart" class="form-control" required>
                    <?php foreach (['tunis', 'bardou', 'marsa', 'ghazela'] as $lieu): ?>
                        <option <?= $reservation['lieu_depart'] == $lieu ? "selected" : "" ?>><?= $lieu ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Lieu d'arrivée</label>
                <select name="lieu_arrive" class="form-control" required>
                    <?php foreach (['tunis', 'bardou', 'marsa', 'ghazela'] as $lieu): ?>
                        <option <?= $reservation['lieu_arrive'] == $lieu ? "selected" : "" ?>><?= $lieu ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="text-center mt-4">
            <?php if (!$isUsedElsewhere): ?>
                <button type="submit" class="btn btn-primary">Modifier</button>
            <?php endif; ?>
            <a href="afficherReservation.php" class="btn btn-secondary ml-3">Annuler</a>
        </div>
    </form>
</div>

<!-- Footer -->
<div class="container-fluid bg-dark text-white mt-5 py-4">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 EcoDrive. Tous droits réservés.</p>
    </div>
</div>

<script src="../../assets/frontOffice/js/jquery.min.js"></script>
<script src="../../assets/frontOffice/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/frontOffice/js/main.js"></script>
</body>
</html>
