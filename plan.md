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

### Time Comparison (Estimated vs. Actual)

| Step / Feature | Estimated Time | Actual Time | Difference |
| :--- | :--- | :--- | :--- |
| **Step 1:** Initial setup (Laravel 13, MySQL, Breeze) | 1.5 h | 2.0 h | +0.5 h |
| **Step 2:** Database schema, Eloquent models, Seeders & Factories | 1.5 h | 1.0 h | -0.5 h |
| **Step 3:** Task CRUD (Controllers, Form Requests, Blade Views with Tailwind) | 3.0 h | 7.0 h | +4.0 h |
| **Step 4:** Image upload & Storage handling | 1.5 h | 2.0 h | +0.5 h |
| **Step 5:** Authorization & Security (Task Policy, PHPUnit Tests) | 1.0 h | 1.0 h | 0.0 h |
| **Step 6:** Final testing, clean install validation & README documentation | 1.5 h | 1.5 h | 0.0 h |
| **TOTAL** | **10.0 h** | **14.5 h** | **+4.5 h** |

> **Development Timeline Note:** Due to daily work schedule constraints and severe power grid instability (experiencing over 6 hours of daily power outages), the total development time of 14.5 hours was completed across a span of **4 calendar days**.

---

### Retrospective Analysis & Key Findings

1. **Environment Setup (Step 1):**
   * *Challenge:* Initial setup took 2 hours instead of 1.5 hours due to PHP and extension version incompatibilities while attempting to run Laravel 13 on XAMPP.
   * *Resolution:* Researched alternative local development environments and migrated the project to Laragon, which provided seamless PHP 8.3+ integration and resolved runtime conflicts.

2. **Database Layer (Step 2):**
   * *Outcome:* Completed ahead of schedule (1.0 h). Configuring Eloquent relationships, foreign key cascade rules, factories, and seeders went smoothly without roadblocks.

3. **CRUD & Frontend Development (Step 3):**
   * *Challenge:* This step took significantly longer than estimated (7.0 h vs 3.0 h).
   * *Reason:* Because my current daily employment is unrelated to active software development, I experienced initial rustiness with Laravel syntax and Tailwind UI layout details. Re-familiarizing myself with Blade components, form state retention, and controller mechanics required additional iteration time.

4. **Image Management (Step 4):**
   * *Challenge:* Uploaded images initially failed to render in the browser.
   * *Resolution:* Diagnosed that the symbolic link had not been established. Running `php artisan storage:link` immediately resolved the asset serving issue.

5. **Authorization & Automated Testing (Step 5):**
   * *Outcome:* Completed right on schedule (1.0 h).
   * *Key Learning:* Writing PHPUnit test cases proved invaluable. An initial authorization test failed, exposing a security flaw where unauthorized users could access other users' task details. Fixing the `TaskPolicy` registration and adding `Gate::authorize()` sealed this vulnerability.

6. **Clean Installation Validation (Step 6):**
   * *Outcome:* Verified the end-to-end setup in a completely clean directory. Cloned the repository, ran composer/npm installs, migrated seeders, linked storage, and verified that all core flows and policy checks functioned perfectly.

7. **AI Tool Assistance Disclaimer:**
   * AI tools were utilized as an assistant during development to accelerate syntax lookups and UI structure. However, all generated logic was thoroughly analyzed, refactored, and adapted to fit Laravel conventions. I maintain full comprehension of the submitted codebase and can explain every implementation detail during the technical review call.