<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../Inventory_frontend/login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Mono:wght@400;500&display=swap');
  </style>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'DM Sans', 'Segoe UI', sans-serif;
    }

    body {
      background: #f5f5f5;
      overflow: hidden;
    }

    .container {
      display: flex;
      width: 100%;
      height: 100vh;
    }

    model-viewer {
      background-color: #cccccc;
    }

    .left-panel {
      width: 67%;
      background: #f5f5f5;
      border-right: 1px solid #cfcfcf;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      height: 55px;
      background: #333538;
      display: flex;
      align-items: center;
      padding: 0 16px;
      gap: 16px;
      color: white;
      flex-shrink: 0;
    }

    .topbar-admin {
      position: relative;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      background: #ff4b4b;
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      transition: .2s;
    }

    .topbar-admin:hover {
      background: #ff2f2f;
    }

    .profile-img {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      object-fit: cover;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      left: 0;
      top: 115%;
      background-color: #ffffff;
      min-width: 140px;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      z-index: 1050;
      overflow: hidden;
      border: 1px solid #ddd;
    }

    .dropdown-menu a {
      color: #ff4b4b;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-weight: 600;
      transition: 0.2s;
    }

    .dropdown-menu a:hover {
      background-color: #fff0f0;
    }

    .title {
      font-size: 18px;
      font-weight: 700;
      flex: 1;
    }

    .search-container {
      padding: 12px 14px 8px;
    }

    .search-box {
      width: 100%;
      height: 42px;
      border-radius: 14px;
      border: 2.5px solid #bcbcbc;
      padding: 0 16px;
      font-size: 16px;
      outline: none;
      transition: .2s;
    }

    .search-box:focus {
      border-color: #4d66ff;
    }

    .categories {
      display: flex;
      gap: 8px;
      padding: 0 14px 10px;
      flex-wrap: wrap;
    }

    .category-btn {
      border: 2px solid #a7a7a7;
      background: white;
      padding: 6px 16px;
      border-radius: 30px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: .2s;
    }

    .category-btn:hover {
      transform: scale(1.05);
    }

    .category-btn.active {
      background: #4d66ff;
      color: white;
      border-color: #4d66ff;
    }

    .brand-name {
      font-size: 11px;
      color: #1c1c1c;
      font-weight: 600;
      text-transform: uppercase;
      margin-top: 2px;
    }

    .filter-select {
      height: 38px;
      border-radius: 10px;
      border: 1.5px solid #bcbcbc;
      padding: 0 12px;
      font-size: 13px;
      font-weight: 600;
      outline: none;
      background: white;
      cursor: pointer;
      color: #333;
    }

    .filter-select:focus {
      border-color: #4d66ff;
    }

    .reset-filter-btn {
      height: 38px;
      border-radius: 10px;
      border: 1.5px solid #ff7070;
      padding: 0 16px;
      font-size: 13px;
      font-weight: 700;
      background: white;
      cursor: pointer;
      color: #ff7070;
      transition: .2s;
    }

    .reset-filter-btn:hover {
      background: #ff2c2c;
      color: white;
      border-color: #ff2c2c;
    }

    .products-wrapper {
      flex: 1;
      overflow-y: auto;
      padding: 10px 14px 14px;
      scrollbar-gutter: stable;
    }

    .products {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
    }

    .product-card {
      width: 155px;
      height: 250px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: #d9d9d9;
      border-radius: 16px;
      padding: 14px;
      text-align: center;
      cursor: pointer;
      transition: .2s;
    }

    .product-card:hover {
      box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
    }

    .product-card.out-of-stock {
      opacity: .55;
      cursor: not-allowed;
    }

    .product-card.out-of-stock:hover {
      transform: none;
      box-shadow: none;
    }

    .product-card img {
      width: 75px;
      height: 75px;
      object-fit: contain;
      margin-bottom: 7px;
    }

    .product-name {
      font-size: 13px;
      font-weight: 700;
      color: #333;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 32px;
    }

    .price {
      font-family: 'DM Mono', monospace;
      font-size: 13px;
      font-weight: 500;
      margin-top: 3px;
    }

    .stock {
      margin-top: 5px;
      font-size: 12px;
      font-weight: 700;
    }

    .green {
      color: #2db84d;
    }

    .orange {
      color: #d9992f;
    }

    .red {
      color: #ff5c5c;
    }

    .no-results {
      width: 100%;
      text-align: center;
      color: #aaa;
      font-size: 14px;
      font-weight: 600;
      padding: 30px 0;
    }

    .spinner-wrap {
      width: 100%;
      text-align: center;
      padding: 40px 0;
      color: #aaa;
      font-size: 14px;
      font-weight: 600;
    }

    .btn-add {
      flex: 2;
      background: #5470ff;
      color: white;
      border: none;
      padding: 6px;
      border-radius: 6px;
      font-weight: 600;
      transition: background 0.2s ease, transform 0.1s ease;
    }

    .btn-add:hover:not(:disabled) {
      background: #3c52d1;
    }

    .btn-add:active:not(:disabled) {
      transform: scale(0.9);
    }

    .btn-view {
      flex: 1;
      background: #5a5a5a;
      color: #ffffff;
      border: none;
      padding: 6px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.2s ease, transform 0.1s ease;
    }

    .btn-view:hover {
      background: #3b3b3b;
    }

    .btn-view:active {
      transform: scale(0.9);
    }

    .right-panel {
      width: 33%;
      background: #efefef;
      display: flex;
      flex-direction: column;
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 13px 16px;
      border-bottom: 1px solid #c7c7c7;
      flex-shrink: 0;
    }

    .order-header h1 {
      font-size: 20px;
    }

    .count {
      height: 28px;
      background: #5470ff;
      color: white;
      border-radius: 20px;
      padding: 0 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 600;
      white-space: nowrap;
      box-shadow: 0 2px 6px rgba(84, 112, 255, 0.3);
    }

    .order-items {
      flex: 1;
      overflow-y: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: #000000;
      font-size: 16px;
      font-weight: 500;
      border-bottom: 1px solid #c7c7c7;
      padding: 10px 0;
      gap: 1px;
      scrollbar-gutter: stable;
    }

    .order-items.has-items {
      justify-content: flex-start;
      padding: 12px 10px;
    }

    .cart-item {
      width: 100%;
      background: white;
      padding: 10px 12px;
      border-radius: 12px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .cart-item-info .name {
      font-size: 13px;
      font-weight: 700;
    }

    .cart-item-info .item-price {
      font-family: 'DM Mono', monospace;
      font-size: 13px;
      font-weight: 500;
      color: #000000;
    }

    .cart-item-controls {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .qty-badge {
      font-family: 'DM Mono', monospace;
      font-size: 13px;
      font-weight: 500;
      background: #e0e0e0;
      padding: 2px 8px;
      border-radius: 6px;
    }

    .remove-btn {
      background: red;
      color: white;
      border: none;
      padding: 5px 9px;
      border-radius: 7px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 700;
    }

    .summary {
      padding: 13px 14px;
      flex-shrink: 0;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      font-size: 15px;
      font-weight: 700;
    }

    .summary-row span:last-child {
      font-family: 'DM Mono', monospace;
      font-weight: 500;
    }

    .discount-row {
      display: flex;
      gap: 8px;
      align-items: center;
      margin-bottom: 12px;
    }

    .discount-input {
      width: 150px;
      height: 38px;
      border-radius: 8px;
      border: 2px solid #a8a8a8;
      padding: 0 10px;
      font-size: 15px;
      outline: none;
      transition: .2s;
    }

    .discount-input:focus {
      border-color: #4d66ff;
    }

    .discount-type-select {
      height: 38px;
      border-radius: 8px;
      border: 2px solid #a8a8a8;
      padding: 0 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      outline: none;
      background: white;
    }

    .discount-result {
      flex: 1;
      font-family: 'DM Mono', monospace;
      font-size: 14px;
      font-weight: 500;
      color: #ff4b4b;
      text-align: right;
      white-space: nowrap;
    }

    .checkout-btn {
      width: 100%;
      height: 48px;
      border: none;
      background: #2e2e2e;
      color: white;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-bottom: 10px;
      transition: .2s;
    }

    .checkout-btn:hover {
      background: #00000097;
    }

    .clear-btn {
      width: 100%;
      height: 48px;
      border: 2.5px solid #ff7070;
      background: white;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      color: #ff7070;
      transition: .2s;
    }

    .clear-btn:hover {
      background: #ff2c2c;
      border: 2.5px solid #ff2c2c;
      color: white;
    }

    .modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .modal-card {
      background: white;
      width: 440px;
      border-radius: 24px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-card h2 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 20px;
      color: #1a1a1a;
    }

    .modal-items-list {
      max-height: 160px;
      overflow-y: auto;
      margin-bottom: 12px;
    }

    .modal-item-row {
      display: flex;
      justify-content: space-between;
      font-size: 15px;
      color: #555;
      margin-bottom: 8px;
    }

    .modal-item-row span:last-child {
      font-family: 'DM Mono', monospace;
    }

    .modal-divider {
      border: none;
      border-top: 2px solid #000000;
      margin: 12px 0;
    }

    .modal-total-row {
      display: flex;
      justify-content: space-between;
      font-size: 20px;
      font-weight: 700;
      color: #000000;
      margin-bottom: 16px;
    }

    .modal-total-row span:last-child {
      font-family: 'DM Mono', monospace;
      font-weight: 500;
    }

    .modal-label {
      font-size: 14px;
      color: #8c8c8c;
      font-weight: 500;
      margin-bottom: 10px;
    }

    .payment-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 18px;
    }

    .pay-method-btn {
      background: white;
      border: 1.5px solid #dcdcdc;
      padding: 12px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 500;
      color: #666;
      cursor: pointer;
      transition: .2s;
      text-align: center;
    }

    .pay-method-btn:hover {
      border-color: #5470ff;
    }

    .pay-method-btn.selected {
      background: #eff2ff;
      border: 2px solid #5470ff;
      color: #5470ff;
      font-weight: 600;
    }

    .cash-input-wrap {
      margin-bottom: 20px;
    }

    .cash-received-field {
      width: 100%;
      height: 44px;
      border: 1.5px solid #dcdcdc;
      border-radius: 10px;
      padding: 0 14px;
      font-size: 16px;
      font-family: 'DM Mono', monospace;
      outline: none;
    }

    .cash-received-field:focus {
      border-color: #5470ff;
    }

    .change-display {
      font-family: 'DM Mono', monospace;
      font-size: 14px;
      font-weight: 500;
      color: #2db84d;
      margin-top: 6px;
      text-align: right;
    }

    .modal-actions {
      display: grid;
      grid-template-columns: 1fr 1.3fr;
      gap: 12px;
    }

    .modal-cancel-btn {
      height: 46px;
      background: #f2f2f2;
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      color: #4a4a4a;
      cursor: pointer;
    }

    .modal-cancel-btn:hover {
      background: #e6e6e6;
    }

    .modal-confirm-btn {
      height: 46px;
      background: #2cb864;
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      color: white;
      cursor: pointer;
    }

    .modal-confirm-btn:hover {
      background: #239450;
    }

    .receipt-modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2000;
    }

    .receipt-card {
      background: white;
      width: 380px;
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .receipt-card h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 4px;
      text-align: center;
    }

    .receipt-subtitle {
      font-size: 13px;
      color: #666;
      text-align: center;
      margin-bottom: 20px;
    }

    .receipt-meta-row {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #444;
      margin-bottom: 6px;
    }

    .receipt-divider {
      border: none;
      border-top: 1px dashed #bbb;
      margin: 14px 0;
    }

    .receipt-items-box {
      margin: 10px 0;
    }

    .receipt-item-line {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin-bottom: 8px;
      color: #333;
    }

    .receipt-item-line span:last-child {
      font-family: 'DM Mono', monospace;
    }

    .receipt-total-line {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin-top: 10px;
    }

    .receipt-total-line span:last-child {
      font-family: 'DM Mono', monospace;
    }

    .receipt-close-btn {
      width: 100%;
      height: 42px;
      background: #333538;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 20px;
      transition: 0.2s;
    }

    .receipt-close-btn:hover {
      background: #1a1a1a;
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="left-panel">

      <div class="topbar">
        <div class="topbar-admin" onclick="toggleUserDropdown(event)">
          <img src="../Images/profile.png" alt="Profile" class="profile-img">
          <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?> ▼

          <div id="userDropdownMenu" class="dropdown-menu">
            <a href="../Inventory_frontend/logout.php">Logout</a>
          </div>
        </div>

        <div class="title">K's Inventory System</div>
      </div>

      <div class="search-container">
        <input type="text" class="search-box" id="searchInput" placeholder="Search...">
      </div>

      <div class="categories" id="filtersContainer" style="display: flex; gap: 8px; padding: 0 14px 14px; flex-wrap: wrap;">
        <select id="filterCategory" class="filter-select" onchange="renderProducts()">
          <option value="All">All Categories</option>
        </select>
        <select id="filterBrand" class="filter-select" onchange="renderProducts()">
          <option value="All">All Brands</option>
        </select>
        <select id="sortPrice" class="filter-select" onchange="renderProducts()">
          <option value="default">Sort by Price</option>
          <option value="asc">Price: Low to High</option>
          <option value="desc">Price: High to Low</option>
        </select>
        <select id="filterStock" class="filter-select" onchange="renderProducts()">
          <option value="All">All Stock</option>
          <option value="in_stock">In Stock</option>
          <option value="low_stock">Low Stock (≤3)</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>
        <button class="reset-filter-btn" onclick="resetFilters()">Reset</button>
      </div>

      <div class="products-wrapper">
        <div class="products" id="products">
          <div class="spinner-wrap">Loading products…</div>
        </div>
      </div>

    </div>

    <div class="right-panel">

      <div class="order-header">
        <h1>Current Order</h1>
        <div class="count" id="count">0</div>
      </div>

      <div class="order-items" id="orderItems">
        <p>No items yet.</p>
        <p>Click a product to add here.</p>
      </div>

      <div class="summary">

        <div class="summary-row">
          <span>Subtotal</span>
          <span id="subtotal">Php0.00</span>
        </div>

        <div class="discount-row">
          <input type="number" class="discount-input" id="discountInput" placeholder="Discount" min="0">
          <select class="discount-type-select" id="discountType">
            <option value="%">%</option>
            <option value="Php">Php</option>
          </select>
          <div class="discount-result" id="discountResult"></div>
        </div>

        <div class="summary-row">
          <span>Total</span>
          <span id="total">Php0.00</span>
        </div>

        <button class="checkout-btn" onclick="openCheckoutModal()">Proceed to checkout</button>
        <button class="clear-btn" onclick="clearOrder()">Clear Order</button>

      </div>
    </div>
  </div>

  <div class="modal-backdrop" id="checkoutModal">
    <div class="modal-card">
      <h2>Checkout</h2>

      <div class="modal-items-list" id="modalItemsList"></div>

      <hr class="modal-divider">

      <div class="modal-total-row">
        <span>Total</span>
        <span id="modalTotalAmount">Php0.00</span>
      </div>

      <div class="modal-label">Payment method</div>
      <div class="payment-grid">
        <button class="pay-method-btn selected" onclick="selectPaymentMethod(this, 'Cash')">Cash</button>
        <button class="pay-method-btn" onclick="selectPaymentMethod(this, 'Card')">Card</button>
        <button class="pay-method-btn" onclick="selectPaymentMethod(this, 'GCash')">GCash</button>
        <button class="pay-method-btn" onclick="selectPaymentMethod(this, 'Maya')">Maya</button>
      </div>

      <div class="cash-input-wrap" id="cashInputContainer">
        <div class="modal-label">Cash received:</div>
        <input type="number" class="cash-received-field" id="cashReceivedInput" placeholder="0.00" min="0" step="any">
        <div class="change-display" id="changeDisplay"></div>
      </div>

      <div class="modal-actions">
        <button class="modal-cancel-btn" onclick="closeCheckoutModal()">Cancel</button>
        <button class="modal-confirm-btn" onclick="confirmSale()">Confirm Sale</button>
      </div>
    </div>
  </div>

  <div id="model-modal" class="modal-backdrop" style="z-index: 1000;">
    <div class="modal-card" style="width: 700px; max-width: 90%; display: flex; gap: 20px; padding: 20px;">

      <div id="viewer-container" style="flex: 1; height: 300px; border-radius: 12px; overflow: hidden;">
      </div>

      <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <h2 id="modal-title" style="margin-bottom: 10px; font-size: 20px;">Product Title</h2>
          <p id="modal-desc" style="font-size: 14px; color: #555; line-height: 1.5;">Product description goes here...</p>
        </div>
        <button onclick="close3DViewer()" style="margin-top: 20px; padding: 12px; background: #ff4b4b; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Close</button>
      </div>

    </div>
  </div>
  <div class="receipt-modal-backdrop" id="receiptModal">
    <div class="receipt-card">
      <h2>TRANSACTION RECEIPT</h2>
      <div class="receipt-subtitle">K's Inventory System</div>

      <div class="receipt-meta-row">
        <span>Order Number:</span>
        <strong id="rcptOrderNo">#0000</strong>
      </div>
      <div class="receipt-meta-row">
        <span>Cashier:</span>
        <span id="rcptCashier">-</span>
      </div>
      <div class="receipt-meta-row">
        <span>Date/Time:</span>
        <span id="rcptDate">-</span>
      </div>
      <div class="receipt-meta-row">
        <span>Payment Method:</span>
        <span id="rcptPayment">-</span>
      </div>

      <hr class="receipt-divider">

      <div class="receipt-items-box" id="rcptItemsBox"></div>

      <hr class="receipt-divider">

      <div class="receipt-meta-row">
        <span>Discount applied:</span>
        <span id="rcptDiscount">-</span>
      </div>
      <div class="receipt-total-line">
        <span>TOTAL PAID:</span>
        <span id="rcptTotal">Php0.00</span>
      </div>

      <div class="receipt-meta-row" style="margin-top: 14px;">
        <span>Cash Received:</span>
        <span id="rcptCashReceived" style="font-family: 'DM Mono', monospace; font-weight: 500; color: #444;">Php0.00</span>
      </div>
      <div class="receipt-meta-row">
        <span>Change Given:</span>
        <span id="rcptChange" style="font-family: 'DM Mono', monospace; font-weight: 500; color: #444;">Php0.00</span>
      </div>

      <button class="receipt-close-btn" onclick="closeReceiptModal()">Close Receipt</button>
    </div>
  </div>
  <script>
    const cashierName = <?= json_encode($_SESSION['username']) ?>;
    let allProducts = [];
    let cart = {};
    let cartTotal = 0;
    let finalCalculatedTotal = 0;
    let cartCount = 0;
    let selectedPayment = 'Cash';

    const DEFAULT_IMG = 'https://cdn-icons-png.flaticon.com/512/2721/2721297.png';

    async function loadData() {
      try {
        const [pRes, cRes] = await Promise.all([
          fetch('../Inventory_backend/api_products.php'),
          fetch('../Inventory_backend/api_categories.php')
        ]);

        allProducts = await pRes.json();
        const allCategories = await cRes.json();

        initializeFilters(allCategories);
        renderProducts();
      } catch (err) {
        document.getElementById('products').innerHTML =
          '<div class="no-results">Failed to load products. Please check your connection.</div>';
      }
    }

    function initializeFilters(categories) {
      const catSelect = document.getElementById('filterCategory');
      categories.forEach(cat => {
        catSelect.innerHTML += `<option value="${cat.name}">${cat.name}</option>`;
      });

      const brandSelect = document.getElementById('filterBrand');
      const uniqueBrands = [...new Set(allProducts.map(p => p.brand).filter(b => b))].sort();
      uniqueBrands.forEach(brand => {
        brandSelect.innerHTML += `<option value="${brand}">${brand}</option>`;
      });
    }

    function stockLabel(s) {
      const n = Number(s);
      return n === 0 ? 'No stocks left' : n + ' stocks left';
    }

    function stockClass(s) {
      const n = Number(s);
      return n === 0 ? 'red' : n <= 3 ? 'orange' : 'green';
    }

    function renderCategories(categories) {
      const container = document.getElementById('categories');

      container.innerHTML = '<button class="category-btn active" data-cat="All">All</button>';

      categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'category-btn';
        btn.dataset.cat = cat.name;
        btn.textContent = cat.name;
        btn.addEventListener('click', () => {
          document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          activeCategory = cat.name;
          renderProducts();
        });
        container.appendChild(btn);
      });

      container.querySelector('[data-cat="All"]').addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeCategory = 'All';
        renderProducts();
      });
    }

    function stockLabel(s) {
      const n = Number(s);
      return n === 0 ? 'No stocks left' : n + ' stocks left';
    }

    function stockClass(s) {
      const n = Number(s);
      return n === 0 ? 'red' : n <= 3 ? 'orange' : 'green';
    }

    function renderProducts() {
      const container = document.getElementById('products');

      const query = document.getElementById('searchInput').value.toLowerCase();
      const cat = document.getElementById('filterCategory').value;
      const brand = document.getElementById('filterBrand').value;
      const priceSort = document.getElementById('sortPrice').value;
      const stock = document.getElementById('filterStock').value;

      let filtered = allProducts.filter(p => {
        const matchSearch = p.name.toLowerCase().includes(query) || (p.brand && p.brand.toLowerCase().includes(query));
        const matchCat = cat === 'All' || p.category === cat;
        const matchBrand = brand === 'All' || p.brand === brand;

        const dbStock = Number(p.stock);
        const qtyInCart = cart[p.id] ? cart[p.id].qty : 0;
        const displayStock = Math.max(0, dbStock - qtyInCart);

        let matchStock = true;
        if (stock === 'in_stock') matchStock = displayStock > 0;
        if (stock === 'low_stock') matchStock = displayStock > 0 && displayStock <= 3;
        if (stock === 'out_of_stock') matchStock = displayStock === 0;

        return matchSearch && matchCat && matchBrand && matchStock;
      });

      if (priceSort === 'asc') {
        filtered.sort((a, b) => Number(a.price) - Number(b.price));
      } else if (priceSort === 'desc') {
        filtered.sort((a, b) => Number(b.price) - Number(a.price));
      } else {
        filtered.sort((a, b) => a.id - b.id);
      }

      if (!filtered.length) {
        container.innerHTML = '<div class="no-results">No products found based on your filters.</div>';
        return;
      }

      container.innerHTML = filtered.map(p => {
        const dbStock = Number(p.stock);
        const price = Number(p.price);
        const qtyInCart = cart[p.id] ? cart[p.id].qty : 0;
        const displayStock = Math.max(0, dbStock - qtyInCart);
        const outClass = displayStock === 0 ? ' out-of-stock' : '';

        let imgUrl = DEFAULT_IMG;
        if (p.image && p.image !== 'default_product.png') {
          imgUrl = `../Images/${p.image}`;
        }

        return `
        <div class="product-card${outClass}">
            <div>
              <img src="${imgUrl}" alt="${p.name}" onerror="this.src='${DEFAULT_IMG}'">
              <div class="product-name">${p.name}</div>
              <div class="brand-name">${p.brand || 'No Brand'}</div>
              <div class="price">Php${price.toFixed(2)}</div>
              <div class="stock ${stockClass(displayStock)}">${stockLabel(displayStock)}</div>
            </div>
            
              <div style="display: flex; gap: 6px; margin-top: 12px; margin-bottom: 10px;">
              <button class="btn-add" 
                      onclick="${displayStock > 0 ? `addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${price})` : ''}" 
                      style="cursor: ${displayStock > 0 ? 'pointer' : 'not-allowed'}; opacity: ${displayStock > 0 ? '1' : '0.6'};"
                      ${displayStock === 0 ? 'disabled' : ''}>
                + Add
              </button>
              
              <button class="btn-view" onclick="open3DViewer(${p.id})">
                View
              </button>
            </div>
          </div>
        `;
      }).join('');
    }

    document.getElementById('searchInput').addEventListener('input', renderProducts);

    document.getElementById('searchInput').addEventListener('input', e => {
      searchQuery = e.target.value;
      renderProducts();
    });

    function addToCart(id, name, price) {
      const product = allProducts.find(p => p.id === id);
      if (!product) return;

      const currentStock = Number(product.stock);
      const qtyInCart = cart[id] ? cart[id].qty : 0;

      if (qtyInCart >= currentStock) {
        alert(`Cannot add more! Only ${currentStock} units of ${name} are available in inventory.`);
        return;
      }

      if (cart[id]) {
        cart[id].qty++;
      } else {
        cart[id] = {
          id: id,
          name: name,
          price: price,
          qty: 1
        };
      }

      renderCart();
      renderProducts();
    }

    function resetFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('filterCategory').value = 'All';
      document.getElementById('filterBrand').value = 'All';
      document.getElementById('sortPrice').value = 'default';
      document.getElementById('filterStock').value = 'All';

      renderProducts();
    }

    function removeFromCart(id) {
      if (cart[id]) {
        delete cart[id];
      }
      renderCart();
      renderProducts();
    }

    function renderCart() {
      const orderItems = document.getElementById('orderItems');
      const keys = Object.keys(cart);

      cartCount = 0;
      cartTotal = 0;

      if (keys.length === 0) {
        orderItems.innerHTML = '<p>No items yet</p><p>Click a product to add.</p>';
        orderItems.classList.remove('has-items');
        updateSummary();
        return;
      }

      orderItems.classList.add('has-items');
      orderItems.innerHTML = '';

      let totalItemQuantity = 0;

      keys.forEach(key => {
        const item = cart[key];
        totalItemQuantity += item.qty;
        cartTotal += (item.price * item.qty);

        const itemEl = document.createElement('div');
        itemEl.classList.add('cart-item');
        itemEl.innerHTML = `
          <div class="cart-item-info" style="width: 100%;">
            <div class="name" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <span>${item.name}</span>
              <button class="remove-btn" onclick="removeFromCart(${item.id})" title="Remove Item">✕</button>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="item-price">Php${(item.price * item.qty).toFixed(2)}</div>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; color: #666;">Qty:</label>
                    <div style="display: flex; align-items: center; border: 1.5px solid #bcbcbc; border-radius: 6px; overflow: hidden;">
                    <button onclick="updateCartQty(${item.id}, ${item.qty - 1})" style="background: #e0e0e0; border: none; width: 28px; height: 28px; cursor: pointer; font-weight: bold; color: #333; font-size: 15px; transition: 0.2s;" onmouseover="this.style.background='#dcdcdc'" onmouseout="this.style.background='#e0e0e0'">−</button>
                    <input type="text" 
                         value="${item.qty}" 
                         style="width: 45px; height: 28px; border: none; outline: none; font-family: 'DM Mono', monospace; text-align: center; border-left: 1.5px solid #bcbcbc; border-right: 1.5px solid #bcbcbc;"
                         onchange="updateCartQty(${item.id}, this.value)">
                  <button onclick="updateCartQty(${item.id}, ${item.qty + 1})" style="background: #e0e0e0; border: none; width: 28px; height: 28px; cursor: pointer; font-weight: bold; color: #333; font-size: 15px; transition: 0.2s;" onmouseover="this.style.background='#dcdcdc'" onmouseout="this.style.background='#e0e0e0'">+</button>
                </div>
              </div>
            </div>
          </div>
        `;
        orderItems.appendChild(itemEl);
      });

      const itemLabel = keys.length === 1 ? 'Product' : 'Products';
      cartCount = `${keys.length} ${itemLabel} (${totalItemQuantity} pcs)`;

      updateSummary();

      updateSummary();
    }

    function updateCartQty(id, newQty) {
      const product = allProducts.find(p => p.id === id);
      if (!product) return;

      const currentStock = Number(product.stock);
      const parsedQty = parseInt(newQty);

      if (parsedQty <= 0 || isNaN(parsedQty)) {
        removeFromCart(id);
        return;
      }

      if (parsedQty > currentStock) {
        alert(`Cannot add more! Only ${currentStock} units of ${product.name} are available in inventory.`);
        cart[id].qty = currentStock;
      } else {
        cart[id].qty = parsedQty;
      }

      renderCart();
      renderProducts();
    }

    function updateSummary() {
      document.getElementById('subtotal').textContent = 'Php' + cartTotal.toFixed(2);
      document.getElementById('count').textContent = cartCount;
      applyDiscount();
    }

    function applyDiscount() {
      const raw = parseFloat(document.getElementById('discountInput').value) || 0;
      const type = document.getElementById('discountType').value;
      const resultEl = document.getElementById('discountResult');
      let deduction = 0;

      if (raw > 0 && cartTotal > 0) {
        if (type === '%') {
          deduction = Math.min(cartTotal, cartTotal * (raw / 100));
          resultEl.textContent = `- Php${deduction.toFixed(2)} (${raw}% off)`;
        } else {
          deduction = Math.min(cartTotal, raw);
          resultEl.textContent = `- Php${deduction.toFixed(2)} discount`;
        }
      } else {
        resultEl.textContent = '';
      }

      finalCalculatedTotal = Math.max(0, cartTotal - deduction);
      document.getElementById('total').textContent = 'Php' + finalCalculatedTotal.toFixed(2);
    }

    document.getElementById('discountInput').addEventListener('input', applyDiscount);
    document.getElementById('discountType').addEventListener('change', applyDiscount);

    function clearOrder() {
      cart = {};
      renderCart();
      renderProducts();
      document.getElementById('discountInput').value = '';
    }

    function openCheckoutModal() {
      if (Object.keys(cart).length === 0) {
        alert("Your order is empty!");
        return;
      }

      const modalList = document.getElementById('modalItemsList');
      modalList.innerHTML = '';

      Object.keys(cart).forEach(key => {
        const item = cart[key];
        const row = document.createElement('div');
        row.classList.add('modal-item-row');
        row.innerHTML = `
          <span>${item.name} ×${item.qty}</span>
          <span>Php${(item.price * item.qty).toFixed(2)}</span>
        `;
        modalList.appendChild(row);
      });

      document.getElementById('modalTotalAmount').textContent = 'Php' + finalCalculatedTotal.toFixed(2);
      document.getElementById('cashReceivedInput').value = '';
      document.getElementById('changeDisplay').textContent = '';

      const cashBtn = document.querySelector('.pay-method-btn');
      selectPaymentMethod(cashBtn, 'Cash');

      document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
      document.getElementById('checkoutModal').style.display = 'none';
    }

    function selectPaymentMethod(buttonElement, method) {
      selectedPayment = method;
      document.querySelectorAll('.pay-method-btn').forEach(btn => btn.classList.remove('selected'));
      buttonElement.classList.add('selected');

      const cashInputContainer = document.getElementById('cashInputContainer');
      if (method === 'Cash') {
        cashInputContainer.style.display = 'block';
      } else {
        cashInputContainer.style.display = 'none';
      }
    }

    document.getElementById('cashReceivedInput').addEventListener('input', e => {
      const cashAmt = parseFloat(e.target.value) || 0;
      const changeEl = document.getElementById('changeDisplay');

      if (cashAmt >= finalCalculatedTotal) {
        const calculatedChange = cashAmt - finalCalculatedTotal;
        changeEl.textContent = `Change: Php${calculatedChange.toFixed(2)}`;
        changeEl.style.color = '#2db84d';
      } else if (cashAmt > 0) {
        const balanceDue = finalCalculatedTotal - cashAmt;
        changeEl.textContent = `Short: Php${balanceDue.toFixed(2)}`;
        changeEl.style.color = '#ff5c5c';
      } else {
        changeEl.textContent = '';
      }
    });

    async function confirmSale() {
      let cashAmt = 0;
      let changeAmt = 0;

      if (selectedPayment === 'Cash') {
        cashAmt = parseFloat(document.getElementById('cashReceivedInput').value) || 0;
        if (cashAmt < finalCalculatedTotal) {
          alert("Insufficient cash amount received!");
          return;
        }
        changeAmt = cashAmt - finalCalculatedTotal;
      }

      try {
        const cartArray = Object.values(cart);

        const rawDiscount = parseFloat(document.getElementById('discountInput').value) || 0;
        const type = document.getElementById('discountType').value;
        let computedDeduction = 0;
        if (rawDiscount > 0 && cartTotal > 0) {
          computedDeduction = (type === '%') ? Math.min(cartTotal, cartTotal * (rawDiscount / 100)) : Math.min(cartTotal, rawDiscount);
        }

        const response = await fetch(
          '../Inventory_backend/api_checkout.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              cart: cartArray,
              payment_method: selectedPayment,
              discount_amount: computedDeduction,
              total_amount: finalCalculatedTotal,
              cash_received: cashAmt,
              change_amount: changeAmt
            })
          }
        );

        const result = await response.json();

        if (result.success && result.is_redirect) {
          window.location.href = result.checkout_url;
          return;
        }

        if (result.success) {

          closeCheckoutModal();

          openReceiptModal({

            order_no: result.order_no,

            date: new Date().toLocaleString(),

            payment: selectedPayment,

            discount: computedDeduction,

            total: finalCalculatedTotal,

            cash_received: cashAmt,

            change_amount: changeAmt,

            items: cartArray.map(item => ({
              name: item.name,
              quantity: item.qty
            }))

          });

          clearOrder();

          loadData();

        } else {
          alert(result.message || 'Checkout failed');
        }

      } catch (err) {
        console.error(err);
        alert('Server error during checkout');
      }
    }

    function toggleUserDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById("userDropdownMenu");
      dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }

    window.onclick = function() {
      const dropdown = document.getElementById("userDropdownMenu");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }

    function open3DViewer(productId) {
      const p = allProducts.find(x => x.id === productId);
      if (!p) return;

      if (!p.model_path || p.model_path === 'null' || p.model_path === '') {
        alert("A 3D model for " + p.name + " has not been uploaded yet.");
      }

      let detailsHTML = `
            <div style="font-size: 14px; color: #444; line-height: 1.6;">
                <strong>Price:</strong> Php${Number(p.price).toFixed(2)}<br>
                <strong>Brand:</strong> ${p.brand || 'N/A'}<br>
                <strong>Color:</strong> ${p.color || 'N/A'}<br>
                <strong>Type:</strong> ${p.type || 'N/A'}<br>
        `;

      if (p.capacity_size) detailsHTML += `<strong>Size/Capacity:</strong> ${p.capacity_size}<br>`;
      if (p.resolution) detailsHTML += `<strong>Resolution:</strong> ${p.resolution}<br>`;

      if (p.description) {
        detailsHTML += `<br><strong>Description:</strong><br>${p.description}`;
      }

      detailsHTML += `</div>`;

      document.getElementById('viewer-container').innerHTML = `
            <model-viewer src="${p.model_path}" auto-rotate camera-controls style="width: 100%; height: 100%;"></model-viewer>
        `;
      document.getElementById('modal-title').innerText = p.name;
      document.getElementById('modal-desc').innerHTML = detailsHTML;

      document.getElementById('model-modal').style.display = 'flex';
    }

    function close3DViewer() {
      document.getElementById('model-modal').style.display = 'none';

      document.getElementById('viewer-container').innerHTML = '';
    }

    loadData();
    window.onload = function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('payment') === 'success') {
        alert("E-Wallet Payment Successful! Sale Confirmed.");

        window.history.replaceState(null, null, window.location.pathname);
      }
    };

    function openReceiptModal(receiptData) {

      document.getElementById("rcptOrderNo").textContent = "#" + receiptData.order_no;
      document.getElementById("rcptCashier").textContent = cashierName;
      document.getElementById("rcptDate").textContent = receiptData.date;
      document.getElementById("rcptPayment").textContent = receiptData.payment;

      const discount = parseFloat(receiptData.discount || 0);

      document.getElementById("rcptDiscount").textContent =
        discount > 0 ? `- Php${discount.toFixed(2)}` : "None";

      document.getElementById("rcptTotal").textContent =
        `Php${parseFloat(receiptData.total).toFixed(2)}`;

      document.getElementById("rcptCashReceived").textContent =
        `Php${parseFloat(receiptData.cash_received).toFixed(2)}`;

      document.getElementById("rcptChange").textContent =
        `Php${parseFloat(receiptData.change_amount).toFixed(2)}`;

      const itemsBox = document.getElementById("rcptItemsBox");
      itemsBox.innerHTML = "";

      receiptData.items.forEach(item => {

        const row = document.createElement("div");

        row.className = "receipt-item-line";

        row.innerHTML = `<span>${item.name} × ${item.quantity}</span>`;

        itemsBox.appendChild(row);

      });

      document.getElementById("receiptModal").style.display = "flex";
    }

    function closeReceiptModal() {
      document.getElementById("receiptModal").style.display = "none";
    }
  </script>
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
</body>

</html>