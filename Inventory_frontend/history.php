<!-- history.php -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>History</title>


  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    /* history.css */

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f3f3f3;
      color: #222;
    }

    /* NAVBAR */

    .navbar {
      height: 90px;
      background: #111;

      display: flex;
      align-items: center;
      gap: 40px;

      padding: 0 45px;
    }

    .user-box {
      background: #ff4d4d;
      color: white;

      display: flex;
      align-items: center;
      gap: 12px;

      padding: 14px 22px;
      border-radius: 12px;

      font-size: 20px;
      font-weight: 600;

      cursor: pointer;
    }

    .user-box i {
      font-size: 24px;
    }

    .navbar h1 {
      color: white;
      font-size: 30px;
      font-weight: 700;
    }

    .history-btn {
      background: #e9e9e9;
      border: none;

      padding: 14px 25px;
      border-radius: 12px;

      font-size: 20px;
      font-weight: 700;

      cursor: pointer;
    }

    /* MAIN */

    .container {
      padding: 30px;
    }

    /* STATS */

    .stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;

      margin-bottom: 35px;
    }

    .stat-card {
      background: #d9d9d9;

      padding: 25px;
      border-radius: 20px;

      min-height: 120px;
    }

    .stat-card h3 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    .stat-card p {
      font-size: 26px;
      font-weight: 600;
    }

    .green {
      color: #24b14d;
    }

    .blue {
      color: #4a6cff;
    }

    /* FILTERS */

    .filters {
      display: grid;
      grid-template-columns: 1fr 370px 230px;
      gap: 30px;

      margin-bottom: 35px;
    }

    .filters input,
    .filters select {
      height: 70px;

      border: none;
      outline: none;

      background: #d9d9d9;

      border-radius: 15px;

      padding: 0 25px;

      font-size: 22px;
      font-weight: 600;

      color: #333;
    }

    /* TABLE */

    .table-section {
      width: 100%;
    }

    /* HEADER */

    .table-header {
      background: #d9d9d9;

      display: grid;
      grid-template-columns: 1fr 1.8fr 1.8fr 1fr 1fr 1fr;

      padding: 18px 28px;

      border-radius: 15px;

      font-size: 22px;
      font-weight: 700;

      margin-bottom: 15px;
    }

    /* ROW */

    .table-row {
      display: grid;
      grid-template-columns: 1fr 1.8fr 1.8fr 1fr 1fr 1fr;

      align-items: center;

      padding: 10px 28px;

      font-size: 22px;
      font-weight: 600;
    }

    /* BADGE */

    .cash-badge {
      background: #5871ff;
      color: white;

      padding: 8px 24px;
      border-radius: 15px;

      font-size: 18px;
    }

    /* VIEW BUTTON */

    .view-btn {
      border: 2px solid #5871ff;
      background: transparent;

      color: #5871ff;

      padding: 10px 25px;
      border-radius: 15px;

      font-size: 18px;
      font-weight: 600;

      cursor: pointer;
      transition: 0.3s;
    }

    .view-btn:hover {
      background: #5871ff;
      color: white;
    }

    /* RESPONSIVE */

    @media(max-width: 1200px) {

      .stats {
        grid-template-columns: 1fr;
      }

      .filters {
        grid-template-columns: 1fr;
      }

      .table-header,
      .table-row {
        grid-template-columns: repeat(6, minmax(120px, 1fr));

        overflow-x: auto;
        font-size: 18px;
      }

    }

    @media(max-width: 768px) {

      .navbar {
        flex-direction: column;
        height: auto;
        padding: 20px;
        gap: 20px;
      }

      .navbar h1 {
        font-size: 24px;
        text-align: center;
      }

      .filters input,
      .filters select {
        font-size: 18px;
      }

    }
  </style>
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
      <?php foreach ($orders as $order): ?>

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