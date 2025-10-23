# AcadEasy - Learning Management System

AcadEasy is a web-based learning management system (LMS) designed to connect instructors and learners in a digital environment, similar to platforms like Udemy. This project was built using the Laravel framework.

## Features Implemented

This prototype includes the following features across three user roles (Guest, Learner, Instructor):

#### General & Public Features
- **Homepage Course Catalog:** The main page displays a catalog of all available courses for guests and logged-in users.
- **Course Detail Page:** Publicly accessible pages for each course, showing its title, description, instructor, and price.
- **User Registration with Role Selection:** A single registration form that allows new users to sign up as either a **Learner** or an **Instructor**.
- **User Login/Logout:** Full authentication system.

#### Learner Features
- **Course Enrollment:** Learners can enroll in courses from the course detail page.
- **Learner Dashboard:** A personal dashboard that displays a list of all courses the learner is currently enrolled in.
- **Role Protection:** Learners are prevented from accessing instructor-only pages (like course creation).

#### Instructor Features
- **Instructor Dashboard:** A dedicated dashboard for instructors to view and manage the courses they have created. It also displays a count of enrolled students for each course.
- **Full Course Management (CRUD):**
    - **Create:** Instructors can create new courses via a form, including title, description, price, and a thumbnail image upload.
    - **Read:** View their own courses on their dashboard.
    - **Update:** Edit and update the details of their existing courses.
    - **Delete:** Remove their courses from the platform.
- **Role Protection:** Instructors are prevented from enrolling in courses and can access instructor-only functionality.

## Technology Stack

- **Backend:** Laravel 12
- **Frontend:** Laravel Blade with Tailwind CSS
- **Database:** SQLite (for simple local setup)
- **Containerization** Docker + Docker Compose
- **Authentication:** Laravel Breeze
- **Testing:** PHPUnit (Unit & Feature), Laravel Dusk (Browser)

## Setup (Manual/Non-Docker)

To set up the project on a new development machine, follow these steps:

1.  **Clone the Repository:**
    ```bash
    git clone <your-repository-url>
    cd AcadEasy
    ```

2.  **Install Dependencies:** Install all backend (PHP) and frontend (JS) dependencies.
    ```bash
    composer install
    npm install
    ```

3.  **Configure Environment:**
    **Development (Local)**
    - Create your local environment file:
      ```bash
      cp .env.example .env.dev
      ```
    - Generate the Laravel application key (run once):
      ```bash
      php artisan key:generate
      ```
    - Make sure the environment values are set for local development:
      ```bash
      APP_ENV=local
      APP_DEBUG=true
      DB_CONNECTION=sqlite
      DB_DATABASE=/var/www/html/database/database.sqlite
      VITE_DEV_SERVER_URL=http://localhost:5173
      ```
    **Production**
    - Create your production environment file:
      ```bash
      cp .env.example .env
      ```
    - Generate the Laravel application key (run once):
      ```bash
      php artisan key:generate
      ```
    - Make sure the environment values are set for production server:
      ```bash
      APP_ENV=production
      APP_DEBUG=false
      DB_CONNECTION=mysql
      DB_HOST=mysql
      DB_PORT=3306
      DB_DATABASE=acadeasy
      DB_USERNAME=acadeasy_user
      DB_PASSWORD=secret
      VITE_APP_URL=http://localhost:8000
      VITE_DEV_SERVER_URL=http://localhost:5173
      ```

4.  **Set Up the Database:**
    - This project is configured to use SQLite by default, which requires no extra database software.
    - Simply run the migration command. This will automatically create a `database.sqlite` file in the `database/` directory and build all the necessary tables.
      ```bash
      php artisan migrate
      ```

5.  **Run the Application:**
    - You need to run two commands in two separate terminals.
    - **Terminal 1 (Start the web server):**
      ```bash
      php artisan serve
      ```
    - **Terminal 2 (Start the frontend asset builder):**
      ```bash
      npm run dev
      ```
    - The application will be available at **http://127.0.0.1:8000**.

## Setup (Docker)

You can also run AcadEasy without installing PHP, Composer, or Node.js locally by using Docker.

1.  **Clone the Repository:**
    - Do the same as the manual setup, i.e., clone the repo and enter the project folder.
    ```bash
    git clone <your-repository-url>
    cd AcadEasy
    ```
2.  **Configure Environment:**
    **Development (Local)**
    - Create your local environment file:
      ```bash
      cp .env.example .env.dev
      ```
    - Generate the Laravel application key (run once):
      ```bash
      php artisan key:generate
      ```
    - Make sure the environment values are set for local development:
      ```bash
      APP_ENV=local
      APP_DEBUG=true
      DB_CONNECTION=sqlite
      DB_DATABASE=/var/www/html/database/database.sqlite
      VITE_DEV_SERVER_URL=http://localhost:5173
      ```
      **Production**
    - Create your production environment file:
      ```bash
      cp .env.example .env
      ```
    - Generate the Laravel application key (run once):
      ```bash
      php artisan key:generate
      ```
    - Make sure the environment values are set for production server:
      ```bash
      APP_ENV=production
      APP_DEBUG=false
      DB_CONNECTION=mysql
      DB_HOST=mysql
      DB_PORT=3306
      DB_DATABASE=acadeasy
      DB_USERNAME=acadeasy_user
      DB_PASSWORD=secret
      VITE_APP_URL=http://localhost:8000
      VITE_DEV_SERVER_URL=http://localhost:5173
      ```
4.  **Build and start the Docker containers:**
    ```bash
    docker-compose up --build -d
    ```
5.  **Generate the APP_KEY (run only once)**
    ```bash
    docker-compose exec php bash
    php artisan key:generate
    exit
    ```
6. **Access application:**
    - Laravel server: http://127.0.0.1:8000
    - Vite server: http://127.0.0.1:5173

## Testing the Application

The project comes with its own test-cases.

- **To run all Unit and Feature tests:**
  ```bash
  php artisan test
  ```

- **To run Browser Automation tests:**
  - **Note:** Requires Google Chrome to be installed on your system.
  ```bash
  php artisan dusk
  ```

## Known Bugs
- **Instructor 'Create Course' Button sometimes goes to pages that doesn't exist**
- **Somehow, we forgot to add a button/function to go back to the course catalog (for learner)**
