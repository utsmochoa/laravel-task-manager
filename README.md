# Task Manager (TODO App)

A clean, robust Task Management web application built with Laravel 13, Tailwind CSS, and MySQL. This application allows authenticated users to manage their daily tasks, upload attachments, and maintain full authorization control over their data.

Developed as part of the Technical Test for the Laravel Developer position.

---

## Requirements

Ensure your local development environment meets the following requirements:

- **PHP:** `>= 8.3`
- **Composer:** `>= 2.0`
- **Node.js:** `>= 18.x` & **npm**
- **Database:** MySQL `>= 8.0` (or MariaDB)
- **Web Server / Local Env:** Laragon (Recommended), Herd, or XAMPP

---

## Installation & Setup Step-by-Step

Follow these steps to set up and run the project locally from a fresh clone.

### 1. Clone the Repository

```bash
git clone https://github.com/utsmochoa/laravel-task-manager.git
cd laravel-task-manager
```

### 2. Install Dependencies

Install PHP dependencies via Composer and Node package manager:

```bash
composer install
npm install
```

### 3. Environment Configuration

Copy the `.env.example` file to create your main environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Database Setup

Update your `.env` file with your MySQL credentials and target database name:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_task
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations & Database Seeders

Execute the database migrations along with seeders to create initial test data:

```bash
php artisan migrate --seed
```

> **Note:** If the database specified in `.env` (`task_manager`) does not exist yet, Laravel will prompt you asking if you would like to create it. Press **Enter** (or type `yes`) to let Laravel create the database automatically and run the migrations.

### 6. Create Storage Symlink (Crucial for Image Uploads)

Link the public storage directory to enable viewing uploaded task images:

```bash
php artisan storage:link
```

### 7. Compile Assets & Start the Server

Compile frontend assets for production:

```bash
npm run build
```

Start the Laravel development server:

```bash
php artisan serve
```

Access the application at `http://127.0.0.1:8000`.

---

## Demo Credentials

The database seeder automatically creates a default user with sample tasks:

- **Email:** `test@example.com`
- **Password:** `password`

You can also register new accounts directly through the UI.

---

## Assumptions

During the development process, the following assumptions and decisions were made:

1. **Storage Link Obligation:** Uploaded task attachments rely on `php artisan storage:link` to create a symbolic link between `storage/app/public` and `public/storage`.

2. **Strict Single-User Access:** Tasks belong strictly to the user who created them. Users cannot view, edit, or delete tasks belonging to other accounts.

3. **Image Constraints:** Supported attachment formats are strictly validated (`jpg`, `jpeg`, `png`, `webp`) up to a maximum file size of 2048 KB (2 MB). Replacing or deleting a task automatically purges the previous image file from physical storage to prevent dead files.

4. **Dashboard and `tasks.index`:** `/dashboard` (route name `dashboard`) is the main task list and is handled by `TaskController@index`. The resource route `tasks.index` (`/tasks`) is also registered by `Route::resource` and renders the same view, but it is not the primary entry point: after creating, updating or deleting a task the user is redirected to `dashboard` with a flash confirmation message.

5. **No Soft Deletes by Default:** Task deletion is permanent along with its associated image file.

---

## Technical Decisions & Justifications

- **Database (MySQL):** Selected over SQLite due to deeper familiarity and hands-on experience with MySQL relational structures, query performance, and indexing behavior in production-like local setups (Laragon).

- **Authentication (Laravel Breeze):** Blade-based authentication scaffolding was chosen to provide a lightweight, secure foundation covering registration, login, session management, and password hashing without introducing unnecessary SPA complexity.

- **Authorization (Laravel Policy):** `TaskPolicy` (`app/Policies/TaskPolicy.php`) is not registered manually: Laravel auto-discovers it by naming convention (`App\Models\Task` -> `App\Policies\TaskPolicy`). It defines only the abilities the app uses (`view`, `update`, `delete`) and is enforced with `Gate::authorize()` in the `show`, `edit`, `update` and `destroy` controller actions. Other users' tasks return a 403, regardless of UI button visibility.

- **Form Requests:** Dedicated `StoreTaskRequest` and `UpdateTaskRequest` classes were created to keep validation rules decoupled from controller logic (respecting strict MVC principles).

---

## Testing

Automated tests use **PHPUnit**. They cover:

- **Authentication (Breeze tests):** login, registration, password flows and profile.
- **Access control:** guests are redirected from task routes, and a user cannot view, edit, update or delete another user's task (403, data unchanged).
- **Task CRUD (`tests/Feature/TaskTest.php`):** creating, updating and deleting an own task, asserting the redirect ends successfully and shows the confirmation message.
- **Image handling (with `Storage::fake()`):** replacing an image deletes the previous file, and deleting a task deletes its image file.
- **Validation:** `due_date` is rejected when it is in the past on creation.

Not covered by automated tests: filtering/search (not implemented), pagination and the Blade UI/styling.

To run the test suite:

```bash
php artisan test
```

---

## Optional Features Implemented

None. Only the base requirements were implemented; the optional features that were considered are listed below as future improvements.

---

## Known Limitations & Future Improvements

- **Filter & Search Enhancements:** Adding real-time status filtering (Pending, In Progress, Completed) and search input for large task lists.

- **Multiple Attachments:** Extending the image relationship to `hasMany` to allow uploading multiple reference photos per task.

- **Notifications:** Implementing email reminders for tasks approaching their `due_date`.