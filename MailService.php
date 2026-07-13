<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Since they are in the root directory, we require them directly
require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';

class MailService
{

    public static function sendDeliveryNotification($poId, $items, $receivedBy)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'seanpaulforonda@gmail.com';
            $mail->Password   = 'tzvfkgkmmyzsecpv';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('seanpaulforonda@gmail.com', 'Inventory System');
            $mail->addAddress('seanforonda1738@gmail.com', 'Inventory Admin');

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

        try {
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'seanpaulforonda@gmail.com';
            $mail->Password   = 'tzvfkgkmmyzsecpv'; 
            $mail->SMTPSecure = 'tls'; 
            $mail->Port       = 587;

            $mail->setFrom('seanpaulforonda@gmail.com', 'Gadget Inventory System');
            $mail->addAddress('seanforonda1738@gmail.com', 'Inventory Admin');

            $mail->isHTML(true);
            $mail->Subject = "ALERT: Low Stock for " . $itemName;
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #d9534f;'>Low Stock Warning</h2>
                    <p>The inventory level for <strong>" . htmlspecialchars($itemName) . "</strong> has dropped below the safety threshold.</p>
                    <p><strong>Current Available Stock:</strong> <span style='color: #d9534f; font-size: 18px; font-weight: bold;'>" . $remainingStock . " units</span></p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p style='font-size: 12px; color: #777;'><em>This is an automated demo alert generated for your school defense project.</em></p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
