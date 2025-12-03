<?php
/**
 * Student Management API
 * 
 * This is a RESTful API that handles all CRUD operations for student management.
 * It uses PDO to interact with a MySQL database.
 * 
 * Database Table Structure (for reference):
 * Table: users
 * Columns:
 *   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 *   - student_id (VARCHAR(50), UNIQUE) - The student's university ID
 *   - name (VARCHAR(100))
 *   - email (VARCHAR(100), UNIQUE)
 *   - password (VARCHAR(255)) - Hashed password
 *   - created_at (TIMESTAMP)
 * 
 * HTTP Methods Supported:
 *   - GET: Retrieve student(s)
 *   - POST: Create a new student OR change password
 *   - PUT: Update an existing student
 *   - DELETE: Delete a student
 * 
 * Response Format: JSON
 */

if (session_status() === PHP_SESSION_NONE)
    session_start();
    
// TODO: Set headers for JSON response and CORS
// Set Content-Type to application/json
// Allow cross-origin requests (CORS) if needed
// Allow specific HTTP methods (GET, POST, PUT, DELETE, OPTIONS)
// Allow specific headers (Content-Type, Authorization)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: same-origin");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// TODO: Handle preflight OPTIONS request
// If the request method is OPTIONS, return 200 status and exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// TODO: Include the database connection class
// Assume the Database class has a method getConnection() that returns a PDO instance
require_once __DIR__ . "/../../db.php";

// TODO: Get the PDO database connection
$database = new Database();
$db = $database->getConnection();

// TODO: Get the HTTP request method
// Use $_SERVER['REQUEST_METHOD']
$method = $_SERVER['REQUEST_METHOD'];

// TODO: Get the request body for POST and PUT requests
// Use file_get_contents('php://input') to get raw POST data
// Decode JSON data using json_decode()
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);

// TODO: Parse query parameters for filtering and searching
$studentIdQuery = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;
$search = $_GET['search'] ?? null;
$sort = $_GET['sort'] ?? null;
$order = $_GET['order'] ?? null;

/**
 * Function: Get all students or search for specific students
 * Method: GET
 * 
 * Query Parameters:
 *   - search: Optional search term to filter by name, student_id, or email
 *   - sort: Optional field to sort by (name, student_id, email)
 *   - order: Optional sort order (asc or desc)
 */
function getStudents($db, $search = null, $sort = null, $order = 'asc') {
    // TODO: Check if search parameter exists
    // If yes, prepare SQL query with WHERE clause using LIKE
    // Search should work on name, student_id, and email fields
    $allowedSort  = ['name', 'id', 'email'];
    $allowedOrder = ['asc', 'desc'];

    $sql = "SELECT id, name, email, created_at FROM users";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE name LIKE :search OR id LIKE :search OR email LIKE :search";
        $params[':search'] = "%$search%";
    }

    // TODO: Check if sort and order parameters exist
    // If yes, add ORDER BY clause to the query
    // Validate sort field to prevent SQL injection (only allow: name, student_id, email)
    // Validate order to prevent SQL injection (only allow: asc, desc)
    if ($sort && in_array($sort, $allowedSort)) {
        $order = in_array(strtolower($order), $allowedOrder) ? $order : 'asc';
        $sql .= " ORDER BY $sort $order";
    } else {
        $sql .= " ORDER BY name ASC";
    }

    // TODO: Prepare the SQL query using PDO
    // Note: Do NOT select the password field
    $stmt = $db->prepare($sql);

    // TODO: Bind parameters if using search
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // TODO: Execute the query
    $stmt->execute();

    // TODO: Fetch all results as an associative array
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // TODO: Return JSON response with success status and data
    sendResponse(['success' => true, 'data' => $students]);
}

/**
 * Function: Get a single student by student_id
 * Method: GET
 * 
 * Query Parameters:
 *   - student_id: The student's university ID
 */
function getStudentById($db, $studentId) {
    // TODO: Prepare SQL query to select student by student_id
    $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($sql);

    // TODO: Bind parameter
    $stmt->bindParam(':id', $studentId, PDO::PARAM_STR);

    // TODO: Execute the query
    $stmt->execute();

    // TODO: Fetch the result
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // TODO: Check if student exists
    // If yes, return success response with student data
    // If no, return error response with 404 status
    if ($student) {
        sendResponse(['success' => true, 'data' => $student]);
    } else {
        sendResponse(['success' => false, 'message' => 'Student not found'], 404);
    }
}

/**
 * Function: Create a new student
 * Method: POST
 * 
 * Required JSON Body:
 *   - student_id: The student's university ID (must be unique)
 *   - name: Student's full name
 *   - email: Student's email (must be unique)
 *   - password: Default password (will be hashed)
 */
function createStudent($db, $data) {
    // TODO: Validate required fields
    // Check if student_id, name, email, and password are provided
    // If any field is missing, return error response with 400 status
    if (empty($data['id']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        sendResponse(['success' => false, 'message' => 'Missing required fields'], 400);
    }

    // TODO: Sanitize input data
    // Trim whitespace from all fields
    // Validate email format using filter_var()
    $student_id = sanitizeInput($data['id']);
    $name = sanitizeInput($data['name']);
    $email = trim($data['email']);
    $password = $data['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(['success'=>false,'message'=>'Invalid email format'],400);
    }

    // TODO: Check if student_id or email already exists
    // Prepare and execute a SELECT query to check for duplicates
    // If duplicate found, return error response with 409 status (Conflict)
    $check = $db->prepare("SELECT 1 FROM users WHERE id = :id OR email = :email");
    $check->execute([':id'=>$student_id, ':email'=>$email]);
    if ($check->fetchColumn()) {
        sendResponse(['success' => false, 'message' => 'Student already exists'], 409);
    }

    // TODO: Hash the password
    // Use password_hash() with PASSWORD_DEFAULT
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // TODO: Insert query
    $sql = "INSERT INTO users (id, name, email, password, created_at) VALUES (:id, :name, :email, :password, NOW())";
    
    // TODO: Bind parameters
    // Bind student_id, name, email, and hashed password
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $student_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    // TODO: Execute the query
    $ok = $stmt->execute();

    // TODO: Check if insert was successful
    // If yes, return success response with 201 status (Created)
    // If no, return error response with 500 status
    if ($ok) {
        sendResponse(['success' => true, 'message' => 'Student created'], 201);
    } else {
        sendResponse(['success' => false, 'message' => 'Insert failed'], 500);
    }
}


/**
 * Function: Update an existing student
 * Method: PUT
 * 
 * Required JSON Body:
 *   - student_id: The student's university ID (to identify which student to update)
 *   - name: Updated student name (optional)
 *   - email: Updated student email (optional)
 */
function updateStudent($db, $data) {
    // TODO: Validate that student_id is provided
    // If not, return error response with 400 status
    if (empty($data['id'])) {
        sendResponse(['success' => false, 'message' => 'Missing id.'], 400);
    }

    $studentId = $data['id'];

    // TODO: Check if student exists
    // Prepare and execute a SELECT query to find the student
    // If not found, return error response with 404 status
    $exists = $db->prepare("SELECT 1 FROM users WHERE id = :id");
    $exists->execute([':id'=>$studentId]);

    if (!$exists->fetchColumn()) {
        sendResponse(['success' => false, 'message' => 'Student not found'], 404);
    }

    // TODO: Build UPDATE query dynamically based on provided fields
    // Only update fields that are provided in the request
    $fields = [];
    $params = [':id' => $studentId];

    if (!empty($data['name'])) {
        $fields[] = "name = :name";
        $params[':name'] = sanitizeInput($data['name']);
    }

    if (!empty($data['email'])) {

    if (!validateEmail($data['email'])) {
        sendResponse(['success'=>false,'message'=>'Invalid email'],400);
    }

    // TODO: If email is being updated, check if new email already exists
    // Prepare and execute a SELECT query
    // Exclude the current student from the check
    // If duplicate found, return error response with 409 status
    $check = $db->prepare("SELECT 1 FROM users WHERE email = :email AND id != :id");
    $check->execute([':email'=>$data['email'],':id'=>$studentId]);
    if ($check->fetchColumn()) {
        sendResponse(['success'=>false,'message'=>'Email already exists'],409);
    }

    $fields[] = "email = :email";
    $params[':email'] = $data['email'];
    }

    // TODO: Bind parameters dynamically
    // Bind only the parameters that are being updated
    if (empty($fields)) {
        sendResponse(['success' => false, 'message' => 'No fields to update'], 400);
    }

    // TODO: Execute the query
    $sql = "UPDATE users SET ".implode(", ",$fields)." WHERE id = :id";
    $stmt = $db->prepare($sql);
    $ok = $stmt->execute($params);

    // TODO: Check if update was successful
    // If yes, return success response
    // If no, return error response with 500 status
    if ($ok) {
        sendResponse(['success' => true, 'message' => 'Student updated successfully.']);
    } else {
        sendResponse(['success' => false, 'message' => 'Failed to update student.'], 500);
    }
}


/**
 * Function: Delete a student
 * Method: DELETE
 * 
 * Query Parameters or JSON Body:
 *   - student_id: The student's university ID
 */
function deleteStudent($db, $studentId) {
    // TODO: Validate that student_id is provided
    // If not, return error response with 400 status
    if (empty($studentId)) {
        sendResponse(['success' => false, 'message' => 'student_id is required.'], 400);
    }

    // TODO: Check if student exists
    // Prepare and execute a SELECT query
    // If not found, return error response with 404 status
    $stmt = $db->prepare("SELECT 1 FROM users WHERE id = :id");
    $stmt->execute([':id'=>$studentId]);
    if (!$stmt->fetchColumn()) {
        sendResponse(['success'=>false,'message'=>'Student not found'],404);
    }


    // TODO: Prepare DELETE query
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $db->prepare($sql);

    // TODO: Bind the student_id parameter
    $stmt->bindParam(':id', $studentId, PDO::PARAM_STR);

    // TODO: Execute the query
    $ok = $stmt->execute();

    // TODO: Check if delete was successful
    // If yes, return success response
    // If no, return error response with 500 status
    if ($ok) {
        sendResponse(['success'=>true,'message'=>'Deleted']);
    } else {
        sendResponse(['success'=>false,'message'=>'Failed to delete'],500);
    }
}


/**
 * Function: Change password
 * Method: POST with action=change_password
 * 
 * Required JSON Body:
 *   - student_id: The student's university ID (identifies whose password to change)
 *   - current_password: The student's current password
 *   - new_password: The new password to set
 */
function changePassword($db, $data) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id'])) {
    sendResponse(['success' => false, 'message' => 'Not logged in'], 401); }

    $userId = $_SESSION['id'];

    error_log('User ID: ' . $userId);

    // TODO: Validate required fields
    // Check if student_id, current_password, and new_password are provided
    // If any field is missing, return error response with 400 status
    if (!$userId || empty($data) || !isset($data['current_password']) || !isset($data['new_password'])) {
    sendResponse(['success'=>false,'message'=>'Missing fields'],400); }

    $newPassword = $data['new_password'];
    // TODO: Validate new password strength
    // Check minimum length (at least 8 characters)
    // If validation fails, return error response with 400 status
    if (strlen($newPassword) < 8) {
        sendResponse(['success'=>false,'message'=>'New password must be at least 8 characters long.'],400);
    }

    // TODO: Retrieve current password hash from database
    // Prepare and execute SELECT query to get password
    $sql = "SELECT password FROM users WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // TODO: Verify current password
    // Use password_verify() to check if current_password matches the hash
    // If verification fails, return error response with 401 status (Unauthorized)
    if (!$row) {
         sendResponse(['success' => false, 'message' => 'Student not found.'], 404);
         exit;
    }

    if (!password_verify($data['current_password'], $row['password'])) {
        sendResponse(['success' => false, 'message' => 'Current password is incorrect'], 401);
    }

    if (password_verify($data['new_password'], $row['password'])) {
        sendResponse(['success'=>false,'message'=>'New password must be different from current password'],400);
        return;
    }
    
    // TODO: Hash the new password
    // Use password_hash() with PASSWORD_DEFAULT
    $newHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
    

    // TODO: Update password in database
    // Prepare UPDATE query
    $stmt = $db->prepare("UPDATE users SET password = :p WHERE id = :id");

    // TODO: Bind parameters and execute
    $ok = $stmt->execute([':p' => $newHash, ':id' => $userId]);

    // TODO: Check if update was successful
    // If yes, return success response
    // If no, return error response with 500 status
    if ($ok) {
        sendResponse(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        sendResponse(['success' => false, 'message' => 'Failed to change password'],500);
    }
}


/**
 * ==============================
 * MAIN REQUEST ROUTER
 * ==============================
 */
try {
// TODO: Route the request based on HTTP method
    if ($method === 'GET') {
        // TODO: Check if student_id is provided in query parameters
        // If yes, call getStudentById()
        // If no, call getStudents() to get all students (with optional search/sort)
        if ($studentIdQuery) {
        getStudentById($db, $studentIdQuery);
        } 
        else {
        getStudents($db, $search, $sort, $order ?? 'asc');
    }
    }
    elseif ($method === 'POST') {
        // TODO: Check if this is a change password request
        // Look for action=change_password in query parameters
        // If yes, call changePassword()
        // If no, call createStudent()
        $action === 'change_password'
            ? changePassword($db,$inputData)
            : createStudent($db,$inputData);
    }
    elseif ($method === 'PUT') {
         // TODO: Call updateStudent()
        updateStudent($db,$inputData);
    }
    elseif ($method === 'DELETE') {
        // TODO: Get student_id from query parameter or request body
        // Call deleteStudent()
        deleteStudent($db,$studentIdQuery ?? $inputData['id'] ?? null);
    }
    else {
        // TODO: Return error for unsupported methods
        // Set HTTP status to 405 (Method Not Allowed)
        // Return JSON error message
        sendResponse(['success'=>false,'message'=>'Method Not Allowed'],405);
    }

} catch (PDOException $e) {
    // TODO: Handle database errors
    // Log the error message (optional)
    // Return generic error response with 500 status
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);

} catch (Exception $e) {
    // TODO: Handle general errors
    // Return error response with 500 status
    http_response_code(500); 
    echo json_encode(['error' => 'An unexpected error occurred']);
}


// ============================================================================
// HELPER FUNCTIONS (Optional but Recommended)
// ============================================================================

/**
 * Helper function to send JSON response
 * 
 * @param mixed $data - Data to send
 * @param int $statusCode - HTTP status code
 */
function sendResponse($data, $statusCode = 200) {
    // TODO: Set HTTP response code
    http_response_code($statusCode);
    
    // TODO: Echo JSON encoded data
    echo json_encode($data);
    
    // TODO: Exit to prevent further execution
    exit();
}


/**
 * Helper function to validate email format
 * 
 * @param string $email - Email address to validate
 * @return bool - True if valid, false otherwise
 */
function validateEmail($email) {
    // TODO: Use filter_var with FILTER_VALIDATE_EMAIL
    // Return true if valid, false otherwise
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


/**
 * Helper function to sanitize input
 * 
 * @param string $data - Data to sanitize
 * @return string - Sanitized data
 */
function sanitizeInput($data) {
    // TODO: Trim whitespace
    // TODO: Strip HTML tags using strip_tags()
    // TODO: Convert special characters using htmlspecialchars()
    // Return sanitized data
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

?>
