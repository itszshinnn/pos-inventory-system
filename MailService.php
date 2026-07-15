<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/Inventory_backend/config.php')) {
    require_once __DIR__ . '/Inventory_backend/config.php';
} else {
    require_once __DIR__ . '/config.php';
}

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

            $mail->setFrom(SMTP_USER, "K's Inventory System");
            $mail->addAddress($targetEmail, 'Inventory Admin');
            $mail->isHTML(true);
            $mail->Subject = "Restock Delivered: Purchase Order #" . $poId;

            $itemListHtml = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">';
            $itemListHtml .= '<thead><tr style="background-color: #f8f9fb; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #ddd; color: #444;">Product Name</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd; color: #444;">Qty</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd; color: #444;">Unit Cost</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd; color: #444; text-align: right;">Total</th>
            </tr></thead><tbody>';

            $grandTotal = 0;

            foreach ($items as $item) {
                $qty = (int)$item['order_qty'];
                $unitCost = (float)$item['unit_cost'];
                $lineTotal = $qty * $unitCost;
                $grandTotal += $lineTotal;

                $itemListHtml .= "<tr>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: #333;'>" . htmlspecialchars($item['name']) . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: #333; font-weight: bold;'>" . $qty . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: #333;'>Php " . number_format($unitCost, 2) . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: #333; text-align: right; font-weight: bold;'>Php " . number_format($lineTotal, 2) . "</td>
                </tr>";
            }

            $itemListHtml .= "<tr>
                <td colspan='3' style='padding: 16px 12px; text-align: right; color: #333; border-top: 2px solid #ddd;'><strong>Grand Total:</strong></td>
                <td style='padding: 16px 12px; color: #2db84d; text-align: right; font-size: 16px; font-weight: bold; border-top: 2px solid #ddd;'>Php " . number_format($grandTotal, 2) . "</td>
            </tr>";

            $itemListHtml .= '</tbody></table>';

            $mail->Body = "
                <div style='font-family: \"Segoe UI\", Helvetica, Arial, sans-serif; max-width: 650px; margin: 0 auto; border: 1px solid #eef0f2; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>
                    <div style='background-color: #2db84d; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0; color: #ffffff; font-size: 22px; letter-spacing: 0.5px;'>Restock Confirmed</h2>
                    </div>
                    
                    <div style='padding: 32px; background-color: #ffffff;'>
                        <p style='font-size: 16px; color: #333; margin-top: 0;'>Hello,</p>
                        <p style='font-size: 15px; color: #555; line-height: 1.6;'>Purchase Order <strong style='color: #1a1a1a;'>#" . $poId . "</strong> has been officially marked as received and processed into the inventory by <strong style='color: #1a1a1a;'>" . htmlspecialchars($receivedBy) . "</strong>.</p>
                        
                        " . $itemListHtml . "
                        
                        <p style='font-size: 14px; color: #777; margin-top: 30px; border-top: 1px dashed #ddd; padding-top: 15px;'>Log in to your dashboard to view the updated stock ledger.</p>
                    </div>
                    
                    <div style='background-color: #f9f9f9; padding: 16px; text-align: center; font-size: 12px; color: #888;'>
                        &copy; " . date('Y') . " K's Inventory System. This is an automated message.
                    </div>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendLowStockAlert($lowStockItems)
    {
        if (empty($lowStockItems)) {
            return false;
        }

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

            $mail->setFrom(SMTP_USER, "K's Inventory System");
            $mail->addAddress($targetEmail, 'Inventory Admin');
            $mail->isHTML(true);

            $itemCount = count($lowStockItems);
            $mail->Subject = "ALERT: " . $itemCount . " Item(s) Low on Stock";

            $itemListHtml = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">';
            $itemListHtml .= '<thead><tr style="background-color: #fff0f0; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #ffbcbc; color: #d9534f;">Product Name</th>
                <th style="padding: 12px; border-bottom: 2px solid #ffbcbc; color: #d9534f; text-align: right;">Remaining Stock</th>
            </tr></thead><tbody>';

            foreach ($lowStockItems as $item) {
                $stockColor = $item['stock'] == 0 ? '#d9534f' : '#d4a017';
                $itemListHtml .= "<tr>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: #333; font-weight: bold;'>" . htmlspecialchars($item['name']) . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid #eee; color: " . $stockColor . "; text-align: right; font-weight: bold;'>" . $item['stock'] . " units</td>
                </tr>";
            }

            $itemListHtml .= '</tbody></table>';

            $mail->Body    = "
                <div style='font-family: \"Segoe UI\", Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eef0f2; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>
                    <div style='background-color: #d9534f; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0; color: #ffffff; font-size: 22px; letter-spacing: 0.5px;'>Low Stock Summary Report</h2>
                    </div>
                    
                    <div style='padding: 32px; background-color: #ffffff;'>
                        <p style='font-size: 16px; color: #333; margin-top: 0;'>Attention,</p>
                        <p style='font-size: 15px; color: #555; line-height: 1.6;'>The following items in your inventory have dropped below the safety threshold and require immediate restocking:</p>
                        
                        " . $itemListHtml . "
                        
                        <p style='font-size: 14px; color: #777; margin-top: 30px; border-top: 1px dashed #ddd; padding-top: 15px;'>Please log in to the dashboard to review your catalog and draft a new purchase order.</p>
                    </div>
                    
                    <div style='background-color: #f9f9f9; padding: 16px; text-align: center; font-size: 12px; color: #888;'>
                        &copy; " . date('Y') . " K's Inventory System. This is an automated message.
                    </div>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendCustomerReceipt($toEmail, $orderNo, $cart, $totalAmount, $paymentMethod)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom(SMTP_USER, "K's Inventory System");
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = "Electronic Receipt for Order #{$orderNo}";

            $itemsHtml = "";
            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: left;'>{$item['name']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['qty']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>Php " . number_format($item['price'], 2) . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>Php " . number_format($subtotal, 2) . "</td>
                </tr>";
            }

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 25px; border: 1px solid #eee; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <h2 style='text-align: center; color: #1a1a1a; margin-bottom: 5px;'>TRANSACTION RECEIPT</h2>
                <p style='text-align: center; font-size: 13px; color: #666; margin-top: 0;'>K's Inventory System</p>
                <hr style='border: none; border-top: 1px dashed #ccc; margin: 20px 0;'>
                
                <p><strong>Order Number:</strong> #{$orderNo}</p>
                <p><strong>Payment Method:</strong> {$paymentMethod}</p>
                <p><strong>Date/Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                
                <table style='width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;'>
                    <thead>
                        <tr style='background: #f8f9fb;'>
                            <th style='padding: 10px 8px; text-align: left; border-bottom: 2px solid #ddd;'>Item</th>
                            <th style='padding: 10px 8px; text-align: center; border-bottom: 2px solid #ddd;'>Qty</th>
                            <th style='padding: 10px 8px; text-align: right; border-bottom: 2px solid #ddd;'>Unit Price</th>
                            <th style='padding: 10px 8px; text-align: right; border-bottom: 2px solid #ddd;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>
                
                <h3 style='text-align: right; margin-top: 25px; color: #2db84d; font-size: 18px;'>TOTAL PAID: Php " . number_format($totalAmount, 2) . "</h3>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='text-align: center; font-size: 11px; color: #999;'>Thank you for choosing K's Inventory System. This is a system-generated electronic receipt.</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/email_error_log.txt', "Mailer Error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}
