<div align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel"/>
  <img src="https://img.shields.io/badge/PHP-^8.2-777BB4?style=for-the-badge&logo=php" alt="PHP"/>
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=fff" alt="MySQL"/>
  <img src="https://img.shields.io/badge/Bootstrap_5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=fff" alt="Bootstrap"/>
  <br/>
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=fff" alt="Vite"/>
  <img src="https://img.shields.io/badge/AdminLTE-3-blue?style=for-the-badge" alt="AdminLTE"/>
  <img src="https://img.shields.io/badge/2FA-Google-4285F4?style=for-the-badge&logo=google&logoColor=fff" alt="2FA"/>
</div>

<br/>

<div align="center">
  <h1>🎫 HelloEvents</h1>
  <h3>Event Ticket Booking System</h3>
  <p><strong>A full-featured, production-ready event management and ticket booking platform built with Laravel.</strong></p>
  
  <br />
  <hr style="width: 50%; border: 1px solid #eee;" />
  <p>Crafted with ❤️ by <a href="https://www.vfixtechnology.com" target="_blank"><strong>VFIX TECHNOLOGY</strong></a></p>
</div>

<br/>

---

## ✨ Overview

**HelloEvents** is a complete event ticketing solution that empowers organizers to create, manage, and sell tickets for events of any scale. Attendees can browse events, purchase tickets, and get QR-coded tickets delivered instantly. Organizers get a powerful admin panel with role-based access, analytics, and more.

---

## ⚡ Features

### 🌐 Frontend

| Feature                       | Description                                                                   |
| ----------------------------- | ----------------------------------------------------------------------------- |
| **🏠 Event Listings**         | Browse upcoming & past events by category with pagination                     |
| **📄 Event Detail Page**      | Venue, map, ticket types, add-ons, host info, and booking CTA                 |
| **📝 Multi-Step Booking**     | Select tickets → Attendee details → Checkout → Payment                        |
| **🎟️ Multiple Ticket Types**  | Per-event pricing, compare-at price, quantity limits, min/max entries         |
| **➕ Add-Ons**                | Optional extras per ticket (VIP, meal, merch, etc.)                           |
| **🏷️ Coupons**                | Fixed or percentage discount codes with expiry & usage limits                 |
| **🧾 Tax Rates**              | Configurable tax per event                                                    |
| **💳 Online Payments**        | Pay at Event (COD) · Razorpay & Stripe (🔒 paid add-on)                       |
| **📱 QR Code Ticket Scanner** | On-site instant check-in via UUID, email, phone or name scan (🔒 paid add-on) |

### 👑 Admin Dashboard

| Feature                | Description                                                  |
| ---------------------- | ------------------------------------------------------------ |
| **📊 Analytics**       | Total events, orders, revenue, tickets sold, upcoming events |
| **📈 Revenue Chart**   | Monthly revenue trends visualization                         |
| **🕐 Recent Orders**   | Quick view of latest transactions                            |
| **📋 Order Breakdown** | Status distribution at a glance                              |

### 🎯 Event Management

| Feature                 | Description                                            |
| ----------------------- | ------------------------------------------------------ |
| **➕ CRUD**             | Full Create, Read, Update, Delete                      |
| **🖼️ Media**            | Multiple images with auto WebP conversion & thumbnails |
| **🧾 Tax Assignment**   | Per-event tax rates                                    |
| **👤 Host Details**     | Name, email, phone, social links                       |
| **📍 Venue & Map**      | Location, map link, video, timezone                    |
| **⭐ Featured/Publish** | Toggle visibility                                      |

### 🎫 Ticket Management

| Feature                | Description                                |
| ---------------------- | ------------------------------------------ |
| **🔍 Advanced Search** | Filter by event, ticket type, status, text |
| **📥 Export**          | Excel / CSV export                         |
| **✏️ Edit Attendees**  | Update attendee details                    |
| **🔄 Status Updates**  | Valid / Used / Cancelled / Refunded        |
| **📎 PDF Download**    | Individual ticket PDF with QR code         |
| **📧 Resend Email**    | Re-send ticket notification                |

### 🛡️ Security & Access

| Feature                | Description                        |
| ---------------------- | ---------------------------------- |
| **🔐 Google 2FA**      | Two-factor authentication with OTP |
| **🔑 Recovery Codes**  | Backup codes for 2FA               |
| **👥 Role Management** | 40+ granular permissions           |
| **👤 User CRUD**       | Manage users with role assignment  |

### 🔔 Notifications

- **📬 Email** – Ticket PDFs, payment receipts, admin alerts
- **⚡ Event-Driven** – `OrderCreated` → Tickets + Receipt + Admin notify

### ⚙️ Settings

- Site name, email, phone
- Social media links
- Google Analytics tracking
- Logo, favicon, banners (drag-to-reorder)

---

## 🧰 Tech Stack

<div align="center">

