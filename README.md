# AcadEasy - Marketplace-style Learning Management System

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
- **Authentication:** Laravel Breeze
- **Testing:** PHPUnit (Unit & Feature), Laravel Dusk (Browser)

## Setup

To set up the project on a new development machine, follow these steps:

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/yandhk/SecProg
    cd SecProg
    ```

2.  **Install Dependencies:** Install all backend (PHP) and frontend (JS) dependencies.
    ```bash
    composer install
    npm install
    ```

3.  **Configure Environment:**
    - Create your local environment file:
      ```bash
      cp .env.example .env
      ```
    - Generate a unique application key:
      ```bash
      php artisan key:generate
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

## Known Bugs & ongoing implementation
- **Instructor 'Create Course' Button sometimes goes to pages that doesn't exist**
- **Somehow, we forgot to add a button/function to go back to the course catalog (for learner)**
- **Docker image is still in testing phase**
