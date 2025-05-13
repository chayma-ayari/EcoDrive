<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/History.php';

// Vérifier si l'utilisateur est connecté (admin or user)
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$historyModel = new History($pdo);

// Pagination parameters
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$totalCount = $historyModel->getLoginHistoryCount();
$totalPages = ceil($totalCount / $limit);

// Fetch paginated login history
$loginHistory = $historyModel->getLoginHistoryPaginated($limit, $offset);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des connexions</title>
    <link href="/user/view/back/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="/user/view/back/assets/css/custom.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2><i class="fa fa-history"></i> Historique des connexions</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Heure de connexion</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($loginHistory) > 0): ?>
                    <?php foreach ($loginHistory as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['id']) ?></td>
                        <td><?= htmlspecialchars($entry['email'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($entry['login_time']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">Aucune donnée disponible.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Précédent">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Suivant">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <a href="user_dashboard.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour au tableau de bord</a>
    </div>
</body>
</html>
