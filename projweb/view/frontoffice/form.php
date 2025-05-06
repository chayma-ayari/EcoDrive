<?php
require_once '../../config.php';
require_once '../../model/livraisons.php';
require_once '../../model/colis.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $db = config::getConnexion();

    if (isset($_POST['form_type']) && $_POST['form_type'] === 'delivery') {
      // Validate inputs
      $date_liv = $_POST['date_liv'] ?? '';
      $lieu = $_POST['lieu'] ?? '';
      $tel = $_POST['tel'] ?? '';
      $moy_transport = $_POST['moy_transport'] ?? '';

      if (empty($date_liv) || empty($lieu) || empty($tel) || empty($moy_transport)) {
        $error = "All delivery fields are required.";
      } elseif (!preg_match('/^\d{8}$/', $tel)) {
        $error = "Phone number must be exactly 8 digits.";
      } elseif (!strtotime($date_liv)) {
        $error = "Invalid delivery date format.";
      } else {
        // Check if colis_id is provided
        if (!empty($_POST['colis_id'])) {
          $colisId = $_POST['colis_id'];

          // Create new Livraison object with provided colis_id
          $livraison = new Livraison(
            $date_liv,
            $lieu,
            $tel,
            $moy_transport,
            $colisId
          );

          if ($livraison->create($db)) {
            $message = "Delivery added successfully with existing Colis ID!";
            // QR_code
            header("Location: qr_code.php?id=" . $db->lastInsertId());
            exit();
          } else {
            $error = "Error adding delivery";
          }
        } elseif (isset($_POST['poids'], $_POST['contenu'], $_POST['statut'], $_POST['date_envoi'], $_POST['date_livraison'])) {
          // Create new Colis object
          $colis = new Colis(
            null, // id_colis will be auto-generated or null
            $_POST['poids'],
            $_POST['contenu'],
            $_POST['statut'],
            $_POST['date_envoi'],
            $_POST['date_livraison']
          );

          if ($colis->create($db)) {
            // Get the last inserted id_colis
            $colisId = $db->lastInsertId();

            // Create new Livraison object with colis_id
            $livraison = new Livraison(
              $date_liv,
              $lieu,
              $tel,
              $moy_transport,
              $colisId
            );

            if ($livraison->create($db)) {
              $message = "Delivery and Colis added successfully!";
              // QR_code
              header("Location: qr_code.php?id=" . $db->lastInsertId());
              exit();
            } else {
              $error = "Error adding delivery";
            }
          } else {
            $error = "Error adding colis";
          }
        } else {
          $error = "Colis data is required for delivery";
        }
      }
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'colis') {
      // Create new Colis object with colis form data
      $colis = new Colis(
        $_POST['id_colis'],
        $_POST['poids'],
        $_POST['contenu'],
        $_POST['statut'],
        $_POST['date_envoi'],
        $_POST['date_livraison']
      );

      // Create the record using CRUD operation
      if ($colis->create($db)) {
        $message = "Colis added successfully!";
      } else {
        $error = "Error adding colis";
      }
    }
  } catch (Exception $e) {
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'delivery') {
      $error = "Error: " . $e->getMessage();
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'colis') {
      $error = "Error: " . $e->getMessage();
    } else {
      $error = "Error: " . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Delivery or Colis Form</title>
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
  <script>
    function toggleForm() {
      var formType = document.querySelector('input[name="form_type_choice"]:checked').value;
      if (formType === 'delivery') {
        document.getElementById('deliveryForm').style.display = 'block';
        document.getElementById('colisForm').style.display = 'none';
      } else {
        document.getElementById('deliveryForm').style.display = 'none';
        document.getElementById('colisForm').style.display = 'block';
      }
    }
    window.onload = function() {
      toggleForm();
    };

    // Show alert on successful delivery submission
    <?php if ($message === "Delivery and Colis added successfully!" || $message === "Delivery added successfully!"): ?>
      alert("Delivery added successfully!");
    <?php endif; ?>

    // Show alert on successful colis submission
    <?php if ($message === "Delivery and Colis added successfully!" || $message === "Colis added successfully!"): ?>
      alert("Colis added successfully!");
    <?php endif; ?>
  </script>
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Choose Form Type</h2>
    <div class="form-group text-center">
      <label>
        <input type="radio" name="form_type_choice" value="delivery" onchange="toggleForm()" checked>
        Delivery Form
      </label>
      &nbsp;&nbsp;&nbsp;
      <label>
        <input type="radio" name="form_type_choice" value="colis" onchange="toggleForm()">
        Colis Form
      </label>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" id="deliveryForm" style="display: block;">
      <input type="hidden" name="form_type" value="delivery" />

      <div class="form-group">
        <label for="id_livraison">Delivery ID</label>
        <input type="text" class="form-control" id="id_livraison" name="id_livraison" required autocomplete="off" />
      </div>

      <fieldset class="border p-2">
        <legend class="w-auto px-2">Package Details</legend>
        <div class="form-group">
          <label for="date_liv">Delivery Date</label>
          <input type="date" class="form-control form-control-sm" id="date_liv" name="date_liv" required autocomplete="off" />
        </div>

        <div class="form-group">
          <label for="lieu">Delivery Location</label>
          <input type="text" class="form-control form-control-sm" id="lieu" name="lieu" required autocomplete="off" />
        </div>

        <div class="form-group">
          <label for="tel">Phone Number</label>
          <input type="tel" class="form-control form-control-sm" id="tel" name="tel" required pattern="[0-9]{8}" title="Enter a valid phone number with exactly 8 digits" autocomplete="off" />
        </div>

        <div class="form-group">
          <label for="moy_transport">Transportation Method</label>
          <input type="text" class="form-control form-control-sm" id="moy_transport" name="moy_transport" required autocomplete="off" />
        </div>

        <div class="form-group">
          <label for="colis_id">Colis ID</label>
          <input type="text" class="form-control form-control-sm" id="colis_id" name="colis_id" required autocomplete="off" />
        </div>
      </fieldset>

      <button type="submit" class="btn btn-success btn-block">Submit Delivery</button>
    </form>

    <form method="POST" action="" id="colisForm" style="display: none;">
      <input type="hidden" name="form_type" value="colis" />

      <div class="form-group">
        <label for="id_colis">Colis ID</label>
        <input type="text" class="form-control" id="id_colis" name="id_colis" required autocomplete="off" />
      </div>

      <div class="form-group">
        <label for="poids">Weight (kg)</label>
        <input type="number" step="0.01" class="form-control" id="poids" name="poids" required min="0" autocomplete="off" />
      </div>

      <div class="form-group">
        <label for="contenu">Content Description</label>
        <input type="text" class="form-control" id="contenu" name="contenu" required autocomplete="off" />
      </div>

      <div class="form-group">
        <label for="statut">Status</label>
        <input type="text" class="form-control" id="statut" name="statut" required autocomplete="off" />
      </div>

      <div class="form-group">
        <label for="date_envoi">Send Date</label>
        <input type="date" class="form-control" id="date_envoi" name="date_envoi" required />
      </div>

      <div class="form-group">
        <label for="date_livraison">Delivery Date</label>
        <input type="date" class="form-control" id="date_livraison" name="date_livraison" required />
      </div>

      <button type="submit" class="btn btn-success btn-block">Submit Colis</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>