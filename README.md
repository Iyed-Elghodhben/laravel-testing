# Event Booking API

A Laravel-based API for managing events, tickets, bookings, and payments with **role-based access**, notifications, queues, caching, and unit testing.

---

## **Table of Contents**
- [Installation](#installation)
- [Setup](#setup)
- [Running the Project](#running-the-project)
- [API Endpoints](#api-endpoints)
  - [Authentication](#authentication)
  - [Events](#events)
  - [Tickets & Bookings](#tickets--bookings)
  - [Payments](#payments)
- [Postman Collection](#postman-collection)
- [Testing](#testing)

---

## Environment / System Requirements

- **PHP:** 8.2.12 (CLI)  
- **Composer:** 2.8.12  
- **Laravel Framework:** 12.37.0  

---

## **Installation**

Clone the repository:

```bash
git clone <your-repo-url>
cd <project-folder>
Install dependencies:

bash
Copier le code
composer install
npm install
Copy .env.example to .env and configure your database:

bash
Copier le code
cp .env.example .env
php artisan key:generate
Setup
Run migrations and seeders:

bash
Copier le code
php artisan migrate --seed
(Optional) Publish queue tables:

bash
Copier le code
php artisan queue:table
php artisan migrate
php artisan queue:work
Email Configuration
To send booking confirmation emails, configure SMTP in .env:

ini
Copier le code
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io        # Example for Mailtrap
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Event Booking API"
Running the Project
Start the Laravel server:

bash
Copier le code
php artisan serve
API Endpoints
Authentication
Register: POST /api/register
Required: name, email, password, password_confirmation, role (customer, organizer, admin)

Login: POST /api/login
Returns token for authentication

Logout: POST /api/logout

Events
Get Events: GET /api/events
Optional filters: search, date, location, per_page
Uses CommonQueryScopes trait with filterByDate() and searchByTitle()

Create Event: POST /api/events (Organizer only)

Update Event: PUT /api/events/{id} (Organizer only)

Delete Event: DELETE /api/events/{id} (Organizer only)

Tickets & Bookings (Customer)
Book Ticket: POST /api/tickets/{id}/bookings
Body: { "quantity": 2 }

Get My Bookings: GET /api/bookings

Cancel Booking: PUT /api/bookings/{id}/cancel

Note: Middleware prevents double booking of the same ticket.

Payments
Pay for Booking: POST /api/bookings/{booking_id}/payment
Body example:

json
Copier le code
{
  "booking_id": 1,
  "amount": 150.50
}
Handled via PaymentService

Validates booking_id exists and amount is numeric

Updates booking status to confirmed

Stores payment record with status = confirmed

View Payment Details: GET /api/payments/{payment_id}

Testing

Run feature tests:

php artisan test --testsuite=Feature


Includes:

User registration & login

Event creation (organizer)

Ticket booking (customer)

Double booking prevention

Payment processing

Viewing payment details

Queue Worker: Make sure php artisan queue:work is running to process notifications

Run unit tests for PaymentService:

php artisan test --testsuite=Unit
