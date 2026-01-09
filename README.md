
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

- **Backend:** PHP (Vanilla)  
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

Users submit their work via Google Drive link.

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
