<?php
require_once '../Database/Database.php';

class TransactionManager {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function processCheckout($cart, $paymentMethod, $discountAmount, $totalAmount, $cashReceived, $changeAmount) {
        try {
            $this->db->beginTransaction();

            $countStmt = $this->db->query('SELECT COUNT(*) FROM orders');
            $nextNumber = $countStmt->fetchColumn() + 1;
            $orderNo = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $orderSql = 'INSERT INTO orders (order_no, total_amount, discount_amount, payment_method, cash_received, change_amount) VALUES (?, ?, ?, ?, ?, ?)';
            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([$orderNo, $totalAmount, $discountAmount, $paymentMethod, $cashReceived, $changeAmount]);
            $orderId = $this->db->lastInsertId();

            $itemSql = 'INSERT INTO order_items (order_id, product_id, quantity, price_at_sale) VALUES (?, ?, ?, ?)';
            $itemStmt = $this->db->prepare($itemSql);

            foreach ($cart as $item) {
                $id    = intval($item['id']);
                $qty   = intval($item['qty']);
                $price = floatval($item['price']);

                $stmt = $this->db->prepare("SELECT stock, name FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) throw new Exception("Product ID " . $id . " could not be found.");
                if ($product['stock'] < $qty) throw new Exception("Insufficient stock parameters tracking for item: " . $product['name']);

                $batchStmt = $this->db->prepare("SELECT id, quantity_remaining FROM product_batches WHERE product_id = ? AND quantity_remaining > 0 ORDER BY created_at ASC");
                $batchStmt->execute([$id]);
                $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

                $remainingToDeduct = $qty;

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    $batchId = $batch['id'];
                    $currentBatchStock = $batch['quantity_remaining'];

                    if ($currentBatchStock >= $remainingToDeduct) {
                        $newBatchStock = $currentBatchStock - $remainingToDeduct;
                        $updateBatch = $this->db->prepare("UPDATE product_batches SET quantity_remaining = ? WHERE id = ?");
                        $updateBatch->execute([$newBatchStock, $batchId]);
                        $remainingToDeduct = 0;
                    } else {
                        $remainingToDeduct -= $currentBatchStock;
                        $updateBatch = $this->db->prepare("UPDATE product_batches SET quantity_remaining = 0 WHERE id = ?");
                        $updateBatch->execute([$batchId]);
                    }
                }

                if ($remainingToDeduct > 0) throw new Exception("FIFO Error: Batches for '" . $product['name'] . "' are desynced with total stock.");

                $updateProductsTable = $this->db->prepare("UPDATE products SET stock = (SELECT COALESCE(SUM(quantity_remaining), 0) FROM product_batches WHERE product_id = ?) WHERE id = ?");
                $updateProductsTable->execute([$id, $id]);
                $itemStmt->execute([$orderId, $id, $qty, $price]);
            }

            $this->db->commit();
            return [
                'success'  => true,
                'order_no' => $orderNo,
                'message'  => 'Transaction completed and logged successfully.'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>