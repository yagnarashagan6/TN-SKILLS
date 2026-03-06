-- Create a database
CREATE DATABASE my_database;

-- Use the database
USE my_database;

-- Create a table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    age INT
);

-- Insert data into the table
INSERT INTO users (name, email, age) VALUES
('Alice', 'alice@example.com', 25),
('Bob', 'bob@example.com', 30),
('Charlie', 'charlie@example.com', 22);

-- Query the data
SELECT * FROM users;