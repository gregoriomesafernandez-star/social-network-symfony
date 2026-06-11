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

Configure the database in the `.env` file:

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/database_name"
```

Run database setup:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

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

You can register a new user or use the demo data to explore the application:

- Create publications
- Follow users
- Like posts
- Send private messages

---

## Author

Gregorio Mesa

---
