# Project Plan — Task Manager (TODO App)

## 1. Initial Time Estimates & Task List

Estimated Total Effort: 10 hours

| Task | Estimated Time |
| :--- | :--- |
| **Step 1:** Initial setup (Laravel 13, MySQL, Breeze) | 1.5 h |
| **Step 2:** Database schema, Eloquent models, Seeders & Factories | 1.5 h |
| **Step 3:** Task CRUD (Controllers, Form Requests, Blade Views with TailWind) | 3.0 h |
| **Step 4:** Image upload & Storage handling (validation, replacement, deletion) | 1.5 h |
| **Step 5:** Authorization & Security (Task Policy, server-side checks) | 1.0 h |
| **Step 6:** Final testing, README documentation & Retrospective | 1.5 h |

---

## 2. Database Schema

### `users` Table
- `id` (BIGINT, PK, Auto_Increment)
- `name` (VARCHAR(100))
- `email` (VARCHAR(150), Unique)
- `password` (VARCHAR(255))


### `tasks` Table
- `id` (BIGINT, PK, Auto_Increment)
- `user_id` (BIGINT, FK -> `users.id`, onDelete cascade)
- `title` (VARCHAR(255), required)
- `description` (Text, NULLABLE)
- `status` (Enum (`pending`, `in_progress`, `completed`), default: `pending`)
- `due_date` (Date, NULLABLE)
- `image` (VARCHAR(255), NULLABLE)


---

## 3. Application Routes

| Method | URI | Action | Middleware / Security |
| :--- | :--- | :--- | :--- |
| `GET` | `/login`, `/register` | Auth Views | `guest` |
| `GET` | `/tasks` | `TaskController@index` | `auth` |
| `GET` | `/tasks/create` | `TaskController@create` | `auth` |
| `POST` | `/tasks` | `TaskController@store` | `auth` |
| `GET` | `/tasks/{task}` | `TaskController@show` | `auth`, `can:view,task` |
| `GET` | `/tasks/{task}/edit` | `TaskController@edit` | `auth`, `can:update,task` |
| `PUT/PATCH` | `/tasks/{task}` | `TaskController@update` | `auth`, `can:update,task` |
| `DELETE` | `/tasks/{task}` | `TaskController@destroy` | `auth`, `can:delete,task` |

---

## 4. Retrospective

...