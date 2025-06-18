# Task Management API

A Laravel-based REST API for managing tasks with user roles and authentication.

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend if needed)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd task-management-api
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

## Setup and Optimization

Before running the application, run these commands to optimize performance:

1. Clear all caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

2. Cache configurations and routes for better performance:
```bash
php artisan config:cache
php artisan route:cache
```

3. Seed the database with initial data:
```bash
php artisan db:seed
```

This will create:
- Admin user (email: admin@admin.com)
- Regular user role
- Admin role

## API Documentation

The API documentation is available in Swagger format. 

The Swagger documentation is also available in the project at `swagger.yaml`.

## Running the Application

Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

## API Endpoints

### Authentication
- POST `/api/register` - Register a new user
- POST `/api/login` - Login user

### Tasks (Requires Authentication)
- GET `/api/tasks/getTasksByUserId/{user_id}` - Get tasks by user ID
- POST `/api/tasks/addTasks` - Create a new task
- PUT `/api/tasks/updateTask/{task_id}` - Update a task
- POST `/api/tasks/reorder` - Reorder tasks

### Admin Only
- GET `/api/tasks/getTasks` - Get all tasks
- GET `/api/user/lists` - Get all non-admin users

## Testing

Run the test suite:
```bash
php artisan test
```

## Maintenance

The application includes a maintenance command to clean up old tasks:
```bash
php artisan tasks:delete-old
```

This command is scheduled to run daily and deletes tasks that have been in the trash for more than 30 days.

## Security

- Uses Laravel Sanctum for API authentication
- Implements role-based access control
- Uses soft deletes for data recovery
- Implements proper validation and error handling

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License.