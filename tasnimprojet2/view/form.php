<?php
session_start();
require_once '../config.php';
require_once '../model/livraisons.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $db = config::getConnexion();

    // Create new Livraison object with form data
    $livraison = new Livraison(
      $_POST['id_liv'],
      $_POST['colis'],
      $_POST['date_liv'],
      $_POST['lieu'],
      $_POST['tel'],
      $_POST['moy_transport']
    );

    // Create the delivery using CRUD operation
    if ($livraison->create($db)) {
      $message = "Delivery added successfully!";
    } else {
      $error = "Error adding delivery";
    }
  } catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Delivery Form</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <style>
    body {
      background-color: #e8f5e9;
      /* Light green background */
    }

    .container {
      background-color: #4caf50;
      /* Green container */
      padding: 20px;
      border-radius: 10px;
      color: white;
      max-width: 500px;
    }

    .btn-success {
      background-color: #2e7d32;
      /* Darker green for button */
      border: none;
    }

    .btn-success:hover {
      background-color: #1b5e20;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Delivery Form</h2>
    <?php if (isset($message)): ?>
      <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="form-group">
        <label for="id_liv">Delivery ID</label>
        <input type="text" class="form-control" id="id_liv" name="id_liv" required>
      </div>

      <div class="form-group">
        <label for="colis">Package Details</label>
        <input type="text" class="form-control" id="colis" name="colis" required>
      </div>

      <div class="form-group">
        <label for="date_liv">Delivery Date</label>
        <input type="date" class="form-control" id="date_liv" name="date_liv" required>
      </div>

      <div class="form-group">
        <label for="lieu">Delivery Location</label>
        <input type="text" class="form-control" id="lieu" name="lieu" required>
      </div>

      <div class="form-group">
        <label for="tel">Phone Number</label>
        <input type="tel" class="form-control" id="tel" name="tel" required>
      </div>

      <div class="form-group">
        <label for="moy_transport">Transportation Method</label>
        <input type="text" class="form-control" id="moy_transport" name="moy_transport" required>
      </div>

      <button type="submit" class="btn btn-success btn-block">Submit Delivery</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>