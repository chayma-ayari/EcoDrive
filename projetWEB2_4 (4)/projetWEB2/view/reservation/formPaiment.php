<?php
session_start();
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
require_once '../../vendor/autoload.php';  // Stripe SDK

require_once '../../model/config.php';
require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';
require_once '../../model/Transport.php';

// Make sure the id_r is passed correctly from the URL
if (isset($_GET['id_r']) && is_numeric($_GET['id_r'])) {
    $id_r = $_GET['id_r'];
} else {
    $id_r = 0;  // Default to 0 if not passed or invalid
}

// Debugging: Check the current URL and the value of id_r
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";  // Prints the full URL
echo "<p>id_r value: " . htmlspecialchars($id_r) . "</p>";  // Debug the value of id_r

if ($id_r == 0) {
    echo "<p>❌ The reservation ID (id_r) is missing or invalid. Please try again.</p>";
    exit;  // Exit if id_r is invalid
}

\Stripe\Stripe::setApiKey('sk_test_51RJJRvP5G4eRbLXromY7OPbct4wpVYRkvBgtpK84NujTwXqBoyABG1yPxajT3waHrxuhxRbCqCYmETXWwtmEbc1800H9GQrXpc'); // Your secret key

$pdo = config::getConnexion();
$success = false;
$errorMessages = [];
$paymentStatus = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Check if the reservation exists
        $stmt = $pdo->prepare("SELECT * FROM reservation WHERE id_r = :id_r");
        $stmt->execute(['id_r' => $id_r]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            throw new Exception('❌ La réservation n\'existe pas.');
        }

        // Create the Stripe Checkout session
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Paiement réservation #' . $id_r,
                    ],
                    'unit_amount' => 1500,  // Price in cents (e.g., 15€)
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'formPaiment.php?status=success&id_r=' . $id_r,
            'cancel_url' => 'formPaiment.php?status=cancel&id_r=' . $id_r,
        ]);

        $success = true;
        $_SESSION['payment_session_id'] = $checkout_session->id;
    } catch (Exception $e) {
        $errorMessages[] = $e->getMessage();
    }
}

// Handle the payment response
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'success') {
        $paymentStatus = 'Paiement réussi !';
        // Update reservation status to 'paid'
        $stmt = $pdo->prepare("UPDATE reservation SET status = 'payé' WHERE id_r = :id_r");
        $stmt->execute(['id_r' => $id_r]);
    } elseif ($status === 'cancel') {
        $paymentStatus = 'Paiement annulé.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Stripe</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<h2>Paiement pour la réservation #<?= htmlspecialchars($id_r) ?></h2> <!-- Ensure id_r is echoed here -->

<?php if ($paymentStatus): ?>
    <h3><?= $paymentStatus ?></h3>
<?php endif; ?>

<?php if ($success && !$paymentStatus): ?>
    <button id="checkout-button">Payer avec Stripe</button>
<?php endif; ?>

<!-- Display error messages -->
<?php if (!empty($errorMessages)): ?>
    <div class="error-msg">
        <?php foreach ($errorMessages as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    const stripe = Stripe('pk_test_51RJJRvP5G4eRbLXry2Ds2B71pjkoFkCQ0CvczkgAw8StKrUXAvd1Pvuz3MXT3BPOZfpd9mIdHrjzv672qxn9sh4E001qTUOo1f'); // Your public key

    <?php if ($success && !$paymentStatus): ?>
        document.getElementById("checkout-button").addEventListener("click", () => {
            stripe.redirectToCheckout({ sessionId: '<?php echo $_SESSION['payment_session_id']; ?>' })
                .then((result) => {
                    if (result.error) {
                        alert(result.error.message);
                    }
                });
        });
    <?php endif; ?>
</script>

</body>
</html>