| Category                               | Technology                                                                                                     |
| -------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| **🔧 Backend**                         | Laravel 12.x · PHP ^8.2                                                                                        |
| **🎨 Frontend**                        | Blade · Bootstrap 5 · AdminLTE 3                                                                               |
| **📦 Bundler**                         | Vite 7 · laravel-vite-plugin                                                                                   |
| **🗄️ Database**                        | MySQL                                                                                                          |
| **🔐 Auth**                            | Laravel Auth · Google 2FA                                                                                      |
| **👮 Authorization**                   | Spatie Laravel-permission                                                                                      |
| **💳 Payments**                        | Pay at event · Razorpay & Stripe (🔒 paid add-on)                                                              |
| **📱 QR Code Scan to mark attendence** | QR Code Scan to mark attendence, On-site instant check-in via UUID, email, phone or name scan (🔒 paid add-on) |
| **📧 Notifications**                   | SMTP Mail                                                                                                      |
| **🖼️ Media**                           | Spatie Laravel-medialibrary v11                                                                                |
| **🔍 SEO**                             | ralphjsmit/laravel-seo                                                                                         |
| **📥 Exports**                         | maatwebsite/excel                                                                                              |
| **📄 PDF**                             | laravel-dompdf                                                                                                 |

</div>

---

## 🚀 Installation

```bash
# 1. Clone the repository
composer create-project vfixtechnology/hello-events
```
```bash
cd hello-events
```
```bash
# 2. Install PHP dependencies
composer install
```
```bash
# 3. Install JS dependencies & build
npm install && npm run build
```

# 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

# 5. Configure DB in .env, then migrate & seed
```bash
php artisan migrate --seed
```

# 6. Start development
```bash
php artisan serve
```

>

## 🧪 Default Credentials

```
📧 Email:    admin@example.com
🔑 Password: admin123
```

---

## 🛒 Installation Service & Add-Ons

<div align="center">
  <h3>🚀 Take Your Event Platform to the Next Level</h3>
  <p>The core HelloEvents is <strong>free & open source</strong>. These premium add-ons enhance it further.</p>
</div>

<br/>

<div align="center">

| #   | Service / Add-On                    | Description                                                                                   | Price   | Buy                                                                                                                                               |
| --- | ----------------------------------- | --------------------------------------------------------------------------------------------- | ------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1️⃣  | **🛠️ Installation Service**         | Full server setup, domain configuration, deployment & go-live support                         | **$99** | [![Buy Now](https://img.shields.io/badge/Buy_Now-28a745?style=for-the-badge&logo=shopify&logoColor=fff)](https://rzp.io/rzp/VuNxabV)    |
| 2️⃣  | **💳 Payment Gateway Integration**  | Seamless Stripe or Razorpay integration                                                       | **$99** | [![Buy Now](https://img.shields.io/badge/Buy_Now-28a745?style=for-the-badge&logo=shopify&logoColor=fff)](https://rzp.io/rzp/VuNxabV) |
| 3️⃣  | **📱 QR Code Ticket Scanner**       | QR Code Scan to mark attendence, On-site instant check-in via UUID, email, phone or name scan | **$99** | [![Buy Now](https://img.shields.io/badge/Buy_Now-28a745?style=for-the-badge&logo=shopify&logoColor=fff)](https://rzp.io/rzp/VuNxabV)  |
| 4️⃣  | **🔄 Yearly Support & Maintenance** | Ongoing server monitoring, updates, bug fixes, and technical assistance                       | **$99** | [![Buy Now](https://img.shields.io/badge/Buy_Now-28a745?style=for-the-badge&logo=shopify&logoColor=fff)](https://rzp.io/rzp/VuNxabV)  |

</div>

<br/>

<div align="center">
  <a href="https://razorpay.me/@vfixtechnology">
    <img src="https://img.shields.io/badge/🍺_Buy_me_a_beer-FFDD00?style=for-the-badge&logo=buymeacoffee&logoColor=000" alt="Buy me a beer"/>
  </a>
</div>

---

## 📞 Company

<div align="center">
  <br/>
  <h2>VFIX TECHNOLOGY</h2>
  <br/>
<table>
  <tr>
    <td align="center"><strong>🌐 Website</strong></td>
    <td><a href="https://www.vfixtechnology.com" target="_blank">www.vfixtechnology.com</a></td>
  </tr>
  <tr>
    <td align="center"><strong>📧 Email</strong></td>
    <td><a href="mailto:info@vfixtechnology.com">info@vfixtechnology.com</a></td>
  </tr>
  <tr>
    <td align="center"><strong>📞 Phone</strong></td>
    <td><a href="tel:+918447525204">+91 8447 525 204</a></td>
  </tr>
  <tr>
    <td align="center"><strong>💬 WhatsApp</strong></td>
    <td><a href="https://wa.me/918447525204" target="_blank">+91 8447 525 204</a></td>
  </tr>
</table>
  <br/>
  <p>
    <a href="https://wa.me/918447525204">
      <img src="https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=fff" alt="WhatsApp"/>
    </a>
    &nbsp;
    <a href="mailto:info@vfixtechnology.com">
      <img src="https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=fff" alt="Email"/>
    </a>
  </p>
  <br/>
</div>

---

<div align="center">
  <p>Made with ❤️ by <strong>VFIX TECHNOLOGY</strong></p>
  <p>© 2024 — Present. All rights reserved.</p>
</div>
