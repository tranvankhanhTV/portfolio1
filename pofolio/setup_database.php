<?php
// Include the database connection
include 'config/db.php';

// SQL to create tables
$sql = "
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Tables created successfully!" . PHP_EOL;
    // It's important to consume all results from a multi_query
    while ($conn->next_result()) {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }
} else {
    echo "Error creating tables: " . $conn->error . PHP_EOL;
}

$conn->close();
?>
