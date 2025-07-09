CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) -- store hashed passwords
);

-- Contestants table
CREATE TABLE contestants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    photo VARCHAR(255)
);

-- Criteria table
CREATE TABLE criteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    percentage INT
);

-- Scores table
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT,
    contestant_id INT,
    criterion_id INT,
    score DECIMAL(5,2),
    FOREIGN KEY (judge_id) REFERENCES judges(id),
    FOREIGN KEY (contestant_id) REFERENCES contestants(id),
    FOREIGN KEY (criterion_id) REFERENCES criteria(id)
);