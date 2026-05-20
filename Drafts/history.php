<!-- history.php -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>History</title>

  <link rel="stylesheet" href="history.css">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >
</head>
<body>

  <?php
    // SAMPLE DATA
    $totalRevenue = "₱69.00";
    $transactions = 1;
    $itemsSold = 1;

    $orders = [
      [
        "order_no" => "0001",
        "item" => "Earphones 1x",
        "payment" => "Cash",
        "discount" => "-",
        "total" => "₱69.00"
      ]
    ];
  ?>

  <!-- NAVBAR -->
  <nav class="navbar">

    <div class="user-box">
      <i class="fa-solid fa-circle-user"></i>
      <span>User</span>
      <i class="fa-solid fa-caret-down"></i>
    </div>

    <h1>K's Inventory System</h1>

    <button class="history-btn">
      History
    </button>

  </nav>

  <!-- MAIN -->
  <main class="container">

    <!-- STATS -->
    <section class="stats">

      <div class="stat-card">
        <h3>Total Revenue</h3>
        <p class="green"><?php echo $totalRevenue; ?></p>
      </div>

      <div class="stat-card">
        <h3>Transactions</h3>
        <p class="blue"><?php echo $transactions; ?></p>
      </div>

      <div class="stat-card">
        <h3>Items Sold</h3>
        <p><?php echo $itemsSold; ?></p>
      </div>

    </section>

    <!-- FILTERS -->
    <section class="filters">

      <input type="text" placeholder="Search...">

      <select>
        <option>All Payment Methods</option>
        <option>Cash</option>
        <option>GCash</option>
      </select>

      <select>
        <option>Newest first</option>
        <option>Oldest first</option>
      </select>

    </section>

    <!-- TABLE -->
    <section class="table-section">

      <!-- TABLE HEADER -->
      <div class="table-header">

        <div>Order no.</div>
        <div>Items</div>
        <div>Payment Method</div>
        <div>Discount</div>
        <div>Total</div>
        <div>Receipt</div>

      </div>

      <!-- TABLE ROWS -->
      <?php foreach($orders as $order): ?>

      <div class="table-row">

        <div><?php echo $order['order_no']; ?></div>

        <div><?php echo $order['item']; ?></div>

        <div>
          <span class="cash-badge">
            <?php echo $order['payment']; ?>
          </span>
        </div>

        <div><?php echo $order['discount']; ?></div>

        <div><?php echo $order['total']; ?></div>

        <div>
          <button class="view-btn">
            View
          </button>
        </div>

      </div>

      <?php endforeach; ?>

    </section>

  </main>

</body>
</html>