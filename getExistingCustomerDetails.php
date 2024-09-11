<?php
include 'includes/DB.php';
global $pdo;

$mobile = $_POST['mobile'];

$stmt = $pdo->prepare("SELECT * FROM customers WHERE contact_number = :mobile");
$stmt->execute(['mobile' => $mobile]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($customer);
