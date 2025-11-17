create database Library_dbms_U

USE Library_dbms_U;
GO

-- ==============================
-- Main Table (Super)
-- ==============================
IF OBJECT_ID('dbo.Library_Db') IS NOT NULL DROP TABLE dbo.Library_Db;
CREATE TABLE Library_Db (
    Library_id INT PRIMARY KEY IDENTITY(1,1),
    BranchName NVARCHAR(100) NOT NULL,
    Location NVARCHAR(100) NOT NULL,
    LibraryManager NVARCHAR(100),
    TotalBooks INT,
    StaffCount INT,
    MemberCount INT,
    BooksIssued INT,
    Status NVARCHAR(20),
    Description NVARCHAR(200)
);

-- ==============================
-- Faculty_Db
-- ==============================
IF OBJECT_ID('dbo.Faculty_Db') IS NOT NULL DROP TABLE dbo.Faculty_Db;
CREATE TABLE Faculty_Db (
    Faculty_id INT PRIMARY KEY IDENTITY(1,1),
    Library_id INT NOT NULL,
    FullName NVARCHAR(100) NOT NULL,
    Rank NVARCHAR(50),
    DOB DATE,
    Email NVARCHAR(100),
    Address NVARCHAR(200),
    AccountStatus NVARCHAR(20),
    CONSTRAINT FK_Faculty_Library_Db FOREIGN KEY (Library_id) REFERENCES Library_Db(Library_id)
);

-- ==============================
-- Student_Db
-- ==============================
IF OBJECT_ID('dbo.Student_Db') IS NOT NULL DROP TABLE dbo.Student_Db;
CREATE TABLE Student_Db (
    Student_id INT PRIMARY KEY IDENTITY(1,1),
    Library_id INT NOT NULL,
    Faculty_id INT NOT NULL,
    FullName NVARCHAR(100) NOT NULL,
    Rank NVARCHAR(50),
    DOB DATE,
    Email NVARCHAR(100),
    City NVARCHAR(50),
    Address NVARCHAR(200),
    ContactNumber NVARCHAR(20),
    Image VARBINARY(MAX),
    CONSTRAINT FK_Student_Library_Db FOREIGN KEY (Library_id) REFERENCES Library_Db(Library_id),
    CONSTRAINT FK_Student_Faculty_Db FOREIGN KEY (Faculty_id) REFERENCES Faculty_Db(Faculty_id)
);

-- ==============================
-- Library_Staff_Db
-- ==============================
IF OBJECT_ID('dbo.Library_Staff_Db') IS NOT NULL DROP TABLE dbo.Library_Staff_Db;
CREATE TABLE Library_Staff_Db (
    Staff_id INT PRIMARY KEY IDENTITY(1,1),
    Library_id INT NOT NULL,
    FirstName NVARCHAR(50),
    LastName NVARCHAR(50),
    Email NVARCHAR(100),
    Position NVARCHAR(50),
    HireDate DATE,
    ShiftTime NVARCHAR(50),
    UserName NVARCHAR(50),
    Password NVARCHAR(50),
    Status NVARCHAR(20),
    Phone NVARCHAR(20),
    CONSTRAINT FK_Staff_Library_Db FOREIGN KEY (Library_id) REFERENCES Library_Db(Library_id)
);

-- ==============================
-- Category_Db (NEW TABLE)
-- ==============================
IF OBJECT_ID('dbo.Category_Db') IS NOT NULL DROP TABLE dbo.Category_Db;
CREATE TABLE Category_Db (
    Category_id INT PRIMARY KEY IDENTITY(1,1),
    CategoryName NVARCHAR(100) NOT NULL,
    Description NVARCHAR(200)
);

