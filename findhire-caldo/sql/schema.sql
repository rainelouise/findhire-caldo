-- Table for storing roles
CREATE TABLE Roles (
    RoleID INT PRIMARY KEY AUTO_INCREMENT,
    RoleName VARCHAR(50) NOT NULL UNIQUE 
);

-- Table for storing users
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL, 
    Email VARCHAR(100) NOT NULL UNIQUE,
    RoleID INT NOT NULL,
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
);

-- Table for storing job posts
CREATE TABLE JobPosts (
    JobPostID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(100) NOT NULL,
    Description TEXT NOT NULL,
    CreatedBy INT NOT NULL, 
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CreatedBy) REFERENCES Users(UserID)
);

-- Table for storing job applications
CREATE TABLE Applications (
    ApplicationID INT PRIMARY KEY AUTO_INCREMENT,
    JobPostID INT NOT NULL,
    ApplicantID INT NOT NULL, 
    CoverLetter TEXT NOT NULL, 
    ResumePath VARCHAR(255) NOT NULL, 
    Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (JobPostID) REFERENCES JobPosts(JobPostID),
    FOREIGN KEY (ApplicantID) REFERENCES Users(UserID)
);


-- Table for storing messages
CREATE TABLE Messages (
    MessageID INT PRIMARY KEY AUTO_INCREMENT,
    SenderID INT NOT NULL, 
    ReceiverID INT NOT NULL, 
    Content TEXT NOT NULL, 
    SentAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SenderID) REFERENCES Users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES Users(UserID)
);

-- Insert roles
INSERT INTO Roles (RoleName) VALUES ('Applicant'), ('HR');