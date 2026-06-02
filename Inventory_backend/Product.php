<?php

class Product {
    private $name;
    private $categoryId;
    private $price;
    private $stock;

    public function __construct($name = "", $categoryId = 0, $price = 0.00, $stock = 0) {
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->setPrice($price);
        $this->setStock($stock);
    }


    public function setName($name) {
        $this->name = trim($name);
    }

    public function getName() {
        return $this->name;
    }

    public function setCategoryId($categoryId) {
        $this->categoryId = (int)$categoryId;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setPrice($price) {
        if ($price >= 0) {
            $this->price = (float)$price;
        }
    }

    public function getPrice() {
        return $this->price;
    }

    public function setStock($stock) {
        if ($stock >= 0) {
            $this->stock = (int)$stock;
        }
    }

    public function getStock() {
        return $this->stock;
    }

    public function toArray() {
        return [
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}
?>