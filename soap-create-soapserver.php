<?php

ini_set('soap.wsdl_cache_enabled', '0');

// Database connection
function getDbConnection() {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=employee', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Convert Celsius to Fahrenheit
function celsiusToFahrenheit($celsius) {
    $fahrenheit = $celsius * 9 / 5 + 32;
    return $fahrenheit;
}

// Create an employee record
function createEmployee($name, $email, $position) {
    $db = getDbConnection();
    $sql = "INSERT INTO employees (name, email, position) VALUES (:name, :email, :position)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':position', $position);
    if ($stmt->execute()) {
        return "Employee record created successfully!";
    }
    return "Error creating employee record!";
}

// Read an employee record by ID
function readEmployee($id) {
    $db = getDbConnection();
    $sql = "SELECT * FROM employees WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($employee) {
        return $employee;
    }
    return "Employee not found!";
}

function getAllEmployees() {
    $db = getDbConnection();
    $sql = "SELECT * FROM employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    return $employees; 
}



// Update an employee record
function updateEmployee($id, $name, $email, $position) {
    $db = getDbConnection();
    $sql = "UPDATE employees SET name = :name, email = :email, position = :position WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':position', $position);
    if ($stmt->execute()) {
        return "Employee record updated successfully!";
    }
    return "Error updating employee record!";
}

// Delete an employee record
function deleteEmployee($id) {
    $db = getDbConnection();
    $sql = "DELETE FROM employees WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        return "Employee record deleted successfully!";
    }
    return "Error deleting employee record!";
}

// Initialize SOAP Server
$server = new SoapServer('http://localhost/php-create-soap-service/ctof.wsdl');

// Register available functions
$server->addFunction('celsiusToFahrenheit');
$server->addFunction('createEmployee');
$server->addFunction('readEmployee');
$server->addFunction('updateEmployee');
$server->addFunction('deleteEmployee');
$server->addFunction('getAllEmployees'); 
// Start handling requests
$server->handle();

?>
