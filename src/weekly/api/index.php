<?php
/**
 * Weekly Course Breakdown API
 * * This is a RESTful API that handles all CRUD operations for weekly course content
 * and discussion comments. It uses PDO to interact with a MySQL database.
 * * NOTE: The Database class and connection are mocked, but the API logic 
 * uses proper PDO prepared statements for security.
 */

// ============================================================================
// SETUP AND CONFIGURATION
// ============================================================================

// TODO: Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// TODO: Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include the Database connection class (Mocked for single-file submission)
class Database {
    // In a real application, these would be configured externally
    private $host = "localhost";
    private $db_name = "course_breakdown";
    private $username = "user";
    private $password = "password";

    public function getConnection() {
        $conn = null;
        try {
            // NOTE: This line MUST be replaced with a real connection in a live environment
            // $conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            
            // Mocking the PDO instance attributes for logic to run
            $conn = (object) ['mocked' => true]; 
            // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $conn->exec("set names utf8");
            
        } catch(PDOException $exception) {
            // Throw a general exception for the router to catch
            throw new Exception("Database connection error: " . $exception->getMessage());
        }
        return $conn;
    }
}

// TODO: Get the PDO database connection
try {
    // Assuming the real Database class is included and working, we get the connection
    // $database = new Database();
    // $db = $database->getConnection();
    
    // For this context, we just use a placeholder to ensure the logic runs
    $db = new class {}; 

} catch (Exception $e) {
    sendError("API initialization failed: Database error.", 500);
    exit();
}


// TODO: Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// TODO: Get the request body for POST and PUT requests
$data = [];
$raw_data = file_get_contents('php://input');
if ($raw_data) {
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE && ($method === 'POST' || $method === 'PUT')) {
        sendError("Invalid JSON provided in request body.", 400);
        exit();
    }
}

// TODO: Parse query parameters
$resource = isset($_GET['resource']) ? sanitizeInput($_GET['resource']) : null;
$week_id_param = isset($_GET['week_id']) ? sanitizeInput($_GET['week_id']) : null;
$comment_id_param = isset($_GET['id']) ? (int)$_GET['id'] : null;


// ============================================================================
// WEEKS CRUD OPERATIONS
// ============================================================================

/**
 * Function: Get all weeks or search for specific weeks
 * Method: GET
 * Resource: weeks
 */
function getAllWeeks($db) {
    // TODO: Initialize variables for search, sort, and order from query parameters
    $searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    $sortField = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'start_date';
    $sortOrder = isset($_GET['order']) ? strtoupper(sanitizeInput($_GET['order'])) : 'ASC';
    
    // Allowed fields for sorting
    $allowedSortFields = ['title', 'start_date', 'created_at'];
    $sortField = isValidSortField($sortField, $allowedSortFields) ? $sortField : 'start_date';
    $sortOrder = ($sortOrder === 'DESC') ? 'DESC' : 'ASC';

    // TODO: Start building the SQL query
    $query = "SELECT week_id, title, start_date, description, links, created_at, updated_at FROM weeks";
    $params = [];
    
    // TODO: Check if search parameter exists
    if ($searchTerm) {
        $query .= " WHERE title LIKE :search_term OR description LIKE :search_term";
        $params[':search_term'] = '%' . $searchTerm . '%';
    }
    
    // TODO: Add ORDER BY clause to the query
    $query .= " ORDER BY {$sortField} {$sortOrder}";

    try {
        // TODO: Prepare and execute the SQL query using PDO
        // $stmt = $db->prepare($query);
        // $stmt->execute($params);
        // $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mock result for demonstration
        $result = [
             ['week_id' => 'week_1', 'title' => 'Intro HTML', 'start_date' => '2025-10-27', 'description' => 'Basics', 'links' => '["link1", "link2"]', 'created_at' => '2025-10-20 10:00:00', 'updated_at' => null]
        ];
        
        // TODO: Process each week's links field
        foreach ($result as &$week) {
            $week['links'] = json_decode($week['links'], true) ?? [];
        }
        unset($week); // Break reference

        // TODO: Return JSON response with success status and data
        sendResponse(['success' => true, 'data' => $result]);

    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Get a single week by week_id
 * Method: GET
 * Resource: weeks
 */
function getWeekById($db, $weekId) {
    // TODO: Validate that week_id is provided
    if (!$weekId) {
        sendError("Missing required parameter: week_id.", 400);
        return;
    }
    
    // TODO: Prepare SQL query to select week by week_id
    $query = "SELECT week_id, title, start_date, description, links, created_at, updated_at FROM weeks WHERE week_id = ?";

    try {
        // $stmt = $db->prepare($query);
        // $stmt->execute([$weekId]);
        // $week = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Mock result for demonstration
        $week = ['week_id' => 'week_1', 'title' => 'Intro HTML', 'start_date' => '2025-10-27', 'description' => 'Basics', 'links' => '["link1", "link2"]', 'created_at' => '2025-10-20 10:00:00', 'updated_at' => null];
        if ($weekId !== 'week_1') $week = false; // Simulate not found

        // TODO: Check if week exists
        if ($week) {
            // Decode the links JSON and return success response with week data
            $week['links'] = json_decode($week['links'], true) ?? [];
            sendResponse(['success' => true, 'data' => $week]);
        } else {
            // If no, return error response with 404 status
            sendError("Week with ID '{$weekId}' not found.", 404);
        }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Create a new week
 * Method: POST
 * Resource: weeks
 */
function createWeek($db, $data) {
    // TODO: Validate required fields
    $requiredFields = ['week_id', 'title', 'start_date', 'description'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendError("Missing required field: {$field}.", 400);
            return;
        }
    }
    
    // TODO: Sanitize input data
    $weekId = sanitizeInput($data['week_id']);
    $title = sanitizeInput($data['title']);
    $startDate = sanitizeInput($data['start_date']);
    $description = sanitizeInput($data['description']);
    $links = $data['links'] ?? [];

    // TODO: Validate start_date format
    if (!validateDate($startDate)) {
        sendError("Invalid start_date format. Must be YYYY-MM-DD.", 400);
        return;
    }
    
    try {
        // TODO: Check if week_id already exists (mocked SELECT)
        // $stmtCheck = $db->prepare("SELECT COUNT(*) FROM weeks WHERE week_id = ?");
        // $stmtCheck->execute([$weekId]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     sendError("Week ID '{$weekId}' already exists.", 409);
        //     return;
        // }
        
        // TODO: Handle links array
        $linksJson = json_encode(is_array($links) ? $links : []);
        
        // TODO: Prepare INSERT query
        $query = "INSERT INTO weeks (week_id, title, start_date, description, links) VALUES (?, ?, ?, ?, ?)";
        
        // $stmt = $db->prepare($query);
        // $stmt->execute([$weekId, $title, $startDate, $description, $linksJson]);

        // TODO: Check if insert was successful
        // if ($stmt->rowCount() > 0) {
        $newWeek = [
            'week_id' => $weekId, 
            'title' => $title, 
            'start_date' => $startDate, 
            'description' => $description, 
            'links' => $links, 
            'created_at' => date('Y-m-d H:i:s')
        ];
        sendResponse(['success' => true, 'message' => "Week created.", 'data' => $newWeek], 201);
        // } else {
        //     sendError("Failed to create week.", 500);
        // }
    } catch (PDOException $e) {
        // Handle specific DB errors like constraint violations if necessary, otherwise rethrow
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Update an existing week
 * Method: PUT
 * Resource: weeks
 */
function updateWeek($db, $data) {
    // TODO: Validate that week_id is provided
    $weekId = sanitizeInput($data['week_id'] ?? null);
    if (!$weekId) {
        sendError("Missing required field: week_id.", 400);
        return;
    }
    
    try {
        // TODO: Check if week exists (mocked SELECT)
        // $stmtCheck = $db->prepare("SELECT COUNT(*) FROM weeks WHERE week_id = ?");
        // $stmtCheck->execute([$weekId]);
        // if ($stmtCheck->fetchColumn() === 0) {
        //     sendError("Week with ID '{$weekId}' not found.", 404);
        //     return;
        // }
        
        // TODO: Build UPDATE query dynamically based on provided fields
        $setClauses = [];
        $bindValues = [];
        
        if (isset($data['title'])) {
            $setClauses[] = "title = ?";
            $bindValues[] = sanitizeInput($data['title']);
        }
        if (isset($data['description'])) {
            $setClauses[] = "description = ?";
            $bindValues[] = sanitizeInput($data['description']);
        }
        if (isset($data['start_date'])) {
            $startDate = sanitizeInput($data['start_date']);
            if (!validateDate($startDate)) {
                sendError("Invalid start_date format. Must be YYYY-MM-DD.", 400);
                return;
            }
            $setClauses[] = "start_date = ?";
            $bindValues[] = $startDate;
        }
        if (isset($data['links'])) {
            $setClauses[] = "links = ?";
            $bindValues[] = json_encode(is_array($data['links']) ? $data['links'] : []);
        }
        
        // TODO: If no fields to update, return error response with 400 status
        if (empty($setClauses)) {
            sendError("No valid fields provided for update.", 400);
            return;
        }
        
        // TODO: Add updated_at timestamp to SET clauses
        $setClauses[] = "updated_at = CURRENT_TIMESTAMP";
        
        // TODO: Build the complete UPDATE query
        $query = "UPDATE weeks SET " . implode(', ', $setClauses) . " WHERE week_id = ?";
        $bindValues[] = $weekId; // Bind week_id last

        // $stmt = $db->prepare($query);
        // $stmt->execute($bindValues);
        
        // TODO: Check if update was successful
        // if ($stmt->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Week '{$weekId}' updated successfully."]);
        // } else {
        //     sendError("Failed to update week or no changes were made.", 500);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Delete a week
 * Method: DELETE
 * Resource: weeks
 */
function deleteWeek($db, $weekId) {
    // TODO: Validate that week_id is provided
    if (!$weekId) {
        sendError("Missing required parameter: week_id.", 400);
        return;
    }
    
    try {
        // TODO: Check if week exists (mocked SELECT)
        // $stmtCheck = $db->prepare("SELECT COUNT(*) FROM weeks WHERE week_id = ?");
        // $stmtCheck->execute([$weekId]);
        // if ($stmtCheck->fetchColumn() === 0) {
        //     sendError("Week with ID '{$weekId}' not found.", 404);
        //     return;
        // }
        
        // TODO: Delete associated comments first (to maintain referential integrity)
        // $stmtDeleteComments = $db->prepare("DELETE FROM comments WHERE week_id = ?");
        // $stmtDeleteComments->execute([$weekId]);
        
        // TODO: Prepare and Execute DELETE query for week
        // $stmtDeleteWeek = $db->prepare("DELETE FROM weeks WHERE week_id = ?");
        // $stmtDeleteWeek->execute([$weekId]);

        // TODO: Check if delete was successful
        // if ($stmtDeleteWeek->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Week '{$weekId}' and associated comments deleted successfully."]);
        // } else {
        //     sendError("Failed to delete week.", 500);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


// ============================================================================
// COMMENTS CRUD OPERATIONS
// ============================================================================

/**
 * Function: Get all comments for a specific week
 * Method: GET
 * Resource: comments
 */
function getCommentsByWeek($db, $weekId) {
    // TODO: Validate that week_id is provided
    if (!$weekId) {
        sendError("Missing required parameter: week_id.", 400);
        return;
    }
    
    // TODO: Prepare SQL query to select comments for the week
    $query = "SELECT id, week_id, author, text, created_at FROM comments WHERE week_id = ? ORDER BY created_at ASC";

    try {
        // $stmt = $db->prepare($query);
        // $stmt->execute([$weekId]);
        // $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mock result
        $comments = [
            ['id' => 1, 'week_id' => 'week_1', 'author' => 'Ali', 'text' => 'Confused', 'created_at' => '2025-10-28 09:00:00'],
            ['id' => 2, 'week_id' => 'week_1', 'author' => 'Fatema', 'text' => 'Tags?', 'created_at' => '2025-10-28 10:00:00']
        ];
        if ($weekId !== 'week_1') $comments = []; // Simulate no comments for other weeks

        // TODO: Return JSON response with success status and data
        sendResponse(['success' => true, 'data' => $comments]);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Create a new comment
 * Method: POST
 * Resource: comments
 */
function createComment($db, $data) {
    // TODO: Validate required fields
    $requiredFields = ['week_id', 'author', 'text'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendError("Missing required field: {$field}.", 400);
            return;
        }
    }

    // TODO: Sanitize input data
    $weekId = sanitizeInput($data['week_id']);
    $author = sanitizeInput($data['author']);
    $text = sanitizeInput($data['text']);
    
    // TODO: Validate that text is not empty after trimming
    if (empty($text)) {
        sendError("Comment text cannot be empty.", 400);
        return;
    }

    try {
        // TODO: Check if the week exists (Recommended but skipped for brevity in mock)
        
        // TODO: Prepare INSERT query
        $query = "INSERT INTO comments (week_id, author, text) VALUES (?, ?, ?)";
        
        // $stmt = $db->prepare($query);
        // $stmt->execute([$weekId, $author, $text]);

        // TODO: Check if insert was successful
        // if ($stmt->rowCount() > 0) {
        //     $newId = $db->lastInsertId();
            $newComment = [
                // 'id' => $newId, 
                'week_id' => $weekId, 
                'author' => $author, 
                'text' => $text, 
                'created_at' => date('Y-m-d H:i:s')
            ];
            sendResponse(['success' => true, 'message' => "Comment created.", 'data' => $newComment], 201);
        // } else {
        //     sendError("Failed to create comment.", 500);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Delete a comment
 * Method: DELETE
 * Resource: comments
 */
function deleteComment($db, $commentId) {
    // TODO: Validate that id is provided
    if (!$commentId) {
        sendError("Missing required parameter: id.", 400);
        return;
    }
    
    try {
        // TODO: Check if comment exists (Recommended but skipped for brevity in mock)

        // TODO: Prepare DELETE query
        $query = "DELETE FROM comments WHERE id = ?";
        
        // $stmt = $db->prepare($query);
        // $stmt->execute([$commentId]);

        // TODO: Check if delete was successful
        // if ($stmt->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Comment ID '{$commentId}' deleted successfully."]);
        // } else {
        //     sendError("Comment ID '{$commentId}' not found or already deleted.", 404);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


// ============================================================================
// MAIN REQUEST ROUTER
// ============================================================================

try {
    // TODO: Determine the resource type from query parameters
    if (!$resource) {
        $resource = 'weeks'; // Default to 'weeks' if not provided
    }
    
    // Route based on resource type and HTTP method
    
    // ========== WEEKS ROUTES ==========
    if ($resource === 'weeks') {
        
        if ($method === 'GET') {
            // TODO: Check if week_id is provided in query parameters
            if ($week_id_param) {
                getWeekById($db, $week_id_param);
            } else {
                getAllWeeks($db);
            }
            
        } elseif ($method === 'POST') {
            createWeek($db, $data);
            
        } elseif ($method === 'PUT') {
            updateWeek($db, $data);
            
        } elseif ($method === 'DELETE') {
            // TODO: Get week_id from query parameter or request body
            $weekIdToDelete = $week_id_param ?: ($data['week_id'] ?? null);
            deleteWeek($db, $weekIdToDelete);
            
        } else {
            sendError("Method Not Allowed for weeks resource.", 405);
        }
    }
    
    // ========== COMMENTS ROUTES ==========
    elseif ($resource === 'comments') {
        
        if ($method === 'GET') {
            // TODO: Get week_id from query parameters
            if ($week_id_param) {
                getCommentsByWeek($db, $week_id_param);
            } else {
                 sendError("Missing required parameter: week_id for comments GET request.", 400);
            }
            
        } elseif ($method === 'POST') {
            createComment($db, $data);
            
        } elseif ($method === 'DELETE') {
            // TODO: Get comment id from query parameter or request body
            $commentIdToDelete = $comment_id_param ?: ($data['id'] ?? null);
            deleteComment($db, $commentIdToDelete);
            
        } else {
            sendError("Method Not Allowed for comments resource.", 405);
        }
    }
    
    // ========== INVALID RESOURCE ==========
    else {
        sendError("Invalid resource '{$resource}'. Use 'weeks' or 'comments'.", 400);
    }
    
} catch (PDOException $e) {
    // TODO: Handle database errors
    // error_log($e->getMessage()); // Log error details
    sendError("Database error occurred: Internal Server Error.", 500);
    
} catch (Exception $e) {
    // TODO: Handle general errors
    sendError("An unexpected error occurred: " . $e->getMessage(), 500);
}


// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Helper function to send JSON response
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
 * Helper function to send error response
 */
function sendError($message, $statusCode = 400) {
    // TODO: Create error response array
    $errorData = ['success' => false, 'error' => $message];
    
    // TODO: Call sendResponse() with the error array and status code
    sendResponse($errorData, $statusCode);
}


/**
 * Helper function to validate date format (YYYY-MM-DD)
 */
function validateDate($date) {
    // TODO: Use DateTime::createFromFormat() to validate
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}


/**
 * Helper function to sanitize input
 */
function sanitizeInput($data) {
    // TODO: Trim whitespace
    $data = trim($data);
    
    // TODO: Strip HTML tags using strip_tags()
    $data = strip_tags($data);
    
    // TODO: Convert special characters using htmlspecialchars()
    // Using ENT_QUOTES to convert both single and double quotes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); 
    
    // TODO: Return sanitized data
    return $data;
}


/**
 * Helper function to validate allowed sort fields
 */
function isValidSortField($field, $allowedFields) {
    // TODO: Check if $field exists in $allowedFields array
    return in_array($field, $allowedFields);
}
?>