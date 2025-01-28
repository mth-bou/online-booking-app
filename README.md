# 🍽️ Online Booking App

## 📖 About the Project

**Online Booking App** is a restaurant reservation system built with **Symfony** following the **hexagonal architecture** for better maintainability, scalability, and flexibility. The application allows users to reserve meals in advance, while restaurant administrators can manage bookings efficiently.

## ✨ Features

- 📅 **Meal Reservations** – Book meals at restaurants with real-time availability.
- 🔐 **Secure Booking & Confirmation** – Ensures reliability and fraud prevention.
- 📊 **Admin Dashboard** – Manage reservations, tables, and availability.
- 📍 **Restaurant Search & Filtering** – Users can find and book restaurants based on location, cuisine, and availability.
- 📢 **Notifications & Reminders** – Automatic alerts for upcoming bookings.
- 🏗 **Hexagonal Architecture** – Ensuring modularity, testability, and clean separation of concerns.

## 🏗 Tech Stack

- **Backend:** Symfony 7.2 (PHP 8.2+)
- **Architecture:** Hexagonal
- **Database:** PostgreSQL / MySQL
- **Authentication:** JWT (LexikJWTAuthenticationBundle)
- **API:** RESTful API using Symfony API Platform
- **Frontend:** API-ready (compatible with React, Vue, Angular)
- **Testing:** PHPUnit / Behat
- **Deployment:** Docker / Kubernetes

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- PHP 8.2+
- Composer
- Symfony CLI
- Docker (optional for containerized development)

### Installation

1. Clone the repository:

   ```sh
   git clone https://github.com/yourusername/online-booking-app.git
   cd online-booking-app
   ```

2. Install dependencies:

   ```sh
   composer install
   ```

3. Set up the environment variables:

   ```sh
   cp .env.example .env
   ```

   Update `.env` with your database credentials.

4. Set up the database:

   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```

5. Run the development server:

   ```sh
   symfony server:start
   ```

## 🏗 Architecture Overview

The application follows a **hexagonal architecture**, dividing the system into:

- **Domain Layer:** Business logic, entities, and domain services.
- **Application Layer:** Use cases and DTOs.
- **Infrastructure Layer:** Database, repositories, external services, and controllers.

This structure ensures a **clean separation of concerns** and facilitates **testing** and **maintainability**.

## 📌 Roadmap

-

## 🤝 Contributing

Contributions are welcome! Feel free to fork the repository and submit a pull request.

## 📜 License

This project is licensed under the **MIT License**.

---

🚀 **Efficient, scalable, and easy to extend.** Enjoy coding! 😊

