# Event Booking API

A Laravel-based API for managing events, tickets, and bookings with **role-based access**, notifications, queues, and caching.

---

## **Table of Contents**
- [Installation](#installation)
- [Setup](#setup)
- [Running the Project](#running-the-project)
- [API Endpoints](#api-endpoints)
  - [Authentication](#authentication)
  - [Events](#events)
  - [Tickets & Bookings](#tickets--bookings)
- [Postman Collection](#postman-collection)
- [Testing](#testing)

---

## **Installation**

Clone the repository:

```bash
git clone <your-repo-url>
cd <project-folder>

Install dependencies:
composer install
npm install
Copy .env.example to .env and configure your database:
cp .env.example .env
Generate app key:
php artisan key:generate

Setup

Run migrations and seeders:

(Optional) Publish queue tables:
php artisan queue:table
php artisan migrate

php artisan queue:work


Email Configuration

To send booking confirmation emails, configure your SMTP settings in .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io        # Example for Mailtrap (dev/test)
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Event Booking API"


Notes:

For local testing, you can use Mailtrap
 or MailHog
.

For production, use your email provider SMTP credentials (Gmail, SendGrid, etc.).

Make sure php artisan queue:work is running to process email notifications queued by Laravel.

Running the Project

Start the Laravel development server:
php artisan serve
API Endpoints
Authentication

Register POST /api/register

Login POST /api/login

Logout POST /api/logout

Notes:

register requires: name, email, password, password_confirmation, role (customer, organizer, admin).

login returns a token for authentication.

Events

Get Events GET /api/events
Optional filters: search, date, location, per_page.

Create Event POST /api/events (Organizer only)

Update Event PUT /api/events/{id} (Organizer only)

Delete Event DELETE /api/events/{id} (Organizer only)
Tickets & Bookings (Customer)

Book Ticket POST /api/tickets/{id}/bookings
Body: { "quantity": 2 }

Get My Bookings GET /api/bookings

Cancel Booking PUT /api/bookings/{id}/cancel

Note: Double booking the same ticket is prevented by middleware.

Testing

Run feature tests:

php artisan test --testsuite=Feature
User registration & login

Event creation (organizer)

Ticket booking (customer)

Double booking prevention

Queue Worker: Make sure php artisan queue:work is running to process notifications.

Notes

Roles: admin, organizer, customer.

Notifications are queued using Laravel’s queue system.

Frequently accessed events are cached for performance.
