# 🌟 MOME — Money Management & Expense Tracker

**MOME** is a personal financial management application designed to help users organize income, expenses, financial planning, and achieve personal economic goals.  
This system is built using **PHP (MVC)** with a **MySQL** database and includes complete features for expense monitoring, financial planning, wishlist tracking, and financial education articles.

---
# 👥 Group Members 6
1. Grace Wahyuni - 245150401111029 
2. Chita Arthalina Sianturi - 245150407111057
3. A. Muh. Abduh Dzaky - 245150407111039 
4. Zaky Ahmady Santoso - 245150407111048 
5. Muhammad Rifa Aqilla - 245150407111047


## 🚀 Main Features

### 🔐 1. Login & Register
- Users can register using **username, email, and password**
- Passwords are securely stored using **hash encryption (password_hash)**
- Session-based authentication ensures the system is accessible only after login

---

### 🏠 2. Home Page (Dashboard)
- First page displayed after successful login
- Shows an overview of financial summary
- Serves as a navigation hub to all features

---

### 💰 3. Track Your Expense
Record daily income and expenses with full CRUD access.

| Attribute | Description |
|----------|-------------|
| Title | Transaction name |
| Amount | Transaction amount |
| Transaction Date | Date of transaction |
| Description | Additional information |
| Edit | Edit a transaction |
| Delete | Delete a transaction |

Additional features:
- Total income for today
- List of all transactions
- Filter by income or expense
- Sort by newest or oldest transaction

---

### 📊 4. MOME Recap
Monthly financial report that provides:
- Total income for the current month
- Total expense for the current month
- Monthly income and expense history

---

### 🎯 5. MOME Goals
Helps users plan and achieve financial goals (e.g., vacation, new gadget, emergency fund).

| Attribute | Description |
|----------|-------------|
| Goal Name | Target to be achieved |
| Target Amount | Total amount to be saved |
| Target Deadline | Goal deadline |
| Add Saving | Increase current saving |
| Edit Goal | Update the selected goal |
| Current Saving | Current savings amount |
| Remaining | Remaining amount to reach the target |
| Description | Notes for the goal |
| Progress | Progress 0% – 100% |

> The system notifies users when the deadline is approaching.

---

### 🛍 6. Wishlist
A list of items users want to buy in the future to help prevent impulsive spending and prioritize needs.

---

### 📚 7. Articles Finance
Contains articles to increase users' financial literacy.

| Role | Access |
|------|--------|
| **User** | Can read articles |
| **Admin** | Can add, edit, and delete articles |

---

## 🎨 Tech Stack

<p align="left">
  <img src="https://skillicons.dev/icons?i=php,mysql,js,html,css,bootstrap" />
</p>

### 🖥 Frontend
- HTML5 — Page structure
- CSS3 — Styling and layout
- Bootstrap 5 — Responsive UI components
- JavaScript — Client-side interaction logic

### ⚙ Backend
- PHP Native (MVC Architecture) — Controller–Model–View structure
- MySQL — Relational database
- Session-based Authentication

### 🗂 Additional Concepts
- CRUD Operations (Transactions, Wishlist, Goals, Articles)
- Role-based Access Control (Admin manages articles)
- Dashboard as the landing page after login

---
## Project Structures

# MOME-4
```
MOME-4
├── index.php          # Web Routes
├── Controller/        # PHP controllers
├── Model/             # Eloquent models
├── View/              # Mome's UI
├── Databases/         # Database schema
├── Images/            # Assets for MOME
└── uploads/           # Article Photos
    └── articles/
```gi

## 🔧 Prerequisites

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.0+ |
| Database | MySQL / MariaDB |
| Server | Apache (XAMPP recommended) |
| Browser | Chrome recommended |

> ⚠ Ensure `mysqli`, `session`, and `openssl` extensions are enabled in `php.ini`.

---

## 📌 Database Installation

1. Create a database named: `mome`
2. Import the provided SQL file
3. Configure database credentials in `/Databases`
4. Run the project in browser: http://localhost/mome-4

## 📄 License
This project is created for **educational purposes** and still needs a lot of improvement

## ✨ Thank you for exploring MOME
