<?php
require_once __DIR__ . '/src/common/DatabaseHelper.php';

try {
    $dbHelper = new DatabaseHelper();
    $db = $dbHelper->getConnection();
    
    // Check if assignment 1 exists
    $check = $db->prepare("SELECT id FROM assignments WHERE id = 1");
    $check->execute();
    
    if ($check->fetch()) {
        echo "Assignment 1 already exists in database!\n";
    } else {
        // Insert Assignment 1
        $sql = "INSERT INTO assignments (id, title, description, due_date, files) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            1,
            'HTML & CSS Portfolio',
            'Create a responsive portfolio website using HTML5 and CSS3. Must include: header, navigation, main content area, and footer.',
            '2025-02-15',
            '["https://example.com/files/assignment1-brief.pdf", "https://example.com/files/starter-template.zip"]'
        ]);
        
        echo "✅ Assignment 1 restored successfully!\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
