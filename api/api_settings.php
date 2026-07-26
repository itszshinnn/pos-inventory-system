<?php
session_start();
header('Content-Type: application/json');

require '../src/Database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'admin_alert_email'");
            $stmt->execute();
            $email = $stmt->fetchColumn() ?: 'seanpaulforonda@gmail.com'; 
            echo json_encode(['admin_alert_email' => $email]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'POST':
    case 'PUT':
        $email = trim($input['admin_alert_email'] ?? '');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Please provide a valid email address.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value) 
                VALUES ('admin_alert_email', ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$email, $email]);
            echo json_encode(['success' => true, 'message' => 'Notification email updated successfully.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update settings: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}