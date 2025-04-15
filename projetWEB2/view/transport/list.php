<?php
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';


$controller = new TransportController();
$liste = $controller->getAllTransports();

foreach ($liste as $transport) {
    echo "<tr>";
    echo "<td>" . $transport->getMatricule() . "</td>";
    echo "<td>" . $transport->getBrand() . "</td>";
    echo "<td>" . $transport->getType_t() . "</td>";
    echo "<td>" . ($transport->getDispo() ? "Oui" : "Non") . "</td>";
    echo "<td>" . ($transport->getEtat() ? "Bon" : "Mauvais") . "</td>";


    echo "</tr>";
}

    $controller = new TransportController();
$transports = $controller->getAllTransports();
?>

<table border="1">
    <tr>
        <th>Matricule</th>
        <th>Brand</th>
        <th>Type</th>
        <th>Disponible</th>
        <th>État</th>
        <th>Actions</th>
    </tr>
   
    <?php foreach ($transports as $t): ?>
        <tr>
            <td><?= $t->getMatricule(); ?></td>
            <td><?= $t->getBrand(); ?></td>
            <td><?= $t->getType_t(); ?></td>
            <td><?= $t->getDispo(); ?></td>
            <td><?= $t->getEtat(); ?></td>
            <td>
                <a href="../../edit.php?matricule=<?= $t->getMatricule(); ?>">Edit</a> |
                <a href="delete.php?matricule=<?= $t->getMatricule(); ?>" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                
            </td>
        </tr>
    <?php endforeach; ?>
</table> 









