# Student Management System

A simple web application built with PHP and MySQL that helps students enroll in subjects, book exams, and schedule meetings with professors. Administrators can manage all the system data through easy-to-use forms.

## What This System Does

### For Students
- **Create an account** and log in securely
- **Enroll in subjects** from a list of available courses
- **Book exams** for subjects you're enrolled in
- **Schedule meetings** with professors when you need help
- **View your dashboard** to see all your activities

### For Administrators
- **Manage user accounts** - add, edit, or delete student accounts
- **Manage subjects** - create new courses, update information, or remove them
- **Manage exams** - schedule new exams, change dates, or cancel them
- **Manage meetings** - see all scheduled meetings and their status
- **Admin dashboard** - overview of everything in the system

## Technologies Used

- **PHP** - Backend programming language (what makes the website work)
- **MySQL** - Database to store all the information
- **HTML/CSS** - Frontend design and layout
- **JavaScript** - Makes the website interactive
- **Sessions** - Keeps users logged in securely

## How the Project is Organized

```
student_management/
├── index.php                 # Main login page
├── signup.php               # Where students create accounts
├── dashboard.php            # Student's main page
├── subjects.php             # Page to enroll in subjects
├── exams.php                # Page to book exams
├── meetings.php             # Page to schedule meetings
├── logout.php               # Logs users out
├── database.sql             # Database structure
├── README.md                # This file
│
├── core/                    # Important system files
│   ├── config.php          # Database connection settings
│   ├── init.php            # Starts database and sessions
│   └── functions.php       # Helper functions
│
├── admin/                   # Admin-only pages
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin main page
│   ├── manage_users.php    # Manage student accounts
│   ├── manage_subjects.php # Manage courses
│   ├── manage_exams.php    # Manage exam schedules
│   ├── manage_meetings.php # Manage meetings
│   └── logout.php          # Admin logout
│
├── assets/                  # Styling files
│   ├── admin-styles.css    # Admin page design
│   └── user-styles.css     # Student page design
│
└── templates/               # Reusable page parts
    ├── header.php          # Navigation menu
    └── footer.php          # Page bottom
```

## Database Tables

### What Information is Stored
- **accounts** - Student and admin user accounts
- **subjects** - Available courses students can enroll in
- **enrollments** - Which students are enrolled in which subjects
- **exams** - Exam schedules and information
- **exam_bookings** - Which students booked which exams
- **meetings** - Student-professor meeting appointments
- **professors** - Professor information

### How Things Connect
- Students can enroll in multiple subjects
- Students can only book exams for subjects they're enrolled in
- Students can schedule meetings with professors
- Admins can see and manage everything

## Security Features

- **Separate login areas** - Students and admins have different login pages
- **Session management** - Users stay logged in while using the website
- **Password protection** - All passwords are securely stored
- **Input validation** - Checks that users enter correct information
- **Access control** - Students can't access admin pages

## How to Set Up and Run

### What You Need
- **XAMPP** or similar (includes PHP and MySQL)
- **Web browser** (Chrome, Firefox, etc.)
- **Text editor** (Notepad++, VS Code, etc.)

### Step-by-Step Setup
1. **Download** the project files to your computer
2. **Put files** in your XAMPP htdocs folder
3. **Start XAMPP** - turn on Apache and MySQL
4. **Create database**:
   - Open phpMyAdmin
   - Create new database called `student_management`
   - Import the `database.sql` file
5. **Update settings**:
   - Edit `core/config.php` with your database details
6. **Test the system**:
   - Go to `http://localhost/student_management/`
   - Try logging in as admin (username: `admin`, password: `admin123`)

## How to Use the System

### For Students
1. **Go to the main page** and click "Sign up"
2. **Create your account** with your name, username, and password
3. **Log in** with your new account
4. **Enroll in subjects** from the Subjects page
5. **Book exams** for subjects you're enrolled in
6. **Schedule meetings** with professors when needed

### For Administrators
1. **Go to admin login** at `/admin/login.php`
2. **Log in** with admin credentials
3. **Manage users** - add new students, update information
4. **Manage subjects** - create new courses, update details
5. **Manage exams** - schedule new exams, change times
6. **Manage meetings** - see all appointments and their status

## How the Code is Organized

### Database Design
- **Simple structure** - easy to understand and modify
- **Proper relationships** - data is connected logically
- **Safe operations** - uses prepared statements to prevent errors

### Code Structure
- **Organized files** - each file has a specific purpose
- **Reusable parts** - header and footer used on all pages
- **Clear names** - file and function names explain what they do
- **Error handling** - system handles problems gracefully

## Design Features

- **Responsive design** - works on computers, tablets, and phones
- **Modern layout** - uses CSS Flexbox for clean arrangements
- **Professional look** - admin area has blue theme, student area is clean
- **Easy navigation** - clear menus and buttons
- **Consistent style** - all pages look and feel the same

## What I Learned from This Project

### Technical Skills
- **PHP programming** - how to build web applications
- **Database design** - creating and connecting data tables
- **Web development** - HTML, CSS, and JavaScript together
- **Security basics** - protecting user data and accounts
- **User authentication** - login and logout functionality

### Development Skills
- **Project organization** - how to structure a web application
- **Code organization** - writing clean, readable code
- **Problem solving** - fixing bugs and improving functionality
- **Testing** - making sure everything works correctly
- **Documentation** - explaining how the system works

## Testing the System

### What I Tested
- ✅ **User registration** - students can create accounts
- ✅ **Login system** - both students and admins can log in
- ✅ **Subject enrollment** - students can enroll in courses
- ✅ **Exam booking** - students can book exams
- ✅ **Meeting scheduling** - students can schedule meetings
- ✅ **Admin functions** - all management features work
- 

### How to Test
1. **Create a student account** and try all student features
2. **Log in as admin** and try all admin features
3. **Test on different devices** - computer, phone, tablet
4. **Try different scenarios** - enroll in subjects, book exams, etc.

## Future Improvements

If I had more time, I could add:
- **Email notifications** when meetings or exams are scheduled
- **Calendar view** to see all appointments
- **File uploads** for assignments
- **Grade tracking** system
- **Better mobile design** for phones
- **More admin reports** and statistics

## About This Project

- **Course**: Web Programming/Web Development
- **Level**: Intermediate student project
- **Purpose**: Learn full-stack web development
- **Technologies**: PHP, MySQL, HTML, CSS, JavaScript
- **Focus**: Building a complete, working system

## Important Notes

- **This is a learning project** - not for production use
- **All passwords are simple** - admin/admin123 for testing
- **Database is included** - just import the SQL file
- **Code is commented** - explains what each part does
- **System is functional** - all features work as intended

---

**This Student Management System demonstrates my ability to:**
- Build a complete web application
- Design and use databases
- Implement user authentication
- Create responsive web designs
- Write clean, organized code
- Document a project properly

The system is for university project and shows practical web development skills at a student level.

