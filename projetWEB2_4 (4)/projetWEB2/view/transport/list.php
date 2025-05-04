<?php
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';


$controller = new TransportController();
$liste = $controller->getAllTransports();



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
            <a href="edit.php?id=<?= $t->getId(); ?>">Modifier</a>
<a href="delete.php?id=<?= $t->getId(); ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>

            </td>
        </tr>
    <?php endforeach; ?>
</table> 









