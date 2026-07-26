# POS Inventory System

## Overview

- The POS Inventory System is a web-based application developed to help businesses efficiently manage their inventory and sales operations in one centralized platform. It caters to small and medium-sized businesses, such as retail stores, mini marts, and shops, by providing an organized way to monitor products, process customer transactions, and manage stock levels. The system is designed for both administrators and staff, where administrators can manage products, categories, accounts, and reports, while staff can efficiently process sales through the point-of-sale interface. With features such as inventory tracking, sales history, XML import/export, and real-time stock monitoring, the system improves operational efficiency, reduces manual errors, and streamlines daily business activities.


=======

## Online Site
- Link to an online hosted version of the POS System. fully working https://k-inventory.html-5.me/
- Admin account:
Username: admin
Password: admin

- User account:
Username: User
Password: 1234

## Members
- Sean
- Limo
- Cerbo
- Bettina
- Calinaya
- Morimitsu
 
# # Features

## Login
- Can login using an Admin account
- Can login using an User/Staff account

![login](ReadMe/login.png)

# Admin Side:

## Dashboard
- Total Products
- Total Units
- Low Stock
- Out of Stock
- Total Revenue
- Total COGS
- Total Purchases
- Total Profit
- Total Sales
- Notifications tab: (Added product, Deleted product, Edited product, Stock alert, New user, Processed Orders)

![dash](ReadMe/dashboard.png)

## Category Management
- Add Category
- View Categories
- Edit Category
- Delete Category

![cate](ReadMe/categories.png)

## Product Management
- View All Products
- Search Product
- Add Product
- Edit Product
- Delete Product

![prod](ReadMe/products.png)

- Product No. | Name | Catergory | Brand | Color | Size | Bought | Retail | Stocks (Fine,Low,Out of stock) | Actions (Restock, Edit or Delete)

![edit](ReadMe/editproduct.png)

## Add Product
- Enter Product Name
- Select Category
- Input Cost Price
- Input Retail Selling Price
- Input Initial Quantity of Stocks
- Input Product Specifications (Optional)
- Input Product Description, Images, and 3D Models

![add](ReadMe/addproduct.png)

## Purchase Orders
- Search product to add in restocking
- View draft purchase orders
- View pending incoming orders

![add](ReadMe/[purchaseorders].png)

## Purchase History
- View purchase history

![add](ReadMe/[purchasehistory].png)

## Backup and History
- Export tables into one database XML file
- Export tables into seperate XML files
- Import XML 
- Import both works on individual files and database file

![xml](ReadMe/XML.png)

## History
### Sales History
- Records all the processed orders
- View total revenue, total transactions made, total items sold
- Sort data in table
- Excel Export: Export currently filtered transactions as native `.xls` spreadsheets with gridlines and formatted order numbers.

![hist](ReadMe/history.png)

![reci](ReadMe/receipt.png)

### Inventory Logs
- Records changes made to the items
- View total logs, products added, products deleted
- Shows old and new stock when changed
- Shows when an item or stock gets deleted
- Shows when an item or stock gets added
- Shows date on each changes
- Excel Export: Export active inventory modification logs into styled spreadsheets.

![inv](ReadMe/invhistory.png)

### Logs History
- Search for specific history logs
- View logs changes

![inv](ReadMe/invhistory.png)

## Accounts 
- Shows total accounts
- Shows total Admin accounts
- Shows total Staff accounts
- Add system email for business emails to receive notifications
- Account creation
- Account actions (edit and delete)

![userss](ReadMe/users.png)

# User Side:
## POS System
- Shows all items in the database with their respective images and 3D models.
- Real-time stock indicator displays (automatically grays out items when sold out).
- Category filtering and real-time search inputs to find products quickly.
- **Interactive 3D Product Viewer:** Cashiers can click "View" on any product to open a premium details modal featuring an interactive 3D model viewport (supports rotation, zoom, drag) and technical attributes (cost, brand, dimensions, color, type).
- **Mobile Responsive Tabbed Layout:** On mobile screens, the terminal automatically transforms into a single-panel interface with bottom navigation tabs (`🏷️ Products` and `🛒 Cart` with a live badge item count) to prevent squishing and ensure fluid operations.

![POS Screenshot](ReadMe/POS.png)

## Cart & Presets
- Displays all selected items inside the sidebar cart with a live quantity counter.
- Easy cart control operations (increment, decrement, and delete actions per line item).
- **Flexible Discounts:** Cashiers can input percentage discounts (e.g. `10%`) or flat currency discounts (e.g. `₱50`) directly on the transaction total.

## Checkout & Processing
- Itemized pricing breakdown displaying subtotal, discount reduction, and total due.
- Payment method options: Cash, Card, GCash, and Maya (with PayMongo redirects for digital options).
- Interactive cash-received field with instant change calculation.
- Automatically generates and displays receipt summaries upon checking out.

![chec](ReadMe/checkout.png)

# Integration
1. **PayMongo Online Checkout API:** Point of Sale system directly integrated with the PayMongo API to accept card payments and local e-wallets (GCash and Maya) seamlessly via secure checkout session redirects.

2. **Automated SMTP Mail Service:** Automated background email notifications using SMTP protocols that instantly deliver stock alerts to administrators when items drop below critical levels.

3. **Interactive 3D Model Viewer:** Integration of Google's `<model-viewer>` library allowing administrators to upload `.glb` or `.gltf` 3D product models, which staff can interactively inspect in real-time.

4. **Native Excel Spreadsheet Exports:** Client-side spreadsheet builder that outputs files using the native `application/vnd.ms-excel` MIME type. Resolves the text import wizard prompt, keeps order number formats (preserving leading zeros), and shows up in the file browser under default spreadsheet filters.

5. **Conversational AI Assistant with Action Intents:** Natural-language bot powered by Groq API (Llama models). Includes:
   - **Database QA:** Evaluates natural language queries to generate safe SQL read commands.
   - **Continuous Conversation Context:** Tracks history states using sessionStorage to handle follow-up statements.
   - **Action Intent Execution:** Processes modification requests (like restocking, updating retail prices, and cost prices) directly into transactions. Includes support for bulk multi-command updates in a single prompt.

# Database
## Database Name
- inventory_db