-- ==============================
-- Book_Details_Db (UPDATED)
-- ==============================
IF OBJECT_ID('dbo.Book_Details_Db') IS NOT NULL DROP TABLE dbo.Book_Details_Db;
CREATE TABLE Book_Details_Db (
    Book_id INT PRIMARY KEY IDENTITY(1,1),
    Library_id INT NOT NULL,
    Category_id INT NOT NULL,
    PublisherName NVARCHAR(100),
    AuthorName NVARCHAR(100),
    BookName NVARCHAR(200) NOT NULL,
    Edition NVARCHAR(50),
    PageCount INT,
    Description NVARCHAR(300),
    CopyCount INT,
    Status NVARCHAR(20),
    CONSTRAINT FK_Book_Library_Db FOREIGN KEY (Library_id) REFERENCES Library_Db(Library_id),
    CONSTRAINT FK_Book_Category_Db FOREIGN KEY (Category_id) REFERENCES Category_Db(Category_id)
);

-- ==============================
-- Warehouse_Db
-- ==============================
IF OBJECT_ID('dbo.Warehouse_Db') IS NOT NULL DROP TABLE dbo.Warehouse_Db;
CREATE TABLE Warehouse_Db (
    Storage_id INT PRIMARY KEY IDENTITY(1,1),
    Library_id INT NOT NULL,
    Book_id INT NOT NULL,
    Location NVARCHAR(100),
    ShelfNumber NVARCHAR(20),
    Quantity INT,
    CurrentLoad INT,
    Status NVARCHAR(20),
    CONSTRAINT FK_Warehouse_Library_Db FOREIGN KEY (Library_id) REFERENCES Library_Db(Library_id),
    CONSTRAINT FK_Warehouse_Book_Db FOREIGN KEY (Book_id) REFERENCES Book_Details_Db(Book_id)
);

-- ==============================
-- Transactions_Db
-- ==============================
IF OBJECT_ID('dbo.Transactions_Db') IS NOT NULL DROP TABLE dbo.Transactions_Db;
CREATE TABLE Transactions_Db (
    Transaction_id INT PRIMARY KEY IDENTITY(1,1),
    Faculty_id INT NOT NULL,
    Student_id INT NOT NULL,
    Book_id INT NOT NULL,
    IssueDate DATE,
    ReturnDate DATE,
    IssueBy NVARCHAR(100),
    ReceiveBy NVARCHAR(100),
    DueDate DATE,
    Status NVARCHAR(20),
    Note NVARCHAR(200),
    CONSTRAINT FK_Trans_Faculty_Db FOREIGN KEY (Faculty_id) REFERENCES Faculty_Db(Faculty_id),
    CONSTRAINT FK_Trans_Student_Db FOREIGN KEY (Student_id) REFERENCES Student_Db(Student_id),
    CONSTRAINT FK_Trans_Book_Db FOREIGN KEY (Book_id) REFERENCES Book_Details_Db(Book_id)
);

-- ==============================
-- Issue_Details_Db
-- ==============================
IF OBJECT_ID('dbo.Issue_Details_Db') IS NOT NULL DROP TABLE dbo.Issue_Details_Db;
CREATE TABLE Issue_Details_Db (
    Issue_id INT PRIMARY KEY IDENTITY(1,1),
    Student_id INT NOT NULL,
    Book_id INT NOT NULL,
    Faculty_id INT NOT NULL,
    IssueBy NVARCHAR(100),
    IssueDate DATE NOT NULL,
    ReturnDate DATE,
    CONSTRAINT FK_Issue_Student_Db FOREIGN KEY (Student_id) REFERENCES Student_Db(Student_id),
    CONSTRAINT FK_Issue_Book_Db FOREIGN KEY (Book_id) REFERENCES Book_Details_Db(Book_id),
    CONSTRAINT FK_Issue_Faculty_Db FOREIGN KEY (Faculty_id) REFERENCES Faculty_Db(Faculty_id)
);

-- ==============================
-- Return_Details_Db
-- ==============================
IF OBJECT_ID('dbo.Return_Details_Db') IS NOT NULL DROP TABLE dbo.Return_Details_Db;
CREATE TABLE Return_Details_Db (
    Ret_id INT PRIMARY KEY IDENTITY(1,1),
    Student_id INT NOT NULL,
    Book_id INT NOT NULL,
    ReceiveBy NVARCHAR(100),
    IssueDate DATE NOT NULL,
    ReturnDate DATE NOT NULL,
    DueDate DATE,
    CONSTRAINT FK_Return_Student_Db FOREIGN KEY (Student_id) REFERENCES Student_Db(Student_id),
    CONSTRAINT FK_Return_Book_Db FOREIGN KEY (Book_id) REFERENCES Book_Details_Db(Book_id)
);

