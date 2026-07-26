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
  <title>Kinetix POS Terminal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    :root {
      --bg: #f0f1f4;
      --brand: #1B4EF5;
      --brand-hover: #153ec3;
      --text: #1a1c20;
      --text-muted: #3e3e3e;
      --border: #c2c2c2;
      --card-bg: #ffffff;
      --white: #ffffff;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: var(--font);
    }

    body {
      background: var(--bg);
      overflow: hidden;
      color: var(--text);
    }

    .container {
      display: flex;
      width: 100%;
      height: 100vh;
    }

    .left-panel {
      width: 66%;
      background: var(--bg);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      height: 56px;
      background: #181b2a;
      display: flex;
      align-items: center;
      padding: 0 20px;
      gap: 16px;
      color: white;
      flex-shrink: 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .topbar-admin {
      position: relative;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      background: transparent;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 600;
      color: #ffffff;
      transition: .2s;
    }

    .topbar-admin:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .profile-img {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      object-fit: cover;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      left: auto;
      top: 110%;
      background-color: #ffffff;
      min-width: 140px;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
      border-radius: 8px;
      z-index: 2100;
      overflow: hidden;
      border: 1px solid var(--border);
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
      font-size: 16px;
      font-weight: 700;
      flex: 1;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .brand-logo-icon {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: var(--brand);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .brand-logo-icon img {
      width: 22px;
      height: 22px;
      object-fit: contain;
    }

    .search-container {
      padding: 16px 16px 8px;
    }

    .search-box {
      width: 100%;
      height: 40px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      padding: 0 14px;
      font-size: 14px;
      outline: none;
      background: #ffffff;
      color: var(--text);
      transition: .2s;
    }

    .search-box:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(27, 78, 245, 0.15);
    }

    .categories {
      display: flex;
      gap: 8px;
      padding: 0 16px 12px;
      flex-wrap: wrap;
      flex-shrink: 0;
    }

    .filter-select {
      height: 40px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      padding: 0 12px;
      font-size: 13.5px;
      font-weight: 600;
      outline: none;
      background: white;
      cursor: pointer;
      color: var(--text);
    }

    .filter-select:focus {
      border-color: var(--brand);
    }

    .reset-filter-btn {
      height: 40px;
      border-radius: 8px;
      border: 1.5px solid #ff7070;
      padding: 0 16px;
      font-size: 13.5px;
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
      padding: 4px 16px 16px;
      scrollbar-gutter: stable;
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 14px;
    }

    .product-card {
      background: var(--card-bg);
      border-radius: 12px;
      border: 2px solid var(--border);
      padding: 12px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.25s ease;
      position: relative;
    }

    .product-card:hover {
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16);
    }

    .product-card.out-of-stock {
      opacity: .6;
    }

    .card-media-wrap {
      width: 100%;
      height: 110px;
      background: #f8fafc;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-bottom: 10px;
    }

    .product-card img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 6px;
      background-color: #c1c1c1;
    }

    model-viewer {
      width: 100%;
      height: 100%;
      background: #c1c1c1;
    }

    .product-name {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text);
      display: -webkit-box;
      line-clamp: 2;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      height: 36px;
      line-height: 1.35;
      margin-bottom: 2px;
    }

    .brand-name {
      font-size: 10px;
      color: white;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .price {
      font-family: 'DM Sans', monospace;
      font-size: 14px;
      font-weight: 600;
      color: var(--text);
      margin-top: 4px;
    }

    .stock {
      margin-top: 4px;
      font-size: 11px;
      font-weight: 700;
      display: inline-block;
    }

    .green {
      color: #10b981;
    }

    .orange {
      color: #f59e0b;
    }

    .red {
      color: #ef4444;
    }

    .no-results,
    .spinner-wrap {
      width: 100%;
      text-align: center;
      color: var(--text-muted);
      font-size: 14px;
      font-weight: 600;
      padding: 40px 0;
    }

    .btn-add {
      flex: 2;
      background: var(--brand);
      color: white;
      border: none;
      padding: 8px 10px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 12px;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(27, 78, 245, 0.15);
      transition: all 0.2s ease;
    }

    .btn-add:hover:not(:disabled) {
      background: var(--brand-hover);
    }

    .btn-add:active:not(:disabled) {
      transform: scale(0.95);
    }

    .btn-view {
      flex: 1;
      background: transparent;
      color: var(--text-muted);
      border: 1.5px solid var(--border);
      padding: 8px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 12px;
      transition: all 0.2s ease;
    }

    .btn-view:hover {
      background: rgba(0, 0, 0, 0.03);
      color: var(--text);
      border-color: var(--text-muted);
    }

    .btn-view:active {
      transform: scale(0.95);
    }

    .right-panel {
      width: 34%;
      background: var(--white);
      border-left: 1px solid var(--border);
      display: flex;
      flex-direction: column;
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .order-header h1 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
    }

    .count {
      height: 26px;
      background: var(--brand);
      color: white;
      border-radius: 20px;
      padding: 0 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11.5px;
      font-weight: 600;
      white-space: nowrap;
      box-shadow: 0 2px 6px rgba(27, 78, 245, 0.2);
    }

    .order-items {
      flex: 1;
      overflow-y: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: var(--text-muted);
      font-size: 14px;
      font-weight: 500;
      border-bottom: 1px solid var(--border);
      padding: 16px;
      gap: 10px;
      scrollbar-gutter: stable;
    }

    .order-items.has-items {
      justify-content: flex-start;
      padding: 16px;
    }

    .cart-item {
      width: 100%;
      background: var(--bg);
      border: 1px solid var(--border);
      padding: 12px;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .cart-item-info .name {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text);
    }

    .cart-item-info .item-price {
      font-family: 'DM Sans', monospace;
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text);
    }

    .qty-badge {
      font-family: 'DM Sans', monospace;
      font-size: 13px;
      font-weight: 600;
    }

    .remove-btn {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      border: none;
      width: 24px;
      height: 24px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 11px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.15s ease;
    }

    .remove-btn:hover {
      background: #ef4444;
      color: white;
    }

    .summary {
      padding: 16px;
      flex-shrink: 0;
      background: var(--white);
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
    }

    .summary-row span:last-child {
      font-family: 'DM Sans', monospace;
      font-weight: 600;
    }

    .discount-presets {
      margin-bottom: 14px;
    }

    .discount-preset-label {
      font-size: 10.5px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
    }

    .discount-preset-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .discount-preset-btn {
      background: white;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      padding: 8px 4px;
      font-size: 11px;
      font-weight: 600;
      color: var(--text);
      cursor: pointer;
      transition: all 0.2s ease;
      text-align: center;
      line-height: 1.35;
    }

    .discount-preset-btn:hover {
      border-color: var(--brand);
      color: var(--brand);
    }

    .discount-preset-btn.active {
      background: rgba(27, 78, 245, 0.08);
      border-color: var(--brand);
      color: var(--brand);
    }

    .discount-presets-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 12px 0;
    }

    .discount-row {
      display: flex;
      gap: 8px;
      align-items: center;
      margin-bottom: 14px;
    }

    .discount-input {
      width: 120px;
      height: 38px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      padding: 0 10px;
      font-size: 14px;
      outline: none;
      transition: .2s;
    }

    .discount-input:focus {
      border-color: var(--brand);
    }

    .discount-type-select {
      height: 38px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      padding: 0 8px;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      outline: none;
      background: white;
    }

    .discount-result {
      flex: 1;
      font-family: 'DM Sans', monospace;
      font-size: 13.5px;
      font-weight: 600;
      color: #ef4444;
      text-align: right;
      white-space: nowrap;
    }

    .checkout-btn {
      width: 100%;
      height: 46px;
      border: none;
      background: var(--brand);
      color: white;
      border-radius: 8px;
      font-size: 14.5px;
      font-weight: 700;
      cursor: pointer;
      margin-bottom: 10px;
      box-shadow: 0 4px 12px rgba(27, 78, 245, 0.15);
      transition: all 0.2s ease;
    }

    .checkout-btn:hover {
      background: var(--brand-hover);
      box-shadow: 0 6px 16px rgba(27, 78, 245, 0.25);
    }

    .clear-btn {
      width: 100%;
      height: 42px;
      border: 1.5px solid #ff7070;
      background: white;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      color: #ff7070;
      transition: all 0.2s ease;
    }

    .clear-btn:hover {
      background: #ff2c2c;
      border-color: #ff2c2c;
      color: white;
    }

    .item-discount-toggle {
      background: none;
      border: 1.5px solid var(--border);
      border-radius: 5px;
      padding: 2px 6px;
      font-size: 10px;
      font-weight: 700;
      color: var(--text-muted);
      cursor: pointer;
      transition: 0.2s;
    }

    .item-discount-toggle:hover {
      border-color: var(--brand);
      color: var(--brand);
    }

    .item-discount-toggle.has-discount {
      background: rgba(27, 78, 245, 0.08);
      border-color: var(--brand);
      color: var(--brand);
    }

    /* Modal Styling */
    .modal-backdrop,
    .receipt-modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      height: 100dvh;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2500;
      overflow-y: auto;
    }

    .modal-card {
      background: white;
      width: 440px;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      border: 1px solid var(--border);
    }

    .modal-card h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 20px;
      color: var(--text);
    }

    .modal-items-list {
      max-height: 160px;
      overflow-y: auto;
      margin-bottom: 12px;
    }

    .modal-item-row {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      color: var(--text);
      margin-bottom: 8px;
    }

    .modal-item-row span:last-child {
      font-family: 'DM Sans', monospace;
    }

    .modal-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 12px 0;
    }

    .modal-total-row {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 16px;
    }

    .modal-total-row span:last-child {
      font-family: 'DM Sans', monospace;
    }

    .modal-label {
      font-size: 12.5px;
      color: var(--text-muted);
      font-weight: 600;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .payment-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 18px;
    }

    .pay-method-btn {
      background: white;
      border: 1.5px solid var(--border);
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: .2s;
      text-align: center;
    }

    .pay-method-btn:hover {
      border-color: var(--brand);
      color: var(--brand);
    }

    .pay-method-btn.selected {
      background: rgba(27, 78, 245, 0.08);
      border: 2px solid var(--brand);
      color: var(--brand);
    }

    .cash-input-wrap {
      margin-bottom: 20px;
    }

    .cash-received-field {
      width: 100%;
      height: 42px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      padding: 0 14px;
      font-size: 15px;
      font-family: 'DM Sans', monospace;
      outline: none;
    }

    .cash-received-field:focus {
      border-color: var(--brand);
    }

    .change-display {
      font-family: 'DM Sans', monospace;
      font-size: 14px;
      font-weight: 600;
      color: #10b981;
      margin-top: 6px;
      text-align: right;
    }

    .modal-actions {
      display: grid;
      grid-template-columns: 1fr 1.3fr;
      gap: 12px;
    }

    .modal-cancel-btn {
      height: 44px;
      background: #f2f2f2;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      color: var(--text-muted);
      cursor: pointer;
    }

    .modal-cancel-btn:hover {
      background: #e6e6e6;
    }

    .modal-confirm-btn {
      height: 44px;
      background: #10b981;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      color: white;
      cursor: pointer;
    }

    .modal-confirm-btn:hover {
      background: #0d9488;
    }

    .viewer-modal-card {
      width: 760px;
      max-width: 90%;
      display: flex;
      gap: 24px;
      padding: 24px;
      position: relative;
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid var(--border);
    }

    .modal-close-x {
      position: absolute;
      top: 14px;
      right: 18px;
      background: none;
      border: none;
      font-size: 24px;
      font-weight: 400;
      color: var(--text-muted);
      cursor: pointer;
      line-height: 1;
      transition: color 0.15s;
      z-index: 10;
      padding: 10px;
      margin: -10px;
    }

    .modal-close-x:hover {
      color: var(--text);
    }

    #viewer-container {
      flex: 1.1;
      height: 340px;
      background: #f8fafc;
      border-radius: 12px;
      border: 1px solid var(--border);
      overflow: hidden;
    }

    .viewer-modal-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .viewer-modal-details h2 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 6px;
      padding-right: 20px;
    }

    .viewer-modal-close-btn {
      width: 100%;
      height: 46px;
      background: #181b2a;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.15s;
      flex-shrink: 0;
      margin-top: 8px;
    }

    .viewer-modal-close-btn:hover {
      background: #10121d;
    }

    .modal-details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 14px;
      margin-bottom: 14px;
    }

    .detail-item {
      font-size: 13px;
      color: var(--text);
      line-height: 1.4;
      background: #f8fafc;
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .detail-item strong {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }

    .detail-item span {
      font-weight: 600;
    }

    .detail-item .price-val {
      color: var(--brand);
      font-family: 'DM Sans', monospace;
      font-size: 14px;
    }

    .modal-desc-section {
      font-size: 13px;
      color: var(--text);
      line-height: 1.5;
      border-top: 1px dashed var(--border);
      padding-top: 12px;
      margin-top: 12px;
    }

    .modal-desc-section strong {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      display: block;
      margin-bottom: 4px;
    }

    .modal-desc-section p {
      color: #334155;
    }

    @media (max-width: 768px) {
      #model-modal {
        align-items: flex-start;
        padding-top: 12px;
        padding-bottom: 12px;
      }

      .viewer-modal-card {
        position: relative;
        width: calc(100vw - 24px);
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 16px;
        gap: 14px;
      }

      #viewer-container {
        height: 220px;
        flex-shrink: 0;
      }

      .viewer-modal-details {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
      }

      .viewer-modal-close-btn {
        height: 48px;
        font-size: 15px;
        border-radius: 10px;
        margin-top: 10px;
        flex-shrink: 0;
      }
    }

    .modal-backdrop {
      padding: 12px;
      overflow: hidden;
    }

    .receipt-card {
      background: white;
      width: 380px;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      border: 1px solid var(--border);
    }

    .receipt-card h2 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 4px;
      text-align: center;
    }

    .receipt-subtitle {
      font-size: 12px;
      color: var(--text-muted);
      text-align: center;
      margin-bottom: 20px;
    }

    .receipt-meta-row {
      display: flex;
      justify-content: space-between;
      font-size: 12.5px;
      color: var(--text);
      margin-bottom: 6px;
    }

    .receipt-divider {
      border: none;
      border-top: 1px dashed var(--border);
      margin: 14px 0;
    }

    .receipt-items-box {
      margin: 10px 0;
      max-height: 150px;
      overflow-y: auto;
    }

    .receipt-item-line {
      display: flex;
      justify-content: space-between;
      font-size: 13.5px;
      margin-bottom: 8px;
      color: var(--text);
    }

    .receipt-item-line span:last-child {
      font-family: 'DM Sans', monospace;
    }

    .receipt-total-line {
      display: flex;
      justify-content: space-between;
      font-size: 16.5px;
      font-weight: 700;
      color: var(--text);
      margin-top: 10px;
    }

    .receipt-total-line span:last-child {
      font-family: 'DM Sans', monospace;
    }

    .receipt-close-btn {
      width: 100%;
      height: 42px;
      background: #181b2a;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 20px;
      transition: 0.2s;
    }

    .receipt-close-btn:hover {
      background: #10121d;
    }

    .item-discount-row {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 6px;
      padding-top: 6px;
      border-top: 1px dashed var(--border);
    }

    .item-discount-input {
      width: 65px;
      height: 28px;
      border-radius: 6px;
      border: 1.5px solid var(--border);
      padding: 0 6px;
      font-size: 12px;
      outline: none;
    }

    .item-discount-input:focus {
      border-color: var(--brand);
    }

    .item-discount-type {
      height: 28px;
      border-radius: 6px;
      border: 1.5px solid var(--border);
      padding: 0 4px;
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
      outline: none;
      background: white;
    }

    .item-discount-apply {
      height: 28px;
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 0 10px;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
    }

    .item-discount-apply:hover {
      background: var(--brand-hover);
    }

    .item-discount-clear {
      height: 28px;
      background: #ff5c5c;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 0 8px;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
    }

    .item-discount-clear:hover {
      background: #e04444;
    }

    .item-discount-badge {
      font-size: 10.5px;
      font-weight: 600;
      color: #ff4b4b;
      font-family: 'DM Sans', monospace;
    }

    .mobile-tabs-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 56px;
      background: #181b2a;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      z-index: 2000;
      grid-template-columns: 1fr 1fr;
    }

    .mobile-tab-btn {
      background: none;
      border: none;
      color: #a4b1cd;
      font-size: 12px;
      font-weight: 600;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 3px;
      cursor: pointer;
      transition: color 0.15s ease;
    }

    .mobile-tab-btn i {
      font-size: 18px;
    }

    .mobile-tab-btn.active {
      color: #ffffff;
      background: var(--brand);
    }

    @media (max-width: 900px) {
      .container {
        height: calc(100dvh - 56px);
      }

      .left-panel,
      .right-panel {
        width: 100% !important;
        height: 100% !important;
        display: none !important;
      }

      .left-panel.active,
      .right-panel.active {
        display: flex !important;
      }

      /* Fix for filter dropdowns overlapping product cards */
      .categories {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 0 14px 12px;
      }

      .categories .filter-select,
      .categories .reset-filter-btn {
        width: 100%;
      }

      /* Stretches reset button full width on mobile grid */
      .reset-filter-btn {
        grid-column: span 2;
      }

      .mobile-tabs-nav {
        display: grid;
      }

      .products {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
      }

      .product-card {
        padding: 10px;
      }

      .card-media-wrap {
        height: 95px;
      }
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="left-panel active">

      <div class="topbar">
        <div class="title">
          <div class="brand-logo-icon">
            <img src="../Images/logo.svg" alt="Logo">
          </div>
          <div class="brand-text-wrapper" style="display: flex; flex-direction: column; gap: 1px; line-height: 1.15; color: #ffffff;">
            <span class="brand-name" style="font-size: 15px; font-weight: 700; letter-spacing: -0.01em;">Kinetix</span>
            <span class="brand-sub" style="font-size: 9px; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em;">Point of Sale System</span>
          </div>
        </div>

        <div class="topbar-admin" onclick="toggleUserDropdown(event)">
          <img src="../Images/profile.png" alt="Profile" class="profile-img">
          <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
          <span style="font-size: 8px; opacity: 0.8; margin-left: 2px;">▼</span>

          <div id="userDropdownMenu" class="dropdown-menu">
            <a href="logout.php">Logout</a>
          </div>
        </div>
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

        <div class="discount-row" style="margin-top: 8px; display: flex; align-items: center; gap: 6px;">
          <input type="text" class="discount-input" id="promoCodeInput" placeholder="Enter Promo Code" style="flex: 1; text-transform: uppercase;">
          <button class="discount-preset-btn" onclick="applyPromoCode()" style="padding: 8px 12px; height: 36px; line-height: 20px; font-size: 12px; white-space: nowrap; border-radius: 6px; margin: 0; background: #1B4EF5; color: white;">Apply</button>
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

  <div class="mobile-tabs-nav">
    <button class="mobile-tab-btn active" onclick="switchMobileTab('products')">
      <i class="fa-solid fa-tags"></i>
      <span>Products</span>
    </button>
    <button class="mobile-tab-btn" onclick="switchMobileTab('cart')">
      <i class="fa-solid fa-cart-shopping"></i>
      <span>Cart (<span id="mobileCartCount">0</span>)</span>
    </button>
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

      <div class="modal-label">Customer Email Address (Optional):</div>
      <div style="margin-bottom: 18px;">
        <input type="email" class="cash-received-field" id="customerEmailInput" placeholder="customer@email.com" style="font-family: inherit;">
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

  <div id="model-modal" class="modal-backdrop" style="z-index: 2600;">
    <div class="modal-card viewer-modal-card">
      <button class="modal-close-x" onclick="close3DViewer()" title="Close">&times;</button>
      <div id="viewer-container"></div>

      <div class="viewer-modal-details">
        <div class="viewer-modal-info">
          <h2 id="modal-title">Product Title</h2>
          <div id="modal-desc">Product description goes here...</div>
        </div>

        <button class="viewer-modal-close-btn" onclick="close3DViewer()">Close</button>
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
        <span id="rcptCashReceived" style="font-family: 'DM Sans', monospace; font-weight: 500; color: #444;">Php0.00</span>
      </div>
      <div class="receipt-meta-row">
        <span>Change Given:</span>
        <span id="rcptChange" style="font-family: 'DM Sans', monospace; font-weight: 500; color: #444;">Php0.00</span>
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
    let selectedDiscountPreset = null;

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
              <div class="card-media-wrap">
                <img src="${imgUrl}" alt="${p.name}" onerror="this.src='${DEFAULT_IMG}'">
              </div>
              <div class="product-name">${p.name}</div>
              <div class="brand-name">${p.brand || 'No Brand'}</div>
              <div class="price">Php${price.toFixed(2)}</div>
              <div class="stock ${stockClass(displayStock)}">${stockLabel(displayStock)}</div>
            </div>
            
            <div style="display: flex; gap: 6px; margin-top: 12px;">
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

        const lineTotal = item.price * item.qty;
        let itemDeduction = 0;
        if (item.itemDiscount && item.itemDiscount > 0) {
          if (item.itemDiscountType === '%') {
            itemDeduction = Math.min(lineTotal, lineTotal * (item.itemDiscount / 100));
          } else {
            itemDeduction = Math.min(lineTotal, item.itemDiscount);
          }
        }
        const discountedLineTotal = lineTotal - itemDeduction;
        cartTotal += discountedLineTotal;

        const hasDiscount = item.itemDiscount && item.itemDiscount > 0;
        const toggleClass = hasDiscount ? 'item-discount-toggle has-discount' : 'item-discount-toggle';
        const toggleLabel = hasDiscount ? '% ✓' : '%';

        let priceHTML = `<div class="item-price">Php${discountedLineTotal.toFixed(2)}</div>`;
        if (hasDiscount) {
          priceHTML = `
            <div>
              <div class="item-price">Php${discountedLineTotal.toFixed(2)}</div>
              <div class="item-discount-badge">-Php${itemDeduction.toFixed(2)} (${item.itemDiscount}${item.itemDiscountType === '%' ? '%' : 'php'} off)</div>
            </div>`;
        }

        let discountRowHTML = '';
        if (item.showDiscountInput) {
          if (hasDiscount) {
            discountRowHTML = `
              <div class="item-discount-row">
                <span style="font-size: 11px; color: #888; font-weight: 600;">Discount: ${item.itemDiscount}${item.itemDiscountType === '%' ? '%' : ' Php'}</span>
                <button class="item-discount-clear" onclick="clearItemDiscount(${item.id})">Remove</button>
              </div>`;
          } else {
            discountRowHTML = `
              <div class="item-discount-row">
                <input type="number" class="item-discount-input" id="itemDiscInput_${item.id}" placeholder="0" min="0">
                <select class="item-discount-type" id="itemDiscType_${item.id}">
                  <option value="%">%</option>
                  <option value="Php">Php</option>
                </select>
                <button class="item-discount-apply" onclick="applyItemDiscount(${item.id})">Apply</button>
              </div>`;
          }
        }

        const itemEl = document.createElement('div');
        itemEl.classList.add('cart-item');
        itemEl.innerHTML = `
          <div class="cart-item-info" style="width: 100%;">
            <div class="name" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <span>${item.name}</span>
              <div style="display: flex; gap: 5px;">
                <button class="${toggleClass}" onclick="toggleItemDiscount(${item.id})" title="Item Discount">${toggleLabel}</button>
                <button class="remove-btn" onclick="removeFromCart(${item.id})" title="Remove Item">✕</button>
              </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                ${priceHTML}
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; color: #666;">Qty:</label>
                    <div style="display: flex; align-items: center; border: 1.5px solid #bcbcbc; border-radius: 6px; overflow: hidden;">
                    <button onclick="updateCartQty(${item.id}, ${item.qty - 1})" style="background: #e0e0e0; border: none; width: 28px; height: 28px; cursor: pointer; font-weight: bold; color: #333; font-size: 15px; transition: 0.2s;" onmouseover="this.style.background='#dcdcdc'" onmouseout="this.style.background='#e0e0e0'">−</button>
                    <input type="text" 
                         value="${item.qty}" 
                         style="width: 45px; height: 28px; border: none; outline: none; font-family: 'DM Sans', monospace; text-align: center; border-left: 1.5px solid #bcbcbc; border-right: 1.5px solid #bcbcbc;"
                         onchange="updateCartQty(${item.id}, this.value)">
                  <button onclick="updateCartQty(${item.id}, ${item.qty + 1})" style="background: #e0e0e0; border: none; width: 28px; height: 28px; cursor: pointer; font-weight: bold; color: #333; font-size: 15px; transition: 0.2s;" onmouseover="this.style.background='#dcdcdc'" onmouseout="this.style.background='#e0e0e0'">+</button>
                </div>
              </div>
            </div>
            ${discountRowHTML}
          </div>
        `;
        orderItems.appendChild(itemEl);
      });

      const itemLabel = keys.length === 1 ? 'Product' : 'Products';
      cartCount = `${keys.length} ${itemLabel} (${totalItemQuantity} pcs)`;

      const mobileCartCountEl = document.getElementById('mobileCartCount');
      if (mobileCartCountEl) {
        mobileCartCountEl.textContent = totalItemQuantity;
      }

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

    document.getElementById('discountInput').addEventListener('input', function() {
      clearPresetSelection();
      applyDiscount();
    });
    document.getElementById('discountType').addEventListener('change', function() {
      clearPresetSelection();
      applyDiscount();
    });

    function selectPresetDiscount(btn) {
      const name = btn.dataset.name;
      const percent = parseFloat(btn.dataset.percent);

      if (selectedDiscountPreset === name) {
        clearPresetSelection();
        document.getElementById('discountInput').value = '';
        applyDiscount();
        return;
      }

      document.querySelectorAll('.discount-preset-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedDiscountPreset = name;

      document.getElementById('discountInput').value = percent;
      document.getElementById('discountType').value = '%';
      applyDiscount();
    }

    function toggleItemDiscount(id) {
      if (!cart[id]) return;
      cart[id].showDiscountInput = !cart[id].showDiscountInput;
      renderCart();
    }

    function applyItemDiscount(id) {
      if (!cart[id]) return;
      const val = parseFloat(document.getElementById('itemDiscInput_' + id).value) || 0;
      const type = document.getElementById('itemDiscType_' + id).value;

      if (val <= 0) {
        alert('Please enter a valid discount amount.');
        return;
      }

      if (type === '%' && val > 100) {
        alert('Percentage discount cannot exceed 100%.');
        return;
      }

      cart[id].itemDiscount = val;
      cart[id].itemDiscountType = type;
      renderCart();
    }

    function clearItemDiscount(id) {
      if (!cart[id]) return;
      cart[id].itemDiscount = 0;
      cart[id].itemDiscountType = null;
      cart[id].showDiscountInput = false;
      renderCart();
    }

    function clearPresetSelection() {
      selectedDiscountPreset = null;
      document.querySelectorAll('.discount-preset-btn').forEach(b => b.classList.remove('active'));
      const promoInput = document.getElementById('promoCodeInput');
      if (promoInput) promoInput.value = '';
    }

    async function applyPromoCode() {
      const code = document.getElementById('promoCodeInput').value.trim();
      if (!code) {
        alert('Please enter a promo code.');
        return;
      }

      try {
        const response = await fetch('../Inventory_backend/api_validate_promo.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ code: code })
        });

        const data = await response.json();
        if (data.success) {
          // Clear active visual presets
          document.querySelectorAll('.discount-preset-btn').forEach(b => b.classList.remove('active'));
          
          document.getElementById('discountInput').value = data.discount_value;
          document.getElementById('discountType').value = (data.discount_type === 'percent') ? '%' : 'Php';
          
          selectedDiscountPreset = "Promo: " + data.code;
          
          alert(`Promo code applied: ${data.code} (${data.discount_value}${data.discount_type === 'percent' ? '%' : ' Php'} Off)`);
          
          applyDiscount();
        } else {
          alert(data.error || 'Failed to apply promo code.');
        }
      } catch (err) {
        alert('Error validating promo code.');
      }
    }

    function clearOrder() {
      cart = {};
      renderCart();
      renderProducts();
      document.getElementById('discountInput').value = '';
      clearPresetSelection();
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
      const customerEmail = document.getElementById('customerEmailInput').value.trim();
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
        const cartArray = Object.values(cart).map(item => {
          const obj = {
            id: item.id,
            name: item.name,
            price: item.price,
            qty: item.qty
          };
          if (item.itemDiscount && item.itemDiscount > 0) {
            obj.itemDiscount = item.itemDiscount;
            obj.itemDiscountType = item.itemDiscountType;
          }
          return obj;
        });

        const rawDiscount = parseFloat(document.getElementById('discountInput').value) || 0;
        const type = document.getElementById('discountType').value;
        let computedDeduction = 0;
        if (rawDiscount > 0 && cartTotal > 0) {
          computedDeduction = (type === '%') ? Math.min(cartTotal, cartTotal * (rawDiscount / 100)) : Math.min(cartTotal, rawDiscount);
        }

        const discountTypeName = selectedDiscountPreset || (computedDeduction > 0 ? 'Custom' : null);

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
              discount_type: discountTypeName,
              total_amount: finalCalculatedTotal,
              cash_received: cashAmt,
              change_amount: changeAmt,
              email: customerEmail
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

      const modelPath =
        (!p.model_path || p.model_path === 'null' || p.model_path === '') ?
        '../Models/default.glb' :
        p.model_path;

      let detailsHTML = `
        <div class="modal-details-grid">
          <div class="detail-item"><strong>Price</strong><span class="price-val">Php${Number(p.price).toFixed(2)}</span></div>
          <div class="detail-item"><strong>Brand</strong><span>${p.brand || 'N/A'}</span></div>
          <div class="detail-item"><strong>Color</strong><span>${p.color || 'N/A'}</span></div>
          <div class="detail-item"><strong>Type</strong><span>${p.type || 'N/A'}</span></div>
      `;

      if (p.capacity_size) detailsHTML += `<div class="detail-item"><strong>Size</strong><span>${p.capacity_size}</span></div>`;
      if (p.resolution) detailsHTML += `<div class="detail-item"><strong>Resolution</strong><span>${p.resolution}</span></div>`;
      detailsHTML += `</div>`;

      if (p.description) {
        detailsHTML += `
          <div class="modal-desc-section">
            <strong>Description</strong>
            <p>${p.description}</p>
          </div>
        `;
      }

      document.getElementById('viewer-container').innerHTML = `
<model-viewer
    src="${modelPath}"
    auto-rotate
    camera-controls
    touch-action="pan-y"
    style="
        display:block;
        width:100%;
        height:100%;
        min-height:300px;
        background:#f8fafc;
    ">
</model-viewer>
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

    function switchMobileTab(tab) {
      const leftPanel = document.querySelector('.left-panel');
      const rightPanel = document.querySelector('.right-panel');
      const tabBtns = document.querySelectorAll('.mobile-tab-btn');

      if (tab === 'products') {
        leftPanel.classList.add('active');
        rightPanel.classList.remove('active');
        tabBtns[0].classList.add('active');
        tabBtns[1].classList.remove('active');
      } else {
        rightPanel.classList.add('active');
        leftPanel.classList.remove('active');
        tabBtns[1].classList.add('active');
        tabBtns[0].classList.remove('active');
      }
    }
  </script>
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
</body>

</html>