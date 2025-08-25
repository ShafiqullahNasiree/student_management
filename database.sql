-- Student Management System Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Accounts table (was users) - only for students and admins
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    account_type ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Professors table - fixed list, cannot log in
CREATE TABLE professors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table (was courses)
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(10) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Meetings table (was appointments)
CREATE TABLE meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    professor_id INT NOT NULL,
    meeting_datetime DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professors(id) ON DELETE CASCADE
);

-- Exams table (no change)
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(100) NOT NULL,
    subject_id INT NOT NULL,
    exam_date DATE NOT NULL,
    exam_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Student enrollments table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, subject_id)
);

-- Exam bookings table
CREATE TABLE exam_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    exam_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    UNIQUE KEY unique_exam_booking (student_id, exam_id)
);

-- Insert sample data

-- Sample accounts (only students and admins)
INSERT INTO accounts (fullname, username, email, password, account_type) VALUES
('Khan', 'khan', 'khan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- password: password
('John Doe', 'john', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Jane Smith', 'jane', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample professors (fixed list, cannot log in)
INSERT INTO professors (fullname, email, department) VALUES
('Prof. Antonio Celesti', 'celesti@university.com', 'Computer Science'),
('Prof. Giacomo Fiumara', 'fiumara@university.com', 'Computer Science'),
('Prof. Andrea Mandanici', 'mandanici@university.com', 'Computer Science'),
('Prof. Massimo Villari', 'villari@university.com', 'Computer Science'),
('Prof. Armando Ruggeri', 'ruggeri@university.com', 'Computer Science'),
('Prof. Sebastiano Vasi', 'vasi@university.com', 'Computer Science');

-- Sample subjects
INSERT INTO subjects (subject_code, subject_name, description) VALUES
('CS101', 'Web Development', 'Learn HTML, CSS, JavaScript and modern web technologies'),
('CS201', 'Data Structures', 'Advanced data structures and algorithms implementation'),
('CS301', 'Algorithms', 'Algorithm design and analysis techniques'),
('CS401', 'Operating Systems', 'OS concepts, processes, memory management'),
('CS501', 'Computer Networks', 'Network protocols, architecture, and security'),
('CS601', 'Database Systems', 'Database design, SQL, and data management'),
('CS701', 'Software Engineering', 'Software development lifecycle and methodologies');

-- Sample exams (updated to use subject_id)
INSERT INTO exams (exam_name, subject_id, exam_date, exam_time) VALUES
('Web Development Final', 1, '2025-01-15', '10:00:00'),
('Data Structures Midterm', 2, '2025-01-20', '14:00:00'),
('Algorithms Final', 3, '2025-01-25', '09:00:00'),
('Operating Systems Exam', 4, '2025-02-01', '13:00:00'),
('Computer Networks Final', 5, '2025-02-05', '11:00:00'),
('Database Systems Final', 6, '2025-02-10', '15:00:00'),
('Software Engineering Project', 7, '2025-02-15', '16:00:00');

-- Sample enrollments
INSERT INTO enrollments (student_id, subject_id) VALUES
(2, 1), -- John enrolled in Web Development
(2, 2), -- John enrolled in Data Structures
(3, 1), -- Jane enrolled in Web Development
(3, 3); -- Jane enrolled in Algorithms

-- Sample exam bookings
INSERT INTO exam_bookings (student_id, exam_id) VALUES
(2, 1), -- John booked Web Development Final
(2, 2), -- John booked Data Structures Midterm
(3, 1), -- Jane booked Web Development Final
(3, 3); -- Jane booked Algorithms Final

-- Sample meetings
INSERT INTO meetings (student_id, professor_id, meeting_datetime, status, notes) VALUES
(2, 1, '2025-01-10 14:00:00', 'confirmed', 'Discussion about web development project'),
(3, 2, '2025-01-12 10:00:00', 'pending', 'Algorithm review session'),
(2, 3, '2025-01-15 16:00:00', 'confirmed', 'Database design consultation'); 