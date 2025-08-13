# 🏥 Patient Appointment Scheduling System

![PHP](https://img.shields.io/badge/PHP-7.4+-blue?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-Responsive-blueviolet?style=flat&logo=bootstrap)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)

> A **web-based healthcare platform** for booking, managing, and tracking medical appointments — built with PHP, MySQL, and Bootstrap.

---

## 📌 Overview
The **Patient Appointment Scheduling System** streamlines appointment management for **patients, doctors, and administrators** with role-based dashboards, email reminders, and real-time scheduling.

---

## ✨ Features

- **Role-Based Access**
  - 👩‍⚕️ **Patients:** Book, cancel, and reschedule appointments.
  - 🧑‍⚕️ **Doctors:** Manage schedules, set daily limits, update appointment statuses.
  - 🛡️ **Admins:** Oversee users, appointments, and system settings.
- 📧 **Automated Email Reminders** (via PHPMailer) to reduce no-shows.
- 📊 **Interactive Dashboards** with real-time data.
- 🔒 **Secure Authentication** with password hashing & sessions.
- 📱 **Responsive UI** (HTML, CSS, JavaScript, Bootstrap).

---

## 🛠️ Tech Stack

| **Frontend**         | **Backend** | **Database** | **Tools**      |
|----------------------|-------------|--------------|----------------|
| HTML5, CSS3, JS      | PHP         | MySQL        | XAMPP / WAMP   |
| Bootstrap            | PHPMailer   |              | Git / GitHub   |
|                      | Sessions    |              | VS Code        |

---

## 🚀 Getting Started

<details>
<summary>📥 Installation Guide</summary>

### **Prerequisites**
- XAMPP/WAMP
- PHP 7.4+ & MySQL 5.7+
- Web browser (Chrome/Firefox)

### **Steps**
1. **Clone Repository**
    ```bash
    git clone https://github.com/your-username/patient-appointment-system.git
    ```
2. **Database Setup**
    - Import `database.sql` from `/db` into **phpMyAdmin**.
3. **Server Setup**
    - Place project in `htdocs` (XAMPP) or `www` (WAMP).
4. **PHPMailer Config**
    - Update SMTP credentials in `includes/mail_config.php`.
5. **Run Project**
    ```
    http://localhost/patient-appointment-system/login.php
    ```

</details>

---

## 📂 Project Structure
```plaintext
project-root/
├── assets/          # CSS, JS, images
├── db/              # Database scripts
├── includes/        # PHP utilities
├── admin/           # Admin dashboard
├── doctor/          # Doctor dashboard
├── patient/         # Patient dashboard
├── login.php
├── register_user.php
└── README.md


![image alt ] (
