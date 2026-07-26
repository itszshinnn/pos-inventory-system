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
- Total Categories
- Low Stock
- Out of Stock
- Total Revenue
- Total Transactions
- Total Items Sold
- Total Logs
- Total Products Added
- Total Products Deleted
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

- Product No. / Name / Catergory / Price / Stocks / Status (Fine,Low,Out of stock) / Actions (Edit or Delete)

![edit](ReadMe/editproduct.png)

## Add Product
- Enter Product Name
- Select Category
- Input price
- Input stock
- (optional, has default image) upload product image 

![add](ReadMe/addproduct.png)

## XML Files
- Export each table in the database to individual files
- Export all database tables into one singular file
- Import XML 
- Import both works on individual files and database file

![xml](ReadMe/XML.png)

## History
### Sales History
- Records all the processed orders
- Order No. / Items Summary / Payment method / Discount / Total Amount / Actions (View Receipt)
- **Excel Export:** Export currently filtered transactions as native `.xls` spreadsheets with gridlines and formatted order numbers.

![hist](ReadMe/history.png)

![reci](ReadMe/receipt.png)

### Inventory Logs
- Records changes made to the items
- Shows old and new stock when changed
- Shows when an item or stock gets deleted
- Shows when an item or stock gets added
- Shows date on each changes
- **Excel Export:** Export active inventory modification logs into styled spreadsheets.

![inv](ReadMe/invhistory.png)

## Accounts 
- Shows total accounts
- Shows total Admin accounts
- Shows total Staff accounts
- Admin can create a staff or an admin account
- Shows all of the existing staff (including admin)
- Admin can edit or delete staff accounts

![userss](ReadMe/users.png)

# User Side :
## POS System
- Shows all of the items in the database
- Shows records of stock of each item
- Greys out items that are sold out
- User can filter items using categories
- User can search for an item
- Clicking on an item adds them to cart

![POS Screenshot](ReadMe/POS.png)

## Cart
- Shows all of the items inside the cart
- User can remove the item from the cart
- User can input a discount, either percentage (%) or in pesos

## Checkout
- Shows a breakdown of the items price
- Shows the total of the order
- User can choose a payment method (Cash, Card, GCash, Maya)
- User inputs how much is paid
- System automatically calculates the change

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
