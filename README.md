# Multi-Tenant Inspection Booking System

A modular Laravel-based SaaS system allowing companies (tenants) to manage inspection teams, define weekly availability, generate dynamic time slots, and handle bookings.

---

## Features

* Multi-tenancy with tenant-scoped data
* User authentication with Laravel Sanctum
* Teams and recurring weekly availability
* Dynamic 1-hour slot generation (on-the-fly)
* Booking system with conflict prevention
* Modular HMVC structure
* Optional React frontend for viewing teams, availability, and booking slots

---

## Tech Stack

* Laravel 12
* PHP 8+
* MySQL/PostgreSQL
* Laravel Sanctum for API authentication
* Spatie Laravel Multi-Tenancy
* Nwidart Laravel Modules for HMVC
* React (optional frontend)

---

## Setup Instructions

1. **Clone the repository**

   ```
   git clone <your-repo-url>
   cd <your-repo-folder>
   ```

2. **Install PHP dependencies**

   ```
   composer install
   ```

3. **Install Node dependencies (if using React frontend)**

   ```
   npm install
   ```

4. **Create `.env` file**

   ```
   cp .env.example .env
   ```

   * Set your database credentials
   * Set `SANCTUM_STATEFUL_DOMAINS` if using React
   * Set `APP_URL` and other keys as needed

5. **Generate application key**

   ```
   php artisan key:generate
   ```

6. **Run migrations and seeders**

   ```
   php artisan migrate
   ```

7. **Serve the application**

   ```
   php artisan serve
   ```

   Access API via `http://127.0.0.1:8000/api/v1`

---

## API Usage

### **Authentication**

| Method | Endpoint              | Description                      |
| ------ | --------------------- | -------------------------------- |
| POST   | /api/v1/auth/register | Register a tenant and admin user |
| POST   | /api/v1/auth/login    | Log in and receive token         |
| POST   | /api/v1/auth/logout   | Logout authenticated user        |

### **Teams**

| Method | Endpoint                                                         | Description                                                          |
| ------ | ---------------------------------------------------------------- | -------------------------------------------------------------------- |
| GET    | /api/v1/teams                                                    | List all teams for the tenant                                        |
| POST   | /api/v1/teams                                                    | Create a new team                                                    |
| POST   | /api/v1/teams/{id}/availability                                  | Set recurring weekly availability for a team                         |
| GET    | /api/v1/teams/{id}/generate-slots?from=YYYY-MM-DD\&to=YYYY-MM-DD | Generate dynamic 1-hour slots based on availability and booked slots |

### **Bookings**

| Method | Endpoint              | Description                                                        |
| ------ | --------------------- | ------------------------------------------------------------------ |
| GET    | /api/v1/bookings      | List bookings for authenticated user                               |
| POST   | /api/v1/bookings      | Book a slot (requires `team_id`, `date`, `start_time`, `end_time`) |
| DELETE | /api/v1/bookings/{id} | Cancel a booking                                                   |

> **Tip:** Use Postman or any API client to test the endpoints. Attach your Bearer token from login to access protected routes.

---
## Multi-Tenancy Comparison

| Feature                | Current Manual Approach         | Spatie Full Integration                       |
| ---------------------- | ------------------------------- | --------------------------------------------- |
| Data Isolation         | ✅ `tenant_id` checks in queries | ✅ Automatic scoping by tenant context       |
| Middleware             | ✅ Custom middleware for auth    | ✅ Spatie middleware handles tenant resolution |
| Database Switching     | ❌ Not needed for single DB      | ✅ Optional if you scale to multiple DBs     |
| Auto-scoped Models     | ❌ You write queries manually    | ✅ Models auto-filter by tenant              |
| Tenant Lifecycle Hooks | ❌ You do it manually            | ✅ Built-in hooks for creating defaults      |

> **Note:** At the current scale with a single database, the manual `tenant_id` scoping approach is sufficient. Spatie is installed but its advanced features are not actively used yet.


## Multi-Tenancy Notes

* Each user is scoped to a tenant via `tenant_id`.
* Queries for teams, bookings, and availability automatically filter by `tenant_id`.
* Users cannot access data from other tenants.
* Spatie Multi-Tenancy package is used for scoping and isolation.

---

## Dynamic Slot Generation

* Slots are **not stored in the database**.
* Generated based on the team's **weekly recurring availability**.
* Duration: **1 hour per slot** (configurable if needed).
* Already booked slots are excluded automatically.
* Conflict prevention ensures overlapping bookings are not allowed.

---


## Notes

* All controllers use **FormRequests** for validation.
* HMVC structure is used:

```
/Modules
  /Auth
  /Tenants
  /Teams
  /Availability
  /Bookings
  /Users
```

* Slot generation logic is extracted into a **service class** for reusability.
* Database migrations and seeders included for easy setup.

---

## Postman Collection

You can import this Postman collection to test all API endpoints:
[Bookings Saas.postman_collection.json](https://github.com/user-attachments/files/21779384/Bookings.Saas.postman_collection.json)
Or you can access it directly from Postman: [Public Link](https://postman.co/workspace/Personal-Workspace~ad6f339b-721b-4d7a-a9a9-360026c674c9/collection/30942164-f135220c-b77a-4fa7-a8e7-70d968fa038b?action=share&creator=30942164)


