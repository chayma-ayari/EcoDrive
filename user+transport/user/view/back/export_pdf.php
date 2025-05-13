<?php
require_once __DIR__ . '/../../config/database.php';

// Inclure FPDF avec le bon chemin
require(__DIR__ . '/../../vendor/fpdf/fpdf.php');

session_start();

// Vérifier l'authentification
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

class PDF extends FPDF {
    // En-tête
    function Header() {
        // Logo ou titre
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Liste des Utilisateurs', 0, 1, 'C');
        $this->Ln(10);

        // En-têtes des colonnes
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(76, 175, 80); // Vert
        $this->SetTextColor(255); // Blanc
        $this->Cell(15, 8, 'ID', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Nom', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Prenom', 1, 0, 'C', true);
        $this->Cell(50, 8, 'Email', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Telephone', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Adresse', 1, 1, 'C', true);
    }

    // Pied de page
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Créer l'instance PDF
$pdf = new PDF('L'); // L pour Landscape (paysage)
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Connexion à la base de données
$database = new Database();
$pdo = $database->getConnection();

// Récupérer les utilisateurs
$query = "SELECT id, nom, prenom, email, telephone, adresse FROM user ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Couleurs alternées pour les lignes
$fill = false;

while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->SetFillColor(242, 242, 242); // Gris clair
    $pdf->SetTextColor(0); // Noir
    
    $pdf->Cell(15, 7, $user['id'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 7, utf8_decode($user['nom']), 1, 0, 'L', $fill);
    $pdf->Cell(35, 7, utf8_decode($user['prenom']), 1, 0, 'L', $fill);
    $pdf->Cell(50, 7, utf8_decode($user['email']), 1, 0, 'L', $fill);
    $pdf->Cell(30, 7, $user['telephone'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 7, utf8_decode($user['adresse']), 1, 1, 'L', $fill);
    
    $fill = !$fill; // Alterner les couleurs
}

// Générer le PDF
$pdf->Output('D', 'liste_utilisateurs.pdf');
?>
