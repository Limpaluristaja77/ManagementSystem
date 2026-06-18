
## Setup After Git Clone

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Copy the environment file:

```bash
cp .env.example .env
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

## Database Setup

Create a MySQL database, for example:

```sql
CREATE DATABASE management_system;
```

Then update these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=management_system
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed demo data:

```bash
php artisan migrate:fresh --seed
```

## Start The Project

Start the Laravel backend:

```bash
composer run dev
```

Open the app in your browser:

```text
http://127.0.0.1:8000
```

## Demo Accounts

The seeder creates these users:

```text
Superadmin: superadmin@gmail.com / password
Manager:    manager@gmail.com / password
User:       user@gmail.com / password
```

## Main Features

- User authentication
- Superadmin-only Users and Roles pages
- Create users and assign roles
- Edit user name and role
- Activate/deactivate users
- Deactivated users cannot log in
- Roles and permissions using Spatie Laravel Permission
- Daily attendance check-in/check-out
- Manager attendance approval/rejection
- Mandatory reason for attendance changes
- Attendance audit trail
