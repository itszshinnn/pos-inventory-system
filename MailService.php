<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/Inventory_backend/config.php';
require_once __DIR__ . '/Database/Database.php';

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';

class MailService
{
    private static function getTargetAlertEmail()
    {
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'admin_alert_email'");
            $stmt->execute();
            $email = $stmt->fetchColumn();
            return !empty($email) ? $email : SMTP_USER;
        } catch (Exception $e) {
            return SMTP_USER;
        }
    }

    public static function sendDeliveryNotification($poId, $items, $receivedBy)
    {
        $mail = new PHPMailer(true);
        $targetEmail = self::getTargetAlertEmail();

        try {
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(SMTP_USER, 'Kinetix Store');
            $mail->addAddress($targetEmail, 'Inventory Admin');
            $mail->isHTML(true);
            $mail->Subject = "Restock Delivered: Purchase Order #" . $poId;

            $itemCount = count($items);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #2db84d;'>Restock Delivered Successfully</h2>
                    <p>Purchase Order <strong>#" . $poId . "</strong> has been marked as received by " . $receivedBy . ".</p>
                    <p>A total of " . $itemCount . " unique products were added to your inventory.</p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendLowStockAlert($itemName, $remainingStock)
    {
        $mail = new PHPMailer(true);
        $targetEmail = self::getTargetAlertEmail();

        try {
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(SMTP_USER, 'Kinetix Store');
            $mail->addAddress($targetEmail, 'Inventory Admin');
            $mail->isHTML(true);
            $mail->Subject = "ALERT: Low Stock for " . $itemName;
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #d9534f;'>Low Stock Warning</h2>
                    <p>The inventory level for <strong>" . htmlspecialchars($itemName) . "</strong> has dropped below the safety threshold.</p>
                    <p><strong>Current Available Stock:</strong> <span style='color: #d9534f; font-size: 18px; font-weight: bold;'>" . $remainingStock . " units</span></p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p style='font-size: 12px; color: #777;'><em>This is an automated system alert generated for your school defense project.</em></p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
