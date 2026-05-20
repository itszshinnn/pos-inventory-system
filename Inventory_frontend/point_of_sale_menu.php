<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Inventory_frontend/login_signup.php");
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

    /* LEFT PANEL */
    .left-panel {
      width: 67%;
      background: #f5f5f5;
      border-right: 1px solid #cfcfcf;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* TOPBAR */
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

    /* User Dropdown Profile Container */
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

    /* Floating Dropdown Drawer */
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

    .history-btn {
      background: #efefef;
      border: none;
      padding: 8px 18px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    /* SEARCH */
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

    /* CATEGORY */
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

    /* PRODUCTS */
    .products-wrapper {
      flex: 1;
      overflow-y: auto;
      padding: 10px 14px 14px;
    }

    .products {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
    }

    .product-card {
      width: 155px;
      background: #d9d9d9;
      border-radius: 16px;
      padding: 14px;
      text-align: center;
      cursor: pointer;
      transition: .2s;
    }

    .product-card:hover {
      transform: translateY(-4px);
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

    /* Loading spinner */
    .spinner-wrap {
      width: 100%;
      text-align: center;
      padding: 40px 0;
      color: #aaa;
      font-size: 14px;
      font-weight: 600;
    }

    /* RIGHT PANEL */
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
      width: 32px;
      height: 32px;
      background: #5470ff;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'DM Mono', monospace;
      font-size: 14px;
      font-weight: 500;
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
      font-weight: 600;
      border-bottom: 1px solid #c7c7c7;
      padding: 10px 0;
    }

    .order-items.has-items {
      justify-content: flex-start;
      padding: 12px 10px;
    }

    /* CART ITEM */
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

    /* SUMMARY */
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
      background: #00000062;
      color: white;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-bottom: 10px;
      transition: .2s;
    }

    .checkout-btn:hover {
      background: #2e2e2e;
      color: white;
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

    /* ── CHECKOUT MODAL SYSTEM ── */
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
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
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
        <button class="history-btn">History</button>
      </div>

      <div class="search-container">
        <input type="text" class="search-box" id="searchInput" placeholder="Search...">
      </div>

      <div class="categories" id="categories">
        <button class="category-btn active" data-cat="All">All</button>
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
        <p>No items yet</p>
        <p>Click a product to add.</p>
      </div>

      <div class="summary">

        <div class="summary-row">
          <span>Subtotal</span>
          <span id="subtotal">₱0.00</span>
        </div>

        <div class="discount-row">
          <input type="number" class="discount-input" id="discountInput" placeholder="Discount" min="0">
          <select class="discount-type-select" id="discountType">
            <option value="%">%</option>
            <option value="₱">₱</option>
          </select>
          <div class="discount-result" id="discountResult"></div>
        </div>

        <div class="summary-row">
          <span>Total</span>
          <span id="total">₱0.00</span>
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
        <span id="modalTotalAmount">₱0.00</span>
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

  <script>
    let allProducts = []; // fetched from API
    let activeCategory = 'All';
    let searchQuery = '';
    
    // Cart tracking state dictionary
    let cart = {}; 
    
    let cartTotal = 0;
    let finalCalculatedTotal = 0;
    let cartCount = 0;
    let selectedPayment = 'Cash';

    // ── Product image map ──
    const productImages = {
      'Wireless Mouse': '../Images/wireless_mouse.png',
      'Wireless Keyboard': '../Images/wireless_keyboard.png',
      'Earphones': '../Images/earphones.png',
      'Wireless Earbuds': '../Images/earbuds.png',
      'USB Flash Drive': '../Images/flash_drive.png',
      'Speaker': '../Images/speaker.png',
      'Micro SD Card': '../Images/micro_sd_card.png',
    };

    const DEFAULT_IMG = 'https://cdn-icons-png.flaticon.com/512/2721/2721297.png';

    // ── Fetch products & categories from API ──────────────────────────────
    async function loadData() {
      try {
        const [pRes, cRes] = await Promise.all([
          fetch('../Inventory_backend/api_products.php'),
          fetch('../Inventory_backend/api_categories.php')
        ]);

        allProducts = await pRes.json();
        const allCategories = await cRes.json();

        renderCategories(allCategories);
        renderProducts();
      } catch (err) {
        document.getElementById('products').innerHTML =
          '<div class="no-results">Failed to load products. Please check your connection.</div>';
      }
    }

    // ── Render category buttons dynamically ───────────────────────────────
    function renderCategories(categories) {
      const container = document.getElementById('categories');
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

    // ── Helpers ───────────────────────────────────────────────────────────
    function stockLabel(s) {
      const n = Number(s);
      return n === 0 ? 'No stocks left' : n + ' stocks left';
    }

    function stockClass(s) {
      const n = Number(s);
      return n === 0 ? 'red' : n <= 3 ? 'orange' : 'green';
    }

    // ── Render product cards dynamically ───────────────────────────────────
    function renderProducts() {
      const container = document.getElementById('products');

      const filtered = allProducts.filter(p => {
        const matchCat = activeCategory === 'All' || p.category === activeCategory;
        const matchSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase());
        return matchCat && matchSearch;
      });

      if (!filtered.length) {
        container.innerHTML = '<div class="no-results">No products found.</div>';
        return;
      }

      container.innerHTML = filtered.map(p => {
        const stock = Number(p.stock);
        const price = Number(p.price);
        const outClass = stock === 0 ? ' out-of-stock' : '';
        const onclick = stock > 0 ?
          `addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${price})` :
          '';

        let imgUrl = DEFAULT_IMG;
        if (productImages[p.name]) {
            imgUrl = productImages[p.name];
        } else if (p.image && p.image !== 'default_product.png') {
            imgUrl = `../Images/${p.image}`;
        }

        return `
          <div class="product-card${outClass}" onclick="${onclick}">
            <img src="${imgUrl}" alt="${p.name}" onerror="this.src='${DEFAULT_IMG}'">
            <div class="product-name">${p.name}</div>
            <div class="price">₱${price.toFixed(2)}</div>
            <div class="stock ${stockClass(stock)}">${stockLabel(stock)}</div>
          </div>
        `;
      }).join('');
    }

    // ── Search ────────────────────────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', e => {
      searchQuery = e.target.value;
      renderProducts();
    });

    // ── Cart Core logic with Multipliers (x) ──────────────────────────────
    function addToCart(id, name, price) {
      if (cart[id]) {
        cart[id].qty++;
      } else {
        cart[id] = { id: id, name: name, price: price, qty: 1 };
      }
      renderCart();
    }

    function removeFromCart(id) {
      if (cart[id]) {
        cart[id].qty--;
        if (cart[id].qty <= 0) {
          delete cart[id];
        }
      }
      renderCart();
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

      keys.forEach(key => {
        const item = cart[key];
        cartCount += item.qty;
        cartTotal += (item.price * item.qty);

        const itemEl = document.createElement('div');
        itemEl.classList.add('cart-item');
        itemEl.innerHTML = `
          <div class="cart-item-info">
            <div class="name">${item.name} <span class="qty-badge">x${item.qty}</span></div>
            <div class="item-price">₱${(item.price * item.qty).toFixed(2)}</div>
          </div>
          <div class="cart-item-controls">
            <button class="remove-btn" onclick="removeFromCart(${item.id})">✕</button>
          </div>
        `;
        orderItems.appendChild(itemEl);
      });

      updateSummary();
    }

    function updateSummary() {
      document.getElementById('subtotal').textContent = '₱' + cartTotal.toFixed(2);
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
          resultEl.textContent = `- ₱${deduction.toFixed(2)} (${raw}% off)`;
        } else {
          deduction = Math.min(cartTotal, raw);
          resultEl.textContent = `- ₱${deduction.toFixed(2)} discount`;
        }
      } else {
        resultEl.textContent = '';
      }

      finalCalculatedTotal = Math.max(0, cartTotal - deduction);
      document.getElementById('total').textContent = '₱' + finalCalculatedTotal.toFixed(2);
    }

    document.getElementById('discountInput').addEventListener('input', applyDiscount);
    document.getElementById('discountType').addEventListener('change', applyDiscount);

    function clearOrder() {
      cart = {};
      renderCart();
      document.getElementById('discountInput').value = '';
    }

    // ── CHECKOUT MODAL INTERACTIVE SYSTEM ──────────────────────────────────
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
          <span>₱${(item.price * item.qty).toFixed(2)}</span>
        `;
        modalList.appendChild(row);
      });
      
      document.getElementById('modalTotalAmount').textContent = '₱' + finalCalculatedTotal.toFixed(2);
      document.getElementById('cashReceivedInput').value = '';
      document.getElementById('changeDisplay').textContent = '';
      
      // Default set back to Cash choice
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

    // Dynamic balance change calculation engine
    document.getElementById('cashReceivedInput').addEventListener('input', e => {
      const cashAmt = parseFloat(e.target.value) || 0;
      const changeEl = document.getElementById('changeDisplay');
      
      if(cashAmt >= finalCalculatedTotal) {
        const calculatedChange = cashAmt - finalCalculatedTotal;
        changeEl.textContent = `Change: ₱${calculatedChange.toFixed(2)}`;
        changeEl.style.color = '#2db84d';
      } else if (cashAmt > 0) {
        const balanceDue = finalCalculatedTotal - cashAmt;
        changeEl.textContent = `Short: ₱${balanceDue.toFixed(2)}`;
        changeEl.style.color = '#ff5c5c';
      } else {
        changeEl.textContent = '';
      }
    });

    async function confirmSale() {

  if (selectedPayment === 'Cash') {

    const cashAmt = parseFloat(
      document.getElementById('cashReceivedInput').value
    ) || 0;

    if (cashAmt < finalCalculatedTotal) {
      alert("Insufficient cash amount received!");
      return;
    }
  }

  try {

    const cartArray = Object.values(cart);

    const response = await fetch(
      '../Inventory_backend/api_checkout.php',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          cart: cartArray
        })
      }
    );

    const result = await response.json();

    if (result.success) {

      alert(
        `Sale Confirmed!\nPaid via: ${selectedPayment}\nTotal: ₱${finalCalculatedTotal.toFixed(2)}`
      );

      closeCheckoutModal();
      clearOrder();

      // Reload updated stocks
      loadData();

    } else {

      alert(result.message || 'Checkout failed');

    }

      } catch (err) {

        console.error(err);
        alert('Server error during checkout');

        }
      } 

    /* DROPDOWN TOGGLE ENGINE */
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

    // ── Boot ──────────────────────────────────────────────────────────────
    loadData();
  </script>
</body>

</html>