<?php
require_once '../../vendor/autoload.php'; // Assuming you have installed a QR code library via Composer

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$data = '';
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];
    $data = "Type: $type, ID: $id";
} else {
    $data = "No data provided";
}

$qrResult = Builder::create()
    ->writer(new PngWriter())
    ->data($data)
    ->size(300)
    ->margin(10)
    ->build();

$qrDataUri = $qrResult->getDataUri();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>QR Code</title>
  <style>
    body {
      background-color: #4caf50; /* Green background */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      color: white;
    }
    h1 {
      margin-bottom: 40px;
    }
    img {
      border: 5px solid white;
      border-radius: 10px;
      max-width: 300px;
      max-height: 300px;
    }
  </style>
</head>
<body>
  <h1>Your QR Code</h1>
  <img src="<?php echo $qrDataUri; ?>" alt="QR Code" />
  <p><?php echo htmlspecialchars($data); ?></p>
</body>
</html>
