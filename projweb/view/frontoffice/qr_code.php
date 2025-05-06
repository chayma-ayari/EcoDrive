<?php
if (!extension_loaded('gd')) {
    echo "<pre>";
    echo "PHP GD library is not enabled.\n\n";
    echo "Current PHP Version: " . phpversion() . "\n";
    echo "Loaded Extensions:\n";
    print_r(get_loaded_extensions());
    echo "\n\nPlease check your php.ini file at: " . php_ini_loaded_file();
    echo "</pre>";
    exit;
}

require_once '../../config.php';
require_once '../../model/livraisons.php';
require_once '../../phpqrcode/qrlib.php';

if (!isset($_GET['id'])) {
    header('Location: form.php');
    exit();
}

$id = $_GET['id'];

$projectRoot = $_SERVER['DOCUMENT_ROOT'] . '/tasnimprojet2/';
$tempDir = $projectRoot . 'uploads/qrcodes/';
$webPath = '/tasnimprojet2/uploads/qrcodes/';

if (!file_exists($tempDir)) {
    if (!@mkdir($tempDir, 0777, true)) {
        die('Failed to create QR code directory');
    }
}

$files = glob($tempDir . "*.png");
if (count($files) > 100) {
    array_map('unlink', array_slice($files, 0, count($files) - 100));
}

$filename = 'qr_' . $id . '_' . uniqid() . '.png';
$filePath = $tempDir . $filename;

$deliveryUrl = "http://" . $_SERVER['HTTP_HOST'] . "/tasnimprojet2/tracking.php?id=" . $id;

try {
    QRcode::png($deliveryUrl, $filePath, QR_ECLEVEL_L, 10);

    if (!file_exists($filePath)) {
        throw new Exception('QR code file was not created');
    }
} catch (Exception $e) {
    die('Error generating QR code: ' . $e->getMessage());
}

$qrCodeUrl = $webPath . $filename;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .qr-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .qr-image {
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="qr-container text-center">
            <h2 class="mb-4">Your Delivery QR Code</h2>
            <div class="qr-image">
                <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code">
            </div>
            <p class="mt-3 text-muted">Scan this QR code to track your delivery</p>
            <p class="font-weight-bold">Delivery ID: <?php echo htmlspecialchars($id); ?></p>
            <div class="mt-4">
                <a href="form.php" class="btn btn-primary">Back to Form</a>
                <a href="../../tracking.php?id=<?php echo htmlspecialchars($id); ?>"
                    class="btn btn-success ml-2">Track Delivery</a>
            </div>
        </div>
    </div>
</body>

</html>