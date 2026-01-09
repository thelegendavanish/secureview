# 🔐 Secure View – Controlled PDF Viewing Platform

Secure View is a secure, web-based PDF viewing system designed to protect confidential documents from unauthorized access, copying, downloading, and redistribution. It enables organizations to securely share sensitive PDF files while maintaining strict control over how and when the content is accessed.

This solution is ideal for **examinations, legal documents, corporate reports, research material, academic evaluations, and government records**.

---

## 🚀 Key Highlights

- Zero file exposure — PDFs are never downloaded to user devices  
- Dynamic user-specific watermarking (Name, UID, IP Address)  
- Time-locked access with real-time countdown expiry  
- Screenshot mitigation mechanisms (supported browsers)  
- Detailed audit logging and activity tracking  
- Simple PHP-based architecture — deploys easily on shared hosting or XAMPP  

---

## 🛡️ Core Security Features

| Feature | Description |
|-------|-------------|
| Copy / Download / Print Restriction | Prevents content extraction via browser shortcuts and UI controls |
| Screenshot Protection | Detects and blocks screen-capture attempts in supported environments |
| Dynamic Watermarking | Overlays viewer Name, UID, and IP address across the document |
| Time-Based Expiry | Access automatically revokes after defined duration |
| Granular User Control | Access is restricted per UID, email, IP, and expiry window |
| Audit Logging | Every login is logged with timestamp and IP address |
| Zero File Exposure | Original PDF never leaves the server |

---

## 📁 Project Structure

| File | Description |
|------|-------------|
| `admin_panel.php` | Admin dashboard to upload PDF, create users, set access times, enable/disable form |
| `user_panel.php` | Secure viewer with watermarking, countdown, browser protection |
| `login.php` | User authentication & IP logging |
| `uploads/forms.php` | Public response submission form |
| `status.json` | Controls whether submission form is enabled or closed |
| `export.php` | Exports submissions as CSV |
| `db.php` | Database connection |
| `assets/` | CSS, JS, PDF.js library |

---

## 🖥️ Tech Stack

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript  
- **PDF Rendering:** PDF.js  
- **Database:** MySQL (mysqli prepared statements)  
- **Security:** Session-based auth, IP logging, watermark overlays  

---

## 🔐 Admin Panel Features

- Upload confidential PDF  
- Define Start Time & Expiration Time  
- Create authorized users (Name + UID)  
- Enable / Disable submission form  
- Monitor user submissions  
- Export submissions as CSV  

---

## 👤 User Panel Features

- Login using assigned UID  
- Countdown timer before access  
- Auto-revoke after expiry  
- Heavily watermarked live document  
- Right-click disabled  
- Blocks keys: `Ctrl+S`, `Ctrl+P`, `Ctrl+U`, `F12`  

---

## 🧾 Public Submission Form

Users submit their work/task via Google Drive link.

**Fields:**
- Name  
- UID  
- Email  
- Google Drive Link  

Form automatically blocks submissions if admin closes access.

---

## 🗄️ Database Tables

| Table | Purpose |
|------|---------|
| `admins` | Admin credentials |
| `users` | Authorized users |
| `user_logins` | Login logs (UID, IP, Timestamp) |
| `responses` | User submissions |

---

## ⚙️ Installation Guide

1. Clone the repository  
   ```bash
   git clone https://github.com/yourusername/secure-view.git

2. Import database file into MySQL.
3. Configure database credentials in db.php.
4. Place project folder inside:
   ```bash
   htdocs/secure-view
5. Open browser:
   ```bash
   http://localhost/secure-view

---
## 📌Use Cases
- Online Exams & Certifications
- Legal & Compliance Documents
- Corporate Confidential Reports
- Academic Evaluations
- Government-sensitive records

---

## 🏁 Conclusion

Secure View is not just a PDF viewer, it is a secure document distribution framework that ensures complete ownership, traceability, and protection of sensitive information even after it has been shared.

---
## ✉ 📧 Contact / Reporting
Mail at: contact@thelegendavanish.tech
⭐ Feel free to fork, explore, or contribute!
⭐ If you like this project, don't forget to star the repo!
