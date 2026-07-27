<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/src/config.php')) {
    require_once __DIR__ . '/src/config.php';
} else {
    require_once __DIR__ . '/config.php';
}

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/libs/Exception.php';
require_once __DIR__ . '/src/libs/PHPMailer.php';
require_once __DIR__ . '/src/libs/SMTP.php';

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
    private static function wrapEmailHtml($headerTitle, $accentColor, $bodyContent)
    {
        return "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>" . htmlspecialchars($headerTitle) . "</title>
<style>
    body, table, td { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    body { margin: 0; padding: 0; background-color: #f4f4f7; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; }
    .email-wrapper { width: 100%; background-color: #f4f4f7; padding: 24px 12px; box-sizing: border-box; }
    .email-container { max-width: 600px; width: 100%; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .email-header { background-color: {$accentColor}; padding: 24px; text-align: center; }
    .email-header h2 { margin: 0; color: #ffffff; font-size: 20px; letter-spacing: 0.5px; }
    .email-body { padding: 28px 24px; background-color: #ffffff; box-sizing: border-box; }
    .email-footer { background-color: #f9f9f9; padding: 16px; text-align: center; font-size: 12px; color: #888; }
    p { word-wrap: break-word; }
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; table-layout: fixed; }
    table.data-table th, table.data-table td { padding: 10px 8px; word-break: break-word; }
    @media only screen and (max-width: 480px) {
        .email-body { padding: 20px 16px !important; }
        .email-header { padding: 18px !important; }
        .email-header h2 { font-size: 17px !important; }

        table.data-table thead { display: none; }
        table.data-table, table.data-table tbody, table.data-table tr, table.data-table td {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
        }
        table.data-table tr {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        table.data-table tr.total-row { border-bottom: none; }
        table.data-table td {
            border: none !important;
            padding: 3px 0 !important;
            text-align: left !important;
        }
        table.data-table td[data-label]:before {
            content: attr(data-label);
            font-weight: bold;
            display: inline-block;
            min-width: 105px;
            color: #888;
            font-size: 12px;
        }
    }
</style>
</head>
<body>
    <div class='email-wrapper'>
        <div class='email-container'>
            <div class='email-header'>
                <h2>" . htmlspecialchars($headerTitle) . "</h2>
            </div>
            <div class='email-body'>
                {$bodyContent}
            </div>
            <div class='email-footer'>
                &copy; " . date('Y') . " K's Inventory System. This is an automated message.
            </div>
        </div>
    </div>
</body>
</html>";
    }

    private static function baseMailer($targetEmail, $recipientName = null)
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet    = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom(SMTP_USER, "K's Inventory System");
        if ($recipientName !== null) {
            $mail->addAddress($targetEmail, $recipientName);
        } else {
            $mail->addAddress($targetEmail);
        }
        $mail->isHTML(true);

        return $mail;
    }

    public static function sendDeliveryNotification($poId, $items, $receivedBy)
    {
        $targetEmail = self::getTargetAlertEmail();

        try {
            $mail = self::baseMailer($targetEmail, 'Inventory Admin');
            $mail->Subject = "Restock Delivered: Purchase Order #" . $poId;

            $itemListHtml = "<table class='data-table'>
                <thead><tr style='background-color: #f8f9fb; text-align: left;'>
                    <th style='border-bottom: 2px solid #ddd; color: #444;'>Product Name</th>
                    <th style='border-bottom: 2px solid #ddd; color: #444;'>Qty</th>
                    <th style='border-bottom: 2px solid #ddd; color: #444;'>Unit Cost</th>
                    <th style='border-bottom: 2px solid #ddd; color: #444; text-align: right;'>Total</th>
                </tr></thead><tbody>";

            $grandTotal = 0;

            foreach ($items as $item) {
                $qty = (int)$item['order_qty'];
                $unitCost = (float)$item['unit_cost'];
                $lineTotal = $qty * $unitCost;
                $grandTotal += $lineTotal;
                $name = htmlspecialchars($item['name']);

                $itemListHtml .= "<tr>
                    <td data-label='Product' style='border-bottom: 1px solid #eee; color: #333;'>{$name}</td>
                    <td data-label='Qty' style='border-bottom: 1px solid #eee; color: #333; font-weight: bold;'>{$qty}</td>
                    <td data-label='Unit Cost' style='border-bottom: 1px solid #eee; color: #333;'>Php " . number_format($unitCost, 2) . "</td>
                    <td data-label='Total' style='border-bottom: 1px solid #eee; color: #333; text-align: right; font-weight: bold;'>Php " . number_format($lineTotal, 2) . "</td>
                </tr>";
            }

            $itemListHtml .= "<tr class='total-row'>
                <td colspan='3' style='text-align: right; color: #333; border-top: 2px solid #ddd;'><strong>Grand Total:</strong></td>
                <td data-label='Grand Total' style='color: #2db84d; text-align: right; font-size: 16px; font-weight: bold; border-top: 2px solid #ddd;'>Php " . number_format($grandTotal, 2) . "</td>
            </tr>";

            $itemListHtml .= '</tbody></table>';

            $bodyContent = "
                <p style='font-size: 16px; color: #333; margin-top: 0;'>Hello,</p>
                <p style='font-size: 15px; color: #555; line-height: 1.6;'>Purchase Order <strong style='color: #1a1a1a;'>#" . $poId . "</strong> has been officially marked as received and processed into the inventory by <strong style='color: #1a1a1a;'>" . htmlspecialchars($receivedBy) . "</strong>.</p>
                {$itemListHtml}
                <p style='font-size: 14px; color: #777; margin-top: 30px; border-top: 1px dashed #ddd; padding-top: 15px;'>Log in to your dashboard to view the updated stock ledger.</p>
            ";

            $mail->Body = self::wrapEmailHtml('Restock Confirmed', '#2db84d', $bodyContent);

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

        $targetEmail = self::getTargetAlertEmail();

        try {
            $mail = self::baseMailer($targetEmail, 'Inventory Admin');

            $itemCount = count($lowStockItems);
            $mail->Subject = "ALERT: " . $itemCount . " Item(s) Low on Stock";

            $itemListHtml = "<table class='data-table'>
                <thead><tr style='background-color: #fff0f0; text-align: left;'>
                    <th style='border-bottom: 2px solid #ffbcbc; color: #d9534f;'>Product Name</th>
                    <th style='border-bottom: 2px solid #ffbcbc; color: #d9534f; text-align: right;'>Remaining Stock</th>
                </tr></thead><tbody>";

            foreach ($lowStockItems as $item) {
                $stockColor = $item['stock'] == 0 ? '#d9534f' : '#d4a017';
                $name = htmlspecialchars($item['name']);
                $itemListHtml .= "<tr>
                    <td data-label='Product' style='border-bottom: 1px solid #eee; color: #333; font-weight: bold;'>{$name}</td>
                    <td data-label='Remaining' style='border-bottom: 1px solid #eee; color: {$stockColor}; text-align: right; font-weight: bold;'>" . $item['stock'] . " units</td>
                </tr>";
            }

            $itemListHtml .= '</tbody></table>';

            $bodyContent = "
                <p style='font-size: 16px; color: #333; margin-top: 0;'>Attention,</p>
                <p style='font-size: 15px; color: #555; line-height: 1.6;'>The following items in your inventory have dropped below the safety threshold and require immediate restocking:</p>
                {$itemListHtml}
                <p style='font-size: 14px; color: #777; margin-top: 30px; border-top: 1px dashed #ddd; padding-top: 15px;'>Please log in to the dashboard to review your catalog and draft a new purchase order.</p>
            ";

            $mail->Body = self::wrapEmailHtml('Low Stock Summary Report', '#d9534f', $bodyContent);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendCustomerReceipt($toEmail, $orderNo, $cart, $totalAmount, $paymentMethod)
    {
        try {
            $mail = self::baseMailer($toEmail);
            $mail->Subject = "Electronic Receipt for Order #{$orderNo}";

            $itemsHtml = "<table class='data-table'>
                <thead>
                    <tr style='background: #f8f9fb;'>
                        <th style='text-align: left; border-bottom: 2px solid #ddd;'>Item</th>
                        <th style='text-align: center; border-bottom: 2px solid #ddd;'>Qty</th>
                        <th style='text-align: right; border-bottom: 2px solid #ddd;'>Unit Price</th>
                        <th style='text-align: right; border-bottom: 2px solid #ddd;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>";

            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $name = htmlspecialchars($item['name']);
                $itemsHtml .= "
                <tr>
                    <td data-label='Item' style='border-bottom: 1px solid #ddd; text-align: left;'>{$name}</td>
                    <td data-label='Qty' style='border-bottom: 1px solid #ddd; text-align: center;'>{$item['qty']}</td>
                    <td data-label='Unit Price' style='border-bottom: 1px solid #ddd; text-align: right;'>Php " . number_format($item['price'], 2) . "</td>
                    <td data-label='Subtotal' style='border-bottom: 1px solid #ddd; text-align: right;'>Php " . number_format($subtotal, 2) . "</td>
                </tr>";
            }

            $itemsHtml .= "</tbody></table>";

            $bodyContent = "
                <p style='text-align: center; font-size: 13px; color: #666; margin-top: 0;'>K's Inventory System</p>
                <hr style='border: none; border-top: 1px dashed #ccc; margin: 20px 0;'>

                <p style='margin: 6px 0;'><strong>Order Number:</strong> #{$orderNo}</p>
                <p style='margin: 6px 0;'><strong>Payment Method:</strong> {$paymentMethod}</p>
                <p style='margin: 6px 0;'><strong>Date/Time:</strong> " . date('Y-m-d H:i:s') . "</p>

                {$itemsHtml}

                <h3 style='text-align: right; margin-top: 25px; color: #2db84d; font-size: 18px;'>TOTAL PAID: Php " . number_format($totalAmount, 2) . "</h3>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='text-align: center; font-size: 11px; color: #999;'>Thank you for choosing K's Inventory System. This is a system-generated electronic receipt.</p>
            ";

            $mail->Body = self::wrapEmailHtml('Transaction Receipt', '#1a1a1a', $bodyContent);

            $mail->send();
            return true;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/email_error_log.txt', "Mailer Error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}