-- ==============================
-- Penalty_Db
-- ==============================
IF OBJECT_ID('dbo.Penalty_Db') IS NOT NULL DROP TABLE dbo.Penalty_Db;
CREATE TABLE Penalty_Db (
    Penalty_id INT PRIMARY KEY IDENTITY(1,1),
    Student_id INT NOT NULL,
    Return_id INT NOT NULL,
    Amount DECIMAL(10,2),
    PenaltyDate DATE,
    PaidStatus NVARCHAR(20),
    DueDays INT,
    CONSTRAINT FK_Penalty_Student_Db FOREIGN KEY (Student_id) REFERENCES Student_Db(Student_id),
    CONSTRAINT FK_Penalty_Return_Db FOREIGN KEY (Return_id) REFERENCES Return_Details_Db(Ret_id)
);

-- ==============================
-- Registration_Db
-- ==============================
IF OBJECT_ID('dbo.Registration_Db') IS NOT NULL DROP TABLE dbo.Registration_Db;
CREATE TABLE Registration_Db (
    ID INT PRIMARY KEY IDENTITY(1,1),
    Student_id INT NOT NULL,
    UserName NVARCHAR(50) UNIQUE NOT NULL,
    Password NVARCHAR(50) NOT NULL,
    Description NVARCHAR(200),
    CONSTRAINT FK_Reg_Student_Db FOREIGN KEY (Student_id) REFERENCES Student_Db(Student_id)
);
---=============
--=INSERT FOR ALL TABLE DATA
--==========================


-- ==============================
-- Library_Db
-- ==============================
INSERT INTO Library_Db (BranchName, Location, LibraryManager, TotalBooks, StaffCount, MemberCount, BooksIssued, Status, Description)
VALUES
('Central Library', 'Kabul', 'Ahmad Karimi', 1200, 20, 500, 300, 'Active', 'Main library of Kabul University'),
('Science Library', 'Kabul', 'Zahra Hashemi', 800, 15, 300, 200, 'Active', 'Library for Science Faculty'),
('Arts Library', 'Kabul', 'Omar Faizi', 600, 10, 200, 150, 'Active', 'Library for Arts Faculty');

-- ==============================
-- Faculty_Db
-- ==============================
INSERT INTO Faculty_Db (Library_id, FullName, Rank, DOB, Email, Address, AccountStatus)
VALUES
(1, 'Dr. Ali Ahmad', 'Professor', '1975-05-10', 'ali.ahmad@ku.edu.af', 'Kabul, Afghanistan', 'Active'),
(1, 'Dr. Fatima Rahimi', 'Associate Professor', '1980-08-20', 'fatima.rahimi@ku.edu.af', 'Kabul, Afghanistan', 'Active'),
(2, 'Dr. Mohammad Jafari', 'Assistant Professor', '1985-03-15', 'mohammad.jafari@ku.edu.af', 'Kabul, Afghanistan', 'Active');

-- ==============================
-- Student_Db
-- ==============================
INSERT INTO Student_Db (Library_id, Faculty_id, FullName, Rank, DOB, Email, City, Address, ContactNumber)
VALUES
(1, 1, 'Ahmad Noor', 'Bachelor', '2002-04-10', 'ahmad.noor@student.ku.edu.af', 'Kabul', 'Street 12, Kabul', '0700123456'),
(1, 2, 'Fatima Gul', 'Bachelor', '2001-06-22', 'fatima.gul@student.ku.edu.af', 'Kabul', 'Street 5, Kabul', '0700654321'),
(2, 3, 'Omar Khan', 'Bachelor', '2003-09-15', 'omar.khan@student.ku.edu.af', 'Kabul', 'Street 8, Kabul', '0700789456');

-- ==============================
-- Library_Staff_Db
-- ==============================
INSERT INTO Library_Staff_Db (Library_id, FirstName, LastName, Email, Position, HireDate, ShiftTime, UserName, Password, Status, Phone)
VALUES
(1, 'Hassan', 'Karimi', 'hassan.karimi@ku.edu.af', 'Librarian', '2015-01-10', 'Morning', 'hkarimi', 'pass123', 'Active', '0700112233'),
(2, 'Leila', 'Ahmadi', 'leila.ahmadi@ku.edu.af', 'Assistant Librarian', '2018-05-15', 'Evening', 'lahmadi', 'pass456', 'Active', '0700223344');

-- ==============================
-- Category_Db
-- ==============================
INSERT INTO Category_Db (CategoryName, Description)
VALUES
('Science', 'Books related to science'),
('Arts', 'Books related to arts'),
('Engineering', 'Books related to engineering');

-- ==============================
-- Book_Details_Db
-- ==============================
INSERT INTO Book_Details_Db (Library_id, Category_id, PublisherName, AuthorName, BookName, Edition, PageCount, Description, CopyCount, Status)
VALUES
(1, 1, 'Oxford', 'Stephen Hawking', 'A Brief History of Time', '1st', 256, 'Famous book on cosmology', 5, 'Available'),
(1, 2, 'Penguin', 'William Shakespeare', 'Hamlet', '2nd', 180, 'Classic play', 3, 'Available'),
(2, 3, 'McGraw-Hill', 'Andrew Tanenbaum', 'Computer Networks', '5th', 1100, 'Networking book', 4, 'Available');

-- ==============================
-- Warehouse_Db
-- ==============================
INSERT INTO Warehouse_Db (Library_id, Book_id, Location, ShelfNumber, Quantity, CurrentLoad, Status)
VALUES
(1, 1, 'Main Hall', 'A1', 5, 5, 'Full'),
(1, 2, 'Second Floor', 'B2', 3, 3, 'Available'),
(2, 3, 'Ground Floor', 'C3', 4, 4, 'Available');

-- ==============================
-- Transactions_Db
-- ==============================
INSERT INTO Transactions_Db (Faculty_id, Student_id, Book_id, IssueDate, ReturnDate, IssueBy, ReceiveBy, DueDate, Status, Note)
VALUES
(1, 1, 1, '2025-09-01', NULL, 'Hassan Karimi', NULL, '2025-09-15', 'Issued', 'First issue'),
(2, 2, 2, '2025-09-05', '2025-09-20', 'Leila Ahmadi', 'Leila Ahmadi', '2025-09-20', 'Returned', '');

-- ==============================
-- Issue_Details_Db
-- ==============================
INSERT INTO Issue_Details_Db (Student_id, Book_id, Faculty_id, IssueBy, IssueDate, ReturnDate)
VALUES
(1, 1, 1, 'Hassan Karimi', '2025-09-01', NULL),
(2, 2, 2, 'Leila Ahmadi', '2025-09-05', '2025-09-20');

-- ==============================
-- Return_Details_Db
-- ==============================
INSERT INTO Return_Details_Db (Student_id, Book_id, ReceiveBy, IssueDate, ReturnDate, DueDate)
VALUES
(2, 2, 'Leila Ahmadi', '2025-09-05', '2025-09-20', '2025-09-20');

-- ==============================
-- Penalty_Db
-- ==============================
INSERT INTO Penalty_Db (Student_id, Return_id, Amount, PenaltyDate, PaidStatus, DueDays)
VALUES
(2, 1, 10.00, '2025-09-21', 'Paid', 1);

-- ==============================
-- Registration_Db
-- ==============================
INSERT INTO Registration_Db (Student_id, UserName, Password, Description)
VALUES
(1, 'ahmadnoor', 'pass123', 'First student login'),
(2, 'fatimagul', 'pass456', 'Second student login');
select * from Student_Db
select * from Faculty_Db