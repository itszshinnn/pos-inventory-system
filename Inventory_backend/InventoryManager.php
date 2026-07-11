<?php
require_once '../Database/Database.php';

class InventoryManager
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getAllProducts()
    {
        $sql = 'SELECT p.id, p.name, p.price_bought, p.price, p.stock, p.category_id, p.image, p.model_path, p.description, 
                       p.brand, p.color, p.type, p.capacity_size, p.resolution,
                       c.name AS category
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.id ASC';

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct(Product $product, $imageName, $modelPath, $description, $brand, $color, $type, $capacity_size, $resolution, $username)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO products (name, category_id, price_bought, price, stock, image, model_path, description, brand, color, type, capacity_size, resolution) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $product->getName(),
                $product->getCategoryId(),
                $product->getPriceBought(),
                $product->getPrice(),
                $product->getStock(),
                $imageName,
                $modelPath,
                $description,
                $brand,
                $color,
                $type,
                $capacity_size,
                $resolution
            ]);

            $productId = $this->db->lastInsertId();

            if ($product->getStock() > 0) {
                $initialBatch = $this->db->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
                $initialBatch->execute([$productId, $product->getStock(), $product->getStock(), $product->getPriceBought()]);
            }

            $log = $this->db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([$productId, $product->getName(), 'Added', null, $product->getStock(), $username]);

            $this->db->commit();
            return ['success' => true, 'id' => $productId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['error' => 'Server error saving product: ' . $e->getMessage()];
        }
    }

    public function deleteProduct($id, $username)
    {
        try {
            $getProduct = $this->db->prepare("SELECT name, stock, image FROM products WHERE id = ?");
            $getProduct->execute([$id]);
            $product = $getProduct->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['error' => 'Product not found', 'code' => 404];
            }

            if (!empty($product['image']) && $product['image'] !== 'default_product.png') {
                $filePath = '../Images/' . $product['image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $this->db->beginTransaction();

            $log = $this->db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([null, $product['name'], 'Deleted', $product['stock'], null, $username]);

            $deleteOrderItems = $this->db->prepare("DELETE FROM order_items WHERE product_id = ?");
            $deleteOrderItems->execute([$id]);

            $deleteBatches = $this->db->prepare("DELETE FROM product_batches WHERE product_id = ?");
            $deleteBatches->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['error' => 'Secure deletion process failed.', 'code' => 500];
        }
    }

    public function updateProduct($id, Product $product, $username)
    {
        try {
            $this->db->beginTransaction();

            $getOld = $this->db->prepare("SELECT stock, name FROM products WHERE id = ?");
            $getOld->execute([$id]);
            $oldProduct = $getOld->fetch(PDO::FETCH_ASSOC);

            if (!$oldProduct) {
                $this->db->rollBack();
                return ['error' => 'Product not found', 'code' => 404];
            }

            $newStock = $product->getStock();
            $oldStock = intval($oldProduct['stock']);
            $price = $product->getPrice();

            if ($newStock !== $oldStock) {
                if ($newStock > $oldStock) {
                    $addedQty = $newStock - $oldStock;
                    $addBatch = $this->db->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
                    $addBatch->execute([$id, $addedQty, $addedQty, $price]);
                } else {
                    $deductQty = $oldStock - $newStock;
                    $batchStmt = $this->db->prepare("SELECT id, quantity_remaining FROM product_batches WHERE product_id = ? AND quantity_remaining > 0 ORDER BY created_at ASC");
                    $batchStmt->execute([$id]);
                    $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($batches as $batch) {
                        if ($deductQty <= 0) break;
                        if ($batch['quantity_remaining'] >= $deductQty) {
                            $updateB = $this->db->prepare("UPDATE product_batches SET quantity_remaining = quantity_remaining - ? WHERE id = ?");
                            $updateB->execute([$deductQty, $batch['id']]);
                            $deductQty = 0;
                        } else {
                            $deductQty -= $batch['quantity_remaining'];
                            $updateB = $this->db->prepare("UPDATE product_batches SET quantity_remaining = 0 WHERE id = ?");
                            $updateB->execute([$batch['id']]);
                        }
                    }
                }
            }

            $stmt = $this->db->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->execute([
                $product->getName(),
                $product->getCategoryId(),
                $price,
                $newStock,
                $id
            ]);

            $log = $this->db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([$id, $product->getName(), 'Edited', $oldStock, $newStock, $username]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['error' => 'Database update interruption error.', 'code' => 500];
        }
    }

    public function getAllCategories()
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCategory($name)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (?)');
            $stmt->execute([$name]);
            return ['success' => true, 'id' => $this->db->lastInsertId(), 'name' => $name];
        } catch (PDOException $e) {
            return ['error' => 'Category already exists', 'code' => 409];
        }
    }

    public function updateCategory($id, $name)
    {
        try {
            $stmt = $this->db->prepare('UPDATE categories SET name = ? WHERE id = ?');
            $stmt->execute([$name, $id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['error' => 'Name already exists', 'code' => 409];
        }
    }

    public function deleteCategory($id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Failed to delete category.', 'code' => 500];
        }
    }
}
