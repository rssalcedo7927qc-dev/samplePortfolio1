<?php
// --- START THE SESSION ---
session_start();

// --- Configuration: Set your correct credentials ---
$correct_username = "admin";
$correct_password = "123456";

// --- Security Questions Configuration (Keep secret!) ---
// NOTE: Use strong, private questions and answers
$security_questions = [
    ['q' => 'Sample Question 1: Answer is 1?', 'a' => '1'],
    ['q' => 'Sample Question 2: Answer is 2', 'a' => '2'],
    ['q' => 'Sample Question 3: Answer is 3', 'a' => '3'],
    ['q' => 'Sample Question 4: Answer is 4', 'a' => '4'],
    ['q' => 'Sample Question 5: Answer is 5', 'a' => '5'],
    ['q' => 'Sample Question 6: Answer is 6', 'a' => '6'],
    ['q' => 'Sample Question 7: Answer is 7', 'a' => '7'],
    ['q' => 'Sample Question 8: Answer is 8', 'a' => '8'],
    ['q' => 'Sample Question 9: Answer is 9', 'a' => '9'],
    ['q' => 'Sample Question 10: Answer is 10', 'a' => '10'],
];

// --- LOGIC START ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $submitted_username = $_POST['username'] ?? '';
    $submitted_password = $_POST['password'] ?? '';
    $security_answer = $_POST['security_answer'] ?? null;

    // A. VALIDATION STEP 1 (Username/Password Check - Triggered by the first modal submit)
    if (!isset($_SESSION['auth_step']) || $_SESSION['auth_step'] !== 'security') 
    {
        // 1. Check U/P fields
        if (empty($submitted_username) || empty($submitted_password)) {
            $_SESSION['login_error'] = "Username and password are required.";
            header("Location: index.php#admin-modal");
            exit();
        }
        
        // 2. Check credentials
        if ($submitted_username === $correct_username && $submitted_password === $correct_password) {
            
            // Success! Credentials correct. Move to security step.
            $_SESSION['auth_step'] = 'security';
            
            // Choose a random question and store its index
            $_SESSION['current_question_index'] = array_rand($security_questions);
            
            // Store the successful credentials temporarily to pass them to the next step
            $_SESSION['temp_username'] = $submitted_username;
            $_SESSION['temp_password'] = $submitted_password;

            // Redirect to index.php to show the security modal
            header("Location: index.php#security-modal");
            exit();
            
        } else {
            // U/P Failed
            $_SESSION['login_error'] = "Invalid username or password.";
            header("Location: index.php#admin-modal");
            exit();
        }
        
    } 
    
    // B. VALIDATION STEP 2 (Security Answer Check - Triggered by the second modal submit)
    else if (isset($_SESSION['auth_step']) && $_SESSION['auth_step'] === 'security') 
    {
        $q_index = $_SESSION['current_question_index'] ?? null;
        
        if ($q_index !== null && !empty($security_answer)) {
            
            $correct_answer = $security_questions[$q_index]['a'];
            
            // Check the answer (case-insensitive for a simple implementation)
            if (strtolower($security_answer) === strtolower($correct_answer)) {
                
                // --- FULL SUCCESSFUL LOGIN ---
                $_SESSION['logged_in'] = true;
                
                // Clear all temporary session variables
                unset($_SESSION['login_error']);
                unset($_SESSION['security_error']);
                unset($_SESSION['auth_step']);
                unset($_SESSION['current_question_index']);
                unset($_SESSION['temp_username']);
                unset($_SESSION['temp_password']);
                
                // Proceed to the dashboard
                header("Location: dashboard.php");
                exit();
                
            } else {
                
                // --- VALIDATION FAILED (Incorrect Answer) ---
                // Shows the specified error and retries with a new question
                $_SESSION['security_error'] = "Validation failed! Retry another set of question.";
                
                // Generate a new random question for the retry
                $_SESSION['current_question_index'] = array_rand($security_questions);
                
                // Redirect to show the security modal again with an error
                header("Location: index.php#security-modal");
                exit();
            }
        }
        // Fallback for missing answer/state
        header("Location: index.php");
        exit();
    }
    
} else {
    // If accessed directly without POST, redirect back to index.php 
    // to handle session-based modal display.
    header("Location: index.php");
    exit();
}
?>