# Social Network App (Symfony)

A social network application built with Symfony where users can register, create publications, follow other users, like posts and interact through private messages and notifications.

---

## Features

- User registration and login
- Publications (text, images, documents)
- Edit user
- Likes system
- Followers system
- Private messages
- Notifications
- Pagination

---

## Technologies

- PHP
- Symfony
- Doctrine ORM
- MySQL
- Twig
- Bootstrap
- CSS
- jQuery / AJAX

---

## Installation

Clone the repository:

```bash
git clone https://github.com/gregoriomesafernandez-star/social-network-symfony.git
cd social-network-symfony
composer install
```

## Environment configuration

Open the .env file and set your own application secret:

```env
APP_SECRET=your_own_secret_key
```

You can generate a secure key using:

php -r "echo bin2hex(random_bytes(32));"

Copy the generated value and replace your_own_secret_key.

---

## Database setup

1. Configure the database

Open the .env file and update the DATABASE_URL with your MySQL or MariaDB credentials:

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/database_name?serverVersion=10.4&charset=utf8mb4"
```

Replace:

- user with your database username.
- password with your database password.
- database_name with your database name.
- serverVersion with your installed MySQL or MariaDB version.

For example:

MySQL 8.0: serverVersion=8.0
MariaDB 10.4: serverVersion=10.4

2. Create the database and load the data

Run the following commands:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

Open http://127.0.0.1:8000 in your browser.

Note: The fixtures command may remove existing database records before loading sample data. Use it with a fresh development database.

---

## Demo Data

The project includes sample users and content for testing purposes.

---

## Demo Users

You can use the following demo accounts:

- Email: user1@test.com  
  Password: 123456  
---

## Usage

You can register a new user to explore the application:

- Create publications
- Follow users
- Like posts
- Send private messages

---

## Author

Gregorio Mesa

---
