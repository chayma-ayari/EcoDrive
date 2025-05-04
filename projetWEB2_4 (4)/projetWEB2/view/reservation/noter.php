<?php
session_start();

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['rating'])) {
    $_SESSION['new_rating'] = true;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Note manquante']);
}
