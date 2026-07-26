<?php
require_once 'config.php';

class PaymentManager
{
    public function createGcashCheckout($cartArray, $totalAmount, $paymentMethod)
    {
        $amountInCentavos = intval(round($totalAmount * 100));

        $apiPaymentMethod = strtolower($paymentMethod);
        if ($apiPaymentMethod === 'maya') {
            $apiPaymentMethod = 'paymaya';
        }

        $itemNames = array_map(function ($item) {
            return $item['name'] . " (x" . $item['qty'] . ")";
        }, $cartArray);
        $summaryName = "Order: " . implode(", ", $itemNames);
        $summaryName = substr($summaryName, 0, 250);

        // --- DYNAMIC URL BUILDER ---
        // Dynamically detects http/https and your IP/Domain (e.g. 192.168.1.9 or localhost)
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];

        // Automatically finds the correct folder path on your server
        $backendDir = dirname($_SERVER['SCRIPT_NAME']); // Path to Inventory_backend
        $projectBase = dirname($backendDir);           // Root project folder

        $successUrl = "{$protocol}://{$host}{$backendDir}/payment_success.php";
        $cancelUrl  = "{$protocol}://{$host}{$projectBase}/Inventory_frontend/point_of_sale_menu.php";

        $payload = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => $amountInCentavos,
                            'name' => $summaryName,
                            'quantity' => 1
                        ]
                    ],
                    'payment_method_types' => [$apiPaymentMethod],
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'description' => 'Inventory System Checkout'
                ]
            ]
        ];

        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $apiKey = defined('PAYMONGO_SECRET_KEY') ? PAYMONGO_SECRET_KEY : '';
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode === 200 && isset($responseData['data']['attributes']['checkout_url'])) {
            return [
                'success' => true,
                'checkout_url' => $responseData['data']['attributes']['checkout_url'],
                'session_id' => $responseData['data']['id']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'API Error: ' . json_encode($responseData)
            ];
        }
    }
}
