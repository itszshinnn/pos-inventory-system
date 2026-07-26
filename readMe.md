# POS Inventory System

## Overview

- The **POS Inventory System** is an application developed to help gadget peripherals and accessories stores efficiently manage their inventory and sales operations in one centralized platform. It caters to small and medium-sized businesses by providing an organized way to monitor products, process customer transactions, and manage stock levels. The system is designed for both administrators and staff, where administrators can manage products, categories, accounts, and reports, while staff can efficiently process sales through the point-of-sale interface. With features such as inventory tracking, sales history, XML import/export, and real-time stock monitoring, the system improves operational efficiency, reduces manual errors, and streamlines daily business activities.

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
 
# Features

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

![hist](ReadMe/history.png)

![reci](ReadMe/receipt.png)

### Inventory Logs
- Records changes made to the items
- Shows old and new stock when changed
- Shows when an item or stock gets deleted
- Shows when an item or stock gets added
- Shows date on each changes

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
1. PayMongo Online Checkout API - we connected our Point of Sale system directly to the PayMongo API, which allows the staffs or cashiers to accept card payments and local e-wallets such as GCash and Maya. When a digital payment method was chosen at the checkout, the system calculates the amount in centavos, securely communicates with PayMongo's servers to get a unique checkout link, and seamlessly redirects the customer to a secure page to complete the transaction before returning them to our app to automatically confirm the sale and clear the cart.  

2. Automated SMTP Mail Service - we built an automated email notification system using the standard SMTP protocols. This service runs behind the scenes and instantly delivers structured emails to a pre-configured business address whenever a product drops to a critical low-stock level of 3 units or lower, or when a pending supplier delivery has been physically checked into the shelves by an administrator.

3. Interactive 3D Model Viewer - the POS menu uses Google's open-source 3D <model-viewer> web library. Whenever an admin adds a new product, they can upload its corresponding 3D asset (.glb or .gltf file), allowing users in the Point of Sale menu to click and open an interactive modal where they can freely click, drag, rotate, and zoom in on a full 3D model rendering of the item in real time.


# Database
## Database Name
- POS_Inventory_System

# Notes
## How to clone the repo
- Open xampp/htdocs folder in vscode
- Open view -> Terminal
- Type in "git clone https://github.com/itszshinnn/pos-inventory-system.git"
- Always pull before pushing changes.
- Use proper commit changes.
- PUT YOUR FRONTEND CODES IN THE .Drafts Folder, kami na ni sean mag coconnect nyan

## Git Commands
- git pull origin (branch name) | download all the latest code in this branch
- git checkout (branch name) | switch your workspace into an existing branch
- git merge (branch name)  | merge your target branch to your active current branch
- git push origin (branch name)  | uploads your local commit to your branch
- git status | checks what branch you are currently in

## Step by step
- Make sure you are in your own branch
- Add/edit your code
- Commit changes and add message/comment
- Push your updates to GitHub (into your own branch)
- Switch to main branch
- Always make sure you are update in main branch
- Merge your own branch into main branch
- Push the updated main branch to GitHub

## How to Add the database
- Open phpmyadmin on xampp
- On the left side bar press "New"
- Create database named "inventory_db"
- Once done ignore the add table name
- Click the "Import" tab above
- Click the "Choose File"
- Select inventory_db.sql
- Once uploaded go to the bottom and click import
