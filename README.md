# 📚 University Library Management System - Diagram Project


A comprehensive and efficient Library Management System tailored for university-level requirements. This project models the entire lifecycle of library operations — from book registration and transaction tracking to penalties and warehouse management — using an advanced ER diagram.

## 📂 Project Contents

- `library-management-diagram.png` – A detailed diagram representing the structure and workflow of the library management system.
- `library-management-presentation.pptx` – A PowerPoint presentation explaining the diagram and the system components.

## 🧠 About the Diagram

The diagram highlights the core elements of a university library management system:

- **User roles** (students, staff, librarians)
- **Book catalog and database**
- **Borrowing and returning processes**
- **System entities and relationships**
- **Data management and access control**

It provides a clear visual representation suitable for academic analysis, software modeling, and system design documentation.

## 🎯 Project Purpose

This project was developed as part of an academic assignment to model and present an efficient design for a university's library system using diagrammatic representation.

## 📸 Preview

![Library Management System Diagram]![aq2 drawio]![Photo of Diagram](https://app.diagrams.net/#Hhussaini021%2FLibrary_management%2Fmain%2FER-Diagram-Library-management-System.drawio#%7B%22pageId%22%3A%22enIxqUN0M-IkAclhJr2Q%22%7D)
<img width="1319" height="768" alt="Screenshot 2025-09-15 224455" src="https://github.com/user-attachments/assets/a7b2f7c4-4737-432f-b8ce-6c167d7839af" />






---

# 📚 Library Management System

A full-stack **Library Management System** built using **PHP (backend)**, **HTML/CSS/JavaScript (frontend)**, and **SQL Server (database)**.
This system allows users to manage books, members, loans, returns, and administrative operations efficiently.

---

## 🚀 Features

### 📝 Core Features

* Add, edit, delete, and search **books**
* Manage **members** and user profiles
* Issue and return **books**
* Track overdue books and fines
* Real-time statistics on dashboard
* Secure login & session management

### 💾 Database Features (SQL Server)

* Proper normalization (up to 3NF)
* Primary keys, foreign keys, constraints
* Stored procedures for CRUD operations
* Triggers for logging activities
* Views for reporting

### 🎨 Frontend Features

* Clean and responsive UI using **HTML5 + CSS3**
* Interactive components using **JavaScript**
* Validations on both client & server side

### ⚙️ Backend Features (PHP)

* MVC-like folder structure
* Secure database connectivity with SQL Server
* Prepared statements to prevent SQL Injection
* Reusable functions and modular codebase

---

## 🧱 Technology Stack

| Layer       | Technology Used                          |
| ----------- | ---------------------------------------- |
| Frontend    | HTML, CSS, JavaScript                    |
| Backend     | PHP 8+                                   |
| Database    | Microsoft SQL Server                     |
| API/Driver  | SQLSRV (Microsoft PHP SQL Server Driver) |
| Other Tools | XAMPP/WAMP, Git, VS Code                 |

---

## 📂 Project Structure

```
/project-root
│── /assets          # CSS, images, JS files
│── /config          # DB configuration (SQL Server connection)
│── /controllers     # PHP logic for handling requests
│── /models          # Database operations (CRUD)
│── /views           # User interface pages
│── /sql             # SQL scripts: tables, procedures, triggers
│── index.php        # Main entry point
│── README.md        # Project documentation
```

---

## 🛢️ Database Setup (SQL Server)

1. Create a new database:

   ```sql
   CREATE DATABASE LibraryDB;
   ```

2. Run the SQL scripts inside the `/sql` folder:

   * Tables
   * Relationships
   * Stored procedures
   * Triggers

3. Enable PHP SQLSRV extension:

   * Install driver from Microsoft
   * Update `php.ini`:

     ```
     extension=php_sqlsrv.dll
     extension=php_pdo_sqlsrv.dll
     ```

4. Update your database configuration in:

   ```
   /config/database.php
   ```

---

## ▶️ How to Run the Project

### Option 1: Local Machine

1. Install **XAMPP/WAMP**
2. Enable SQL Server driver
3. Clone the repository:

   ```
   git clone https://github.com/your-username/library-management-system.git
   ```
4. Move folder to `/htdocs` or `/www`
5. Start Apache & SQL Server
6. Open in browser:

   ```
   http://localhost/library-management-system/
   ```

### Option 2: Production Server

* Upload project files
* Configure `database.php`
* Ensure SQLSRV drivers exist on the server

---

## 🔐 Security

* Password hashing (bcrypt)
* SQL Injection protection using prepared statements
* Role-based access (Admin/User)
* Error logging system

---

## 📸 Screenshots (optional)

*Add your UI images here if available.*

---

## 📌 Future Enhancements

* REST API version
* QR code generation for books
* Advanced reporting dashboard
* Mobile-friendly UI improvements

---

## 🤝 Contributing

Pull requests are welcome!
Please open an issue to discuss improvements or bugs.

---

## 📄 License

This project is licensed under the **MIT License**.

 
---
## 👨‍💻 Authors

This project was created by:

- **Murtaza Hussaini**
- **Jawied Moraadi**

Students of the **Faculty of Information communication and Technology**,  
**Kabul University**

Developed under the guidance of **Prof. Garani**.  
Special thanks for the support, supervision, and valuable insights provided throughout the project.




