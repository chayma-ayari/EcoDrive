<?php
// Vérifie si le formulaire a été soumis
unset($_SESSION['delivery']);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delivery_id = htmlspecialchars($_POST["delivery_id"]);
    $package_details = htmlspecialchars($_POST["package_details"]);
    $delivery_date = htmlspecialchars($_POST["delivery_date"]);
    $delivery_location = htmlspecialchars($_POST["delivery_location"]);
    $phone_number = htmlspecialchars($_POST["phone_number"]);
    $transportation_method = htmlspecialchars($_POST["transportation_method"]);
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Delivery Details</title>
        <style>
            table {
                width: 50%;
                margin: 50px auto;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #4CAF50;
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #4CAF50;
                color: white;
            }
            caption {
                font-size: 24px;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <table>
            <caption>Delivery Information</caption>
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Delivery ID</td><td><?= $delivery_id ?></td></tr>
            <tr><td>Package Details</td><td><?= $package_details ?></td></tr>
            <tr><td>Delivery Date</td><td><?= $delivery_date ?></td></tr>
            <tr><td>Delivery Location</td><td><?= $delivery_location ?></td></tr>
            <tr><td>Phone Number</td><td><?= $phone_number ?></td></tr>
            <tr><td>Transportation Method</td><td><?= $transportation_method ?></td></tr>
        </table>
    </body>
    </html>

<?php
} else {
    echo "Aucune donnée reçue.";
}
unset($_SESSION['delivery']);
?>