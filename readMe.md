# POS Inventory System

## Members
- Sean
- Limo
- Cerbo
- Bettina
- Calinaya
- Leah
 
# Features
## Dashboard
- Total Products
- Total Units
- Total Categories
- Low Stock
- Out of Stock

## Product Management
- View All Products
- Search Product
- Add Product
- Edit Product
- Delete Product

## Category Management
- Add Category
- View Categories
- Edit Category
- Delete Category

# Product Categories
## Input Devices
- Keyboard (Wired/Wireless)
- Mouse (Wired/Wireless)

## Audio Devices
- Earphones
- Wireless Earbuds
- Speaker

## Storage Devices
- Memory Card (SD/MicroSD)
- USB Flash Drive
- External Hard Drive

## Output Devices
- Monitor
- Webcam

# Database
## Database Name
- POS_Inventory_System
## Table Name 
- Accounts, Products

## Products Table
- Item Number | Displays the id of the item in the table
- Name | The name of the item in the table
- Category | Input Devices, Audio, Storage, Output Devices
- Price | Price of each item
- Stocks | Current number of item in stock
- Status | Fine if there are enough stock, Low stock, Out of stock

# UI Design
## Side Navbar
	Dashboard
	- Total Products
	- Total Units
	- Categories
	- Low stock
	- Out of stock

	Categories
	- Add New Category
	- View All Categories
	- Edit/Delete Category

	Products
	- Item Number
	- Product Name
	- Category 
	- Price 
	- Stocks 
	- Status
	- Edit/Delete 

# Notes
## How to clone the repo
- Open xampp/htdocs folder in vscode
- Open view -> Terminal
- Type in "git clone https://github.com/itszshinnn/pos-inventory-system.git"
- Always pull before pushing changes.
- Use proper commit changes.
- PUT YOUR FRONTEND CODES IN THE .Drafts Folder, kami na ni sean mag coconnect nyan

## Directory to place in the browser
- http://localhost/inventory/inventory_frontend/dashboard.php
- http://localhost/inventory/inventory_frontend/categories.php
- http://localhost/inventory/inventory_frontend/products.php
- http://localhost/inventory/inventory_frontend/add-product.php

## Git Commands
- git pull origin <branch name> | download all the latest code in this branch
- git checkout <branch name> | switch your workspace into an existing branch
- git merge <branch name> | merge your target branch to your active current branch
- git push origin <branch name> | uploads your local commit to your branch
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
