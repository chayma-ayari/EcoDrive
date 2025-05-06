<?php
require_once '../../config.php';
require_once '../../model/colis.php';

$message = '';
$error = '';
$qrDataUri = '';

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php'; // For QR code generation
    // Import classes after including autoload
    class_alias('Endroid\QrCode\Builder\Builder', 'Builder');
    class_alias('Endroid\QrCode\Writer\PngWriter', 'PngWriter');
} else {
    // Fallback or error handling if autoload.php is not found
    $message = "";
}

require_once __DIR__ . '/../../vendor/autoload.php'; // Add Twilio SDK require_once

use Twilio\Rest\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $db = config::getConnexion();

    // Validate colis form inputs
    $id_colis = $_POST['id_colis'] ?? '';
    $poids = $_POST['poids'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $statut = $_POST['statut'] ?? '';
    $date_envoi = $_POST['date_envoi'] ?? '';
    $date_livraison = $_POST['date_livraison'] ?? '';

    if (empty($id_colis) || empty($poids) || empty($contenu) || empty($statut) || empty($date_envoi) || empty($date_livraison)) {
      $error = "All colis fields are required.";
    } elseif (!is_numeric($poids) || $poids <= 0) {
      $error = "Weight must be a positive number.";
    } elseif (!strtotime($date_envoi) || !strtotime($date_livraison)) {
      $error = "Invalid date format for send date or delivery date.";
    } else {
      // Create new Colis object with colis form data
      $colis = new Colis(
        $id_colis,
        $poids,
        $contenu,
        $statut,
        $date_envoi,
        $date_livraison
      );

      // Create the record using CRUD operation
      if ($colis->create($db)) {
        $message = "Colis added successfully!";
        $data = "Type: colis, ID: $id_colis";
        $qrResult = Builder::create()
          ->writer(new PngWriter())
          ->data($data)
          ->size(300)
          ->margin(10)
          ->build();
        $qrDataUri = $qrResult->getDataUri();

        // Save QR code image to file
        $qrImagePath = __DIR__ . '/QR_code.png';
        $qrResult->saveToFile($qrImagePath);

        // Send email notification with colis attributes
        $to = "tasnimouertani516@gmail.com";
        $subject = "New Colis Submission";
        $email_message = "A new colis has been submitted with the following details:\n\n";
        $email_message .= "Colis ID: $id_colis\n";
        $email_message .= "Weight: $poids\n";
        $email_message .= "Content: $contenu\n";
        $email_message .= "Status: $statut\n";
        $email_message .= "Send Date: $date_envoi\n";
        $email_message .= "Delivery Date: $date_livraison\n";

        $headers = "From: no-reply@yourdomain.com\r\n";
        $headers .= "Reply-To: no-reply@yourdomain.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $email_message, $headers)) {
          $message .= " Email notification sent.";
        } else {
          $error .= " Failed to send email notification.";
          error_log("Mail sending failed to $to with subject $subject");
        }

        // Fetch livraison info for the colis
        $query = "SELECT lieu, tel FROM livraisons WHERE id_liv = :id_liv";
        $stmt = $db->prepare($query);
        $stmt->execute(['id_liv' => $id_colis]);
        $livraison = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($livraison) {
          $lieu = $livraison['lieu'];
          $tel = $livraison['tel'];

          // Send SMS with Twilio
          $sid = "AC6ea813a87fe8070ea4b2a663ab6907d8";
          $token = "07a34e01aaf7a5ba51631bd5901753d2";
          $twilio = new Client($sid, $token);

          try {
            $message_sms = "Your colis coordinates: " . $lieu;
            // Debug info for phone number and message
            $message .= " Sending SMS to: $tel with message: $message_sms";
            $twilio->messages->create(
              $tel,
              [
                'from' => '+1234567890', // Replace with your Twilio number
                'body' => $message_sms
              ]
            );
            $message .= " SMS sent with coordinates.";
          } catch (Exception $e) {
            $error .= " SMS sending failed: " . $e->getMessage();
          }
        } else {
          $error .= " Livraison info not found for this colis.";
        }
      } else {
        $error = "Error adding colis";
      }
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
  <title>Colis Form</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <style>
    body {
      background-color: #e8f5e9;
    }

    .container {
      background-color: #4caf50;
      padding: 20px;
      border-radius: 10px;
      color: white;
      max-width: 500px;
      margin-top: 50px;
    }

    .btn-success {
      background-color: #2e7d32;
      border: none;
    }

    .btn-success:hover {
      background-color: #1b5e20;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2 class="text-center">Colis Form</h2>

    <?php if ($message): ?>
      <div class="alert alert-success text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
        <?php echo $message; ?>
      </div>
      <?php if ($qrDataUri): ?>
        <div class="text-center mt-3">
          <img src="<?php echo $qrDataUri; ?>" alt="QR Code" />
        </div>
      <?php endif; ?>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger text-center font-weight-bold" role="alert" style="font-size: 1.2em;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
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
