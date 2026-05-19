<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>K's Inventory System</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f5f5f5;
    overflow:hidden;
}

.container{
    display:flex;
    width:100%;
    height:100vh;
}

/* LEFT PANEL */

.left-panel{
    width:67%;
    background:#f5f5f5;
    border-right:1px solid #cfcfcf;
}

/* TOPBAR */

.topbar{
    height:85px;
    background:#181818;
    display:flex;
    align-items:center;
    padding:0 25px;
    gap:25px;
    color:white;
}

.user-btn{
    background:#ff4b4b;
    border:none;
    color:white;
    padding:15px 25px;
    border-radius:10px;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.user-btn:hover{
    background:#ff2f2f;
}

.title{
    font-size:28px;
    font-weight:700;
    flex:1;
}

.history-btn{
    background:#efefef;
    border:none;
    padding:14px 30px;
    border-radius:10px;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
}

/* SEARCH */

.search-container{
    padding:20px;
}

.search-box{
    width:100%;
    height:70px;
    border-radius:22px;
    border:4px solid #bcbcbc;
    padding:0 25px;
    font-size:28px;
    outline:none;
}

/* CATEGORY */

.categories{
    display:flex;
    gap:15px;
    padding:0 20px;
    flex-wrap:wrap;
}

.category-btn{
    border:3px solid #a7a7a7;
    background:white;
    padding:12px 28px;
    border-radius:40px;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.category-btn:hover{
    transform:scale(1.05);
}

.active{
    background:#4d66ff;
    color:white;
    border:none;
}

/* PRODUCTS */

.products{
    padding:30px 20px;
    display:flex;
    gap:40px;
    flex-wrap:wrap;
}

.product-card{
    width:240px;
    background:#d9d9d9;
    border-radius:25px;
    padding:20px;
    text-align:center;
    cursor:pointer;
    transition:.2s;
}

.product-card:hover{
    transform:translateY(-5px);
}

.product-card img{
    width:120px;
    height:120px;
    object-fit:contain;
    margin-bottom:10px;
}

.product-name{
    font-size:20px;
    font-weight:700;
    color:#333;
}

.price{
    font-size:18px;
    font-weight:700;
    margin-top:5px;
}

.stock{
    margin-top:10px;
    font-size:18px;
    font-weight:700;
}

.green{
    color:#2db84d;
}

.orange{
    color:#d9992f;
}

.red{
    color:#ff8c8c;
}

/* RIGHT PANEL */

.right-panel{
    width:33%;
    background:#efefef;
    display:flex;
    flex-direction:column;
}

.order-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px;
    border-bottom:1px solid #c7c7c7;
}

.order-header h1{
    font-size:30px;
}

.count{
    width:45px;
    height:45px;
    background:#5470ff;
    color:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}

.order-items{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    color:#b8b8b8;
    font-size:24px;
    font-weight:600;
    border-bottom:1px solid #c7c7c7;
}

.summary{
    padding:20px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
    font-size:22px;
    font-weight:700;
}

.discount{
    display:flex;
    gap:15px;
    margin-bottom:20px;
}

.discount input{
    flex:1;
    height:50px;
    border-radius:10px;
    border:2px solid #a8a8a8;
    padding-left:15px;
    font-size:18px;
}

.percent-btn{
    width:80px;
    border:none;
    border-radius:10px;
    font-size:24px;
    cursor:pointer;
}

.checkout-btn{
    width:100%;
    height:65px;
    border:none;
    background:#9b9b9b;
    color:white;
    border-radius:15px;
    font-size:22px;
    font-weight:700;
    cursor:pointer;
    margin-bottom:15px;
}

.clear-btn{
    width:100%;
    height:65px;
    border:3px solid #ff7070;
    background:white;
    border-radius:15px;
    font-size:22px;
    font-weight:700;
    cursor:pointer;
}

/* CART ITEM */

.cart-item{
    width:90%;
    background:white;
    padding:15px;
    border-radius:15px;
    margin-bottom:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.remove-btn{
    background:red;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
}

</style>
</head>

<body>

<div class="container">

<!-- LEFT -->

<div class="left-panel">

<div class="topbar">

<button class="user-btn">
👤 User ▼
</button>

<div class="title">
K's Inventory System
</div>

<button class="history-btn">
History
</button>

</div>

<!-- SEARCH -->

<div class="search-container">
<input type="text" class="search-box" placeholder="Search...">
</div>

<!-- CATEGORY -->

<div class="categories">

<button class="category-btn active">All</button>
<button class="category-btn">Input Devices</button>
<button class="category-btn">Audio Devices</button>
<button class="category-btn">Storage Devices</button>
<button class="category-btn">Output Devices</button>

</div>

<!-- PRODUCTS -->

<div class="products">

<div class="product-card" onclick="addToCart('Wireless Mouse',249)">
<img src="https://cdn-icons-png.flaticon.com/512/4341/4341139.png">

<div class="product-name">
Wireless Mouse
</div>

<div class="price">
₱249.00
</div>

<div class="stock green">
10 stocks left
</div>
</div>

<div class="product-card" onclick="addToCart('Keyboard',499)">
<img src="https://cdn-icons-png.flaticon.com/512/643/643131.png">

<div class="product-name">
Keyboard
</div>

<div class="price">
₱499.00
</div>

<div class="stock orange">
3 stocks left
</div>
</div>

<div class="product-card" onclick="addToCart('Earphones',69)">
<img src="https://cdn-icons-png.flaticon.com/512/3659/3659899.png">

<div class="product-name">
Earphones
</div>

<div class="price">
₱69.00
</div>

<div class="stock red">
No stocks left
</div>
</div>

<div class="product-card" onclick="addToCart('Wireless Earbuds',499)">
<img src="https://cdn-icons-png.flaticon.com/512/3659/3659898.png">

<div class="product-name">
Wireless Earbuds
</div>

<div class="price">
₱499.00
</div>

<div class="stock green">
10 stocks left
</div>
</div>

</div>

</div>

<!-- RIGHT -->

<div class="right-panel">

<div class="order-header">

<h1>Current Order</h1>

<div class="count" id="count">
0
</div>

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

<div class="discount">

<input type="text" placeholder="Discount">

<button class="percent-btn">
%
</button>

</div>

<div class="summary-row">
<span>Total</span>
<span id="total">₱0.00</span>
</div>

<button class="checkout-btn">
Proceed to checkout
</button>

<button class="clear-btn" onclick="clearOrder()">
Clear Order
</button>

</div>

</div>

</div>

<script>

let total = 0;
let count = 0;

function addToCart(name, price){

    const orderItems = document.getElementById("orderItems");

    if(count === 0){
        orderItems.innerHTML = "";
        orderItems.style.justifyContent = "flex-start";
        orderItems.style.paddingTop = "20px";
    }

    const item = document.createElement("div");

    item.classList.add("cart-item");

    item.innerHTML = `
    
    <div>
        <strong>${name}</strong><br>
        ₱${price}.00
    </div>

    <button class="remove-btn">
    X
    </button>

    `;

    item.querySelector(".remove-btn").addEventListener("click", function(){

        item.remove();

        total -= price;
        count--;

        updateTotal();

        if(count === 0){

            orderItems.innerHTML = `
            <p>No items yet</p>
            <p>Click a product to add.</p>
            `;

            orderItems.style.justifyContent = "center";
        }

    });

    orderItems.appendChild(item);

    total += price;
    count++;

    updateTotal();

}

function updateTotal(){

    document.getElementById("subtotal").innerHTML = "₱" + total.toFixed(2);

    document.getElementById("total").innerHTML = "₱" + total.toFixed(2);

    document.getElementById("count").innerHTML = count;

}

function clearOrder(){

    const orderItems = document.getElementById("orderItems");

    orderItems.innerHTML = `
    <p>No items yet</p>
    <p>Click a product to add.</p>
    `;

    orderItems.style.justifyContent = "center";

    total = 0;
    count = 0;

    updateTotal();

}

</script>

</body>
</html>