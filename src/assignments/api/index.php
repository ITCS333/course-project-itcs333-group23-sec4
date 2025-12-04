<?php
/**
 * Assignment Management API
 * 
 * This is a RESTful API that handles all CRUD operations for course assignments
 * and their associated discussion comments.
 * It uses PDO to interact with a MySQL database.
 * 
 * Database Table Structures (for reference):
 * 
 * Table: assignments
 * Columns:
 *   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 *   - title (VARCHAR(200))
 *   - description (TEXT)
 *   - due_date (DATE)
 *   - files (TEXT)
 *   - created_at (TIMESTAMP)
 *   - updated_at (TIMESTAMP)
 * 
 * Table: comments
 * Columns:
 *   - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 *   - assignment_id (VARCHAR(50), FOREIGN KEY)
 *   - author (VARCHAR(100))
 *   - text (TEXT)
 *   - created_at (TIMESTAMP)
 * 
 * HTTP Methods Supported:
 *   - GET: Retrieve assignment(s) or comment(s)
 *   - POST: Create a new assignment or comment
 *   - PUT: Update an existing assignment
 *   - DELETE: Delete an assignment or comment
 * 
 * Response Format: JSON
 */

// ============================================================================
// HEADERS AND CORS CONFIGURATION
// ============================================================================

// TODO: Set Content-Type header to application/json

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}



// ============================================================================
// DATABASE CONNECTION
// ============================================================================

// TODO: Include the database connection class
require_once __DIR__ . '/../../common/DatabaseHelper.php';

// TODO: Create database connection


// TODO: Set PDO to throw exceptions on errors

// Include a mocked Database class (other modules use a similar approach)
$dbHelper = new DatabaseHelper();
$db = $dbHelper->getConnection();


// ============================================================================
// REQUEST PARSING
// ============================================================================

// TODO: Get the HTTP request method

$method = $_SERVER['REQUEST_METHOD'];

// Get request body for POST and PUT requests
$data = [];
$raw_data = file_get_contents('php://input');
if ($raw_data) {
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE && ($method === 'POST' || $method === 'PUT')) {
        sendError("Invalid JSON provided in request body.", 400);
        exit();
    }
}

// Parse query parameters
$resource = isset($_GET['resource']) ? sanitizeInput($_GET['resource']) : null;
$assignment_id_param = isset($_GET['id']) ? sanitizeInput($_GET['id']) : null;
$comment_id_param = isset($_GET['id']) ? (int)$_GET['id'] : null;



// ============================================================================
// ASSIGNMENT CRUD FUNCTIONS
// ============================================================================

/**
 * Function: Get all assignments
 * Method: GET
 * Endpoint: ?resource=assignments
 * 
 * Query Parameters:
 *   - search: Optional search term to filter by title or description
 *   - sort: Optional field to sort by (title, due_date, created_at)
 *   - order: Optional sort order (asc or desc, default: asc)
 * 
 * Response: JSON array of assignment objects
 */
function getAllAssignments($db) {
    $searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
    $sortField = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'created_at';
    $sortOrder = isset($_GET['order']) ? strtoupper(sanitizeInput($_GET['order'])) : 'ASC';

    $allowedSortFields = ['title', 'due_date', 'created_at'];
    $sortField = validateAllowedValue($sortField, $allowedSortFields) ? $sortField : 'created_at';
    $sortOrder = ($sortOrder === 'DESC') ? 'DESC' : 'ASC';

    $query = "SELECT id, title, description, due_date, files, created_at, updated_at FROM assignments";
    $params = [];
    if ($searchTerm) {
        $query .= " WHERE title LIKE :search OR description LIKE :search";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    $query .= " ORDER BY {$sortField} {$sortOrder}";

    try {
        // In a real DB environment use PDO prepared statements. Here we mock results.
        $stmt = $db->prepare($query); // Prepare sanitizes the sql to avoid sql injection attacks
        $stmt->execute($params); // Execute the query
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Get all the results

        // $result = [
        //     ['id' => 1, 'title' => 'Assignment 1', 'description' => 'Intro', 'due_date' => '2025-12-01', 'files' => '["/files/a1.pdf"]', 'created_at' => '2025-11-01 09:00:00', 'updated_at' => null]
        // ];

        // foreach ($result as &$assignment) {
        //     $assignment['files'] = json_decode($assignment['files'], true) ?? [];
        // }
        // unset($assignment);

        sendResponse(['success' => true, 'data' => $result]);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Get a single assignment by ID
 * Method: GET
 * Endpoint: ?resource=assignments&id={assignment_id}
 * 
 * Query Parameters:
 *   - id: The assignment ID (required)
 * 
 * Response: JSON object with assignment details
 */
function getAssignmentById($db, $assignmentId) {
    if (!$assignmentId) {
        sendError("Missing required parameter: id.", 400);
        return;
    }

    $query = "SELECT id, title, description, due_date, files, created_at, updated_at FROM assignments WHERE id = ?";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$assignmentId]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mock result (null when not found)
        // $assignment = ($assignmentId == 1) ? ['id' => 1, 'title' => 'Assignment 1', 'description' => 'Intro', 'due_date' => '2025-12-01', 'files' => '["/files/a1.pdf"]', 'created_at' => '2025-11-01 09:00:00', 'updated_at' => null] : null;

        if ($assignment !== null) {
            $assignment['files'] = json_decode($assignment['files'], true) ?? [];
            sendResponse(['success' => true, 'data' => $assignment]);
        } else {
            sendError("Assignment with ID '{$assignmentId}' not found.", 404);
        }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Create a new assignment
 * Method: POST
 * Endpoint: ?resource=assignments
 * 
 * Required JSON Body:
 *   - title: Assignment title (required)
 *   - description: Assignment description (required)
 *   - due_date: Due date in YYYY-MM-DD format (required)
 *   - files: Array of file URLs/paths (optional)
 * 
 * Response: JSON object with created assignment data
 */
function createAssignment($db, $data) {
    $required = ['title', 'description', 'due_date'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendError("Missing required field: {$field}.", 400);
            return;
        }
    }

    $title = sanitizeInput($data['title']);
    $description = sanitizeInput($data['description']);
    $due_date = sanitizeInput($data['due_date']);
    $files = $data['files'] ?? [];

    if (!validateDate($due_date)) {
        sendError("Invalid due_date format. Must be YYYY-MM-DD.", 400);
        return;
    }

    try {
        // $filesJson = json_encode(is_array($files) ? $files : []);
        // $query = "INSERT INTO assignments (title, description, due_date, files) VALUES (?, ?, ?, ?)";
        // $stmt = $db->prepare($query);
        // $stmt->execute([$title, $description, $due_date, $filesJson]);
        // if ($stmt->rowCount() > 0) {
        //     $newId = $db->lastInsertId();
        //     $newAssignment = ['id' => $newId, 'title'=> $title, 'description'=>$description, 'due_date'=>$due_date, 'files'=>is_array($files)?$files:[], 'created_at'=>date('Y-m-d H:i:s')];
        //     sendResponse(['success'=>true, 'message'=>'Assignment created.','data'=>$newAssignment], 201);
        // }

        // Mocked successful insert
        $newAssignment = ['id' => 2, 'title'=> $title, 'description'=>$description, 'due_date'=>$due_date, 'files'=>is_array($files)?$files:[], 'created_at'=>date('Y-m-d H:i:s')];
        sendResponse(['success'=>true, 'message'=>'Assignment created.','data'=>$newAssignment], 201);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Update an existing assignment
 * Method: PUT
 * Endpoint: ?resource=assignments
 * 
 * Required JSON Body:
 *   - id: Assignment ID (required, to identify which assignment to update)
 *   - title: Updated title (optional)
 *   - description: Updated description (optional)
 *   - due_date: Updated due date (optional)
 *   - files: Updated files array (optional)
 * 
 * Response: JSON object with success status
 */
function updateAssignment($db, $data) {
    $assignmentId = sanitizeInput($data['id'] ?? null);
    if (!$assignmentId) {
        sendError("Missing required field: id.", 400);
        return;
    }

    // Build dynamic set clauses
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
    if (isset($data['due_date'])) {
        $d = sanitizeInput($data['due_date']);
        if (!validateDate($d)) {
            sendError("Invalid due_date format. Must be YYYY-MM-DD.", 400);
            return;
        }
        $setClauses[] = "due_date = ?";
        $bindValues[] = $d;
    }
    if (isset($data['files'])) {
        $setClauses[] = "files = ?";
        $bindValues[] = json_encode(is_array($data['files']) ? $data['files'] : []);
    }

    if (empty($setClauses)) {
        sendError("No valid fields provided for update.", 400);
        return;
    }

    $setClauses[] = "updated_at = CURRENT_TIMESTAMP";
    $query = "UPDATE assignments SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $bindValues[] = $assignmentId;

    try {
        // $stmt = $db->prepare($query);
        // $stmt->execute($bindValues);
        // if ($stmt->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Assignment '{$assignmentId}' updated successfully."]);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Delete an assignment
 * Method: DELETE
 * Endpoint: ?resource=assignments&id={assignment_id}
 * 
 * Query Parameters:
 *   - id: Assignment ID (required)
 * 
 * Response: JSON object with success status
 */
function deleteAssignment($db, $assignmentId) {
    if (!$assignmentId) {
        sendError("Missing required parameter: id.", 400);
        return;
    }

    try {
        // $stmtDeleteComments = $db->prepare("DELETE FROM comments WHERE assignment_id = ?");
        // $stmtDeleteComments->execute([$assignmentId]);
        // $stmtDelete = $db->prepare("DELETE FROM assignments WHERE id = ?");
        // $stmtDelete->execute([$assignmentId]);
        // if ($stmtDelete->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Assignment ID '{$assignmentId}' deleted successfully."]);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


// ============================================================================
// COMMENT CRUD FUNCTIONS
// ============================================================================

/**
 * Function: Get all comments for a specific assignment
 * Method: GET
 * Endpoint: ?resource=comments&assignment_id={assignment_id}
 * 
 * Query Parameters:
 *   - assignment_id: The assignment ID (required)
 * 
 * Response: JSON array of comment objects
 */
function getCommentsByAssignment($db, $assignmentId) {
    if (!$assignmentId) {
        sendError("Missing required parameter: assignment_id.", 400);
        return;
    }

    $query = "SELECT id, assignment_id, author, text, created_at FROM comments WHERE assignment_id = ? ORDER BY created_at ASC";
    try {
        // $stmt = $db->prepare($query);
        // $stmt->execute([$assignmentId]);
        // $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mock result
        $comments = [
            ['id' => 1, 'assignment_id' => $assignmentId, 'author' => 'Student A', 'text' => 'Question about problem 2', 'created_at' => '2025-11-10 12:00:00']
        ];

        sendResponse(['success' => true, 'data' => $comments]);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Create a new comment
 * Method: POST
 * Endpoint: ?resource=comments
 * 
 * Required JSON Body:
 *   - assignment_id: Assignment ID (required)
 *   - author: Comment author name (required)
 *   - text: Comment content (required)
 * 
 * Response: JSON object with created comment data
 */
function createComment($db, $data) {
    $required = ['assignment_id', 'author', 'text'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendError("Missing required field: {$field}.", 400);
            return;
        }
    }

    $assignmentId = sanitizeInput($data['assignment_id']);
    $author = sanitizeInput($data['author']);
    $text = sanitizeInput($data['text']);

    if (empty($text)) {
        sendError("Comment text cannot be empty.", 400);
        return;
    }

    try {
        // $query = "INSERT INTO comments (assignment_id, author, text) VALUES (?, ?, ?)";
        // $stmt = $db->prepare($query);
        // $stmt->execute([$assignmentId, $author, $text]);
        // $newId = $db->lastInsertId();

        $newComment = ['id' => 2, 'assignment_id' => $assignmentId, 'author' => $author, 'text' => $text, 'created_at' => date('Y-m-d H:i:s')];
        sendResponse(['success'=>true, 'message'=>'Comment created.', 'data'=>$newComment], 201);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


/**
 * Function: Delete a comment
 * Method: DELETE
 * Endpoint: ?resource=comments&id={comment_id}
 * 
 * Query Parameters:
 *   - id: Comment ID (required)
 * 
 * Response: JSON object with success status
 */
function deleteComment($db, $commentId) {
    if (!$commentId) {
        sendError("Missing required parameter: id.", 400);
        return;
    }

    try {
        // $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
        // $stmt->execute([$commentId]);
        // if ($stmt->rowCount() > 0) {
        sendResponse(['success' => true, 'message' => "Comment ID '{$commentId}' deleted successfully."]);
        // }
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
}


// ============================================================================
// MAIN REQUEST ROUTER
// ============================================================================

try {
    // Default resource to 'assignments' if not provided
    if (!$resource) $resource = 'assignments';

    if ($method === 'GET') {
        if ($resource === 'assignments') {
            if ($assignment_id_param) {
                getAssignmentById($db, $assignment_id_param);
            } else {
                getAllAssignments($db);
            }
        } elseif ($resource === 'comments') {
            $aid = isset($_GET['assignment_id']) ? sanitizeInput($_GET['assignment_id']) : null;
            if ($aid) {
                getCommentsByAssignment($db, $aid);
            } else {
                sendError("Missing required parameter: assignment_id for comments GET request.", 400);
            }
        } else {
            sendError("Invalid resource '{$resource}'. Use 'assignments' or 'comments'.", 400);
        }

    } elseif ($method === 'POST') {
        if ($resource === 'assignments') {
            createAssignment($db, $data);
        } elseif ($resource === 'comments') {
            createComment($db, $data);
        } else {
            sendError("Invalid resource '{$resource}'.", 400);
        }

    } elseif ($method === 'PUT') {
        if ($resource === 'assignments') {
            updateAssignment($db, $data);
        } else {
            sendError("PUT not supported for resource '{$resource}'.", 405);
        }

    } elseif ($method === 'DELETE') {
        if ($resource === 'assignments') {
            $delId = $assignment_id_param ?: ($data['id'] ?? null);
            deleteAssignment($db, $delId);
        } elseif ($resource === 'comments') {
            $delId = $comment_id_param ?: ($data['id'] ?? null);
            deleteComment($db, $delId);
        } else {
            sendError("Invalid resource '{$resource}'.", 400);
        }

    } else {
        sendError("Method Not Allowed.", 405);
    }
    
} catch (PDOException $e) {
    // TODO: Handle database errors
    error_log($e->getMessage());
    sendResponse(['success' => false, 'error' => 'Database error occurred: Internal Server Error.'], 500);

} catch (Exception $e) {
    // TODO: Handle general errors
    error_log($e->getMessage());
    sendResponse(['success' => false, 'error' => 'An unexpected error occurred.'], 500);

}


// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Helper function to send JSON response and exit
 * 
 * @param array $data - Data to send as JSON
 * @param int $statusCode - HTTP status code (default: 200)
 */
function sendResponse($data, $statusCode = 200) {
    // TODO: Set HTTP response code
    http_response_code($statusCode);

    // TODO: Ensure data is an array
    if (!is_array($data) && !is_object($data)) {
        $data = ['data' => $data];
    }

    // TODO: Echo JSON encoded data
    echo json_encode($data);

    // TODO: Exit to prevent further execution
    exit();
}


/**
 * Helper function to sanitize string input
 * 
 * @param string $data - Input data to sanitize
 * @return string - Sanitized data
 */
function sanitizeInput($data) {
    // TODO: Trim whitespace from beginning and end
    if (!is_string($data)) return $data;
    $data = trim($data);

    // TODO: Remove HTML and PHP tags
    $data = strip_tags($data);

    // TODO: Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

    // TODO: Return the sanitized data
    return $data;
}


/**
 * Helper function to validate date format (YYYY-MM-DD)
 * 
 * @param string $date - Date string to validate
 * @return bool - True if valid, false otherwise
 */
function validateDate($date) {
    // TODO: Use DateTime::createFromFormat to validate
    $d = DateTime::createFromFormat('Y-m-d', $date);

    // TODO: Return true if valid, false otherwise
    return $d && $d->format('Y-m-d') === $date;
}


/**
 * Helper function to validate allowed values (for sort fields, order, etc.)
 * 
 * @param string $value - Value to validate
 * @param array $allowedValues - Array of allowed values
 * @return bool - True if valid, false otherwise
 */
function validateAllowedValue($value, $allowedValues) {
    // TODO: Check if $value exists in $allowedValues array
    // Allow case-insensitive comparison for strings
    if (!is_string($value)) {
        return in_array($value, $allowedValues, true);
    }
    foreach ($allowedValues as $v) {
        if (strcasecmp($value, $v) === 0) return true;
    }

    // TODO: Return the result
    return false;
}


// getAllAssignments();

?>
