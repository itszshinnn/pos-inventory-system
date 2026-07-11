<?php

class Product {
    private $name;
    private $categoryId;
    private $price_bought; 
    private $price;
    private $stock;

    public function __construct($name = "", $categoryId = 0, $price_bought = 0.00, $price = 0.00, $stock = 0) {
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->setPriceBought($price_bought);
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

    public function setPriceBought($price_bought) {
        if ($price_bought >= 0) {
            $this->price_bought = (float)$price_bought;
        }
    }

    public function getPriceBought() {
        return $this->price_bought;
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
            'price_bought' => $this->price_bought,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}
?>