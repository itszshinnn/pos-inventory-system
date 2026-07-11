<?php
require_once __DIR__ . '/../Database/Database.php';
$alertDb = new Database();
$alertPdo = $alertDb->getConnection();

$alertStmt = $alertPdo->query("
    SELECT p.id, p.name, p.stock 
    FROM products p
    WHERE p.stock <= 3 
    AND p.id NOT IN (
        SELECT poi.product_id 
        FROM po_items poi
        JOIN purchase_orders po ON poi.po_id = po.id
        WHERE po.status = 'Pending'
    )
    ORDER BY p.stock ASC
");
$alertLowStocks = $alertStmt->fetchAll(PDO::FETCH_ASSOC);
$alertLowStockCount = count($alertLowStocks);
?>

<style>
    .stock-alert-overlay {
        position: fixed;
        inset: 0;
        display: none;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.35);
        z-index: 9999;
    }

    .stock-alert {
        height: 350px;
        width: 550px;
        max-width: 90%;
        background: #fff;
        border-left: 5px solid #ff9800;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .2);
        padding: 34px;
        display: flex;
        flex-direction: column;
        animation: popupFade .25s ease;
    }

    .stock-alert h4 {
        font-size: 24px;
        margin-bottom: 12px;
        color: #1a1a1a;
        font-family: 'DM Sans', sans-serif;
    }

    .stock-alert p {
        font-size: 16px;
        margin: 12px 0;
        color: #333;
        font-family: 'DM Sans', sans-serif;
    }

    .stock-alert ul {
        font-size: 15px;
        max-height: 220px;
        overflow-y: auto;
        color: #444;
        font-family: 'DM Sans', sans-serif;
    }

    .stock-alert-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: auto;
    }

    .stock-alert-buttons button {
        border: none;
        padding: 10px 18px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: bold;
        font-family: 'DM Sans', sans-serif;
    }

    .stock-alert-buttons button:first-child {
        background: #ddd;
        color: #333;
    }

    .stock-alert-buttons button:last-child {
        background: #2E8B57;
        color: white;
    }

    .stock-out {
        color: #dc3545;
        font-weight: 600;
    }

    .stock-low {
        color: #d4a017;
        font-weight: 600;
    }

    @keyframes popupFade {
        from {
            opacity: 0;
            transform: scale(.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<div id="stockAlertOverlay" class="stock-alert-overlay">
    <div id="stockAlert" class="stock-alert">
        <h4>Inventory Alert</h4>
        <p>The following products need restocking:</p>
        <ul>
            <?php foreach ($alertLowStocks as $item): ?>
                <li>
                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                    <?php if ($item['stock'] == 0): ?>
                        <span class="stock-out">(Out of Stock)</span>
                    <?php else: ?>
                        <span class="stock-low">(Low Stock - <?= $item['stock'] ?> remaining)</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p>Would you like to put an order for restocking?</p>
        <div class="stock-alert-buttons">
            <button id="restockLater">Later</button>
            <button id="restockYes">Restock</button>
        </div>
    </div>
</div>

<script>
    const globalLowStockCount = <?= $alertLowStockCount ?>;
    const globalOverlay = document.getElementById("stockAlertOverlay");
    const GLOBAL_KEY = "stockAlertNextShowTime";
    const SNOOZE_MS = 10000;

    function showGlobalAlert() {
        if (globalLowStockCount <= 0) return;
        globalOverlay.style.display = "flex";
    }

    function hideGlobalAlert() {
        globalOverlay.style.display = "none";
    }

    function checkGlobalState() {
        if (globalLowStockCount <= 0) return;

        if (window.location.pathname.includes("purchase_orders.php")) {
            hideGlobalAlert();
            return;
        }

        const nextShowTime = localStorage.getItem(GLOBAL_KEY);

        if (nextShowTime === null) {
            showGlobalAlert();
            return;
        }

        if (Date.now() >= parseInt(nextShowTime, 10)) {
            showGlobalAlert();
        } else {
            hideGlobalAlert();
        }
    }

    const btnLater = document.getElementById("restockLater");
    const btnRestock = document.getElementById("restockYes");

    if (btnLater) {
        btnLater.onclick = function() {
            localStorage.setItem(GLOBAL_KEY, (Date.now() + SNOOZE_MS).toString());
            hideGlobalAlert();
        };
    }

    if (btnRestock) {
        btnRestock.onclick = function() {
            // Sets 10-second timer. If they leave without buying, it pops up again.
            localStorage.setItem(GLOBAL_KEY, (Date.now() + SNOOZE_MS).toString());
            const lowStockItems = <?= json_encode($alertLowStocks) ?>;
            const itemIds = lowStockItems.map(item => item.id).join(',');
            window.location.href = "purchase_orders.php" + (itemIds ? "?items=" + itemIds : "");
        };
    }

    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(checkGlobalState, 300);
    });

    setInterval(checkGlobalState, 1000);

    window.addEventListener('storage', function(event) {
        if (event.key === GLOBAL_KEY) {
            checkGlobalState();
        }
    });
</script>