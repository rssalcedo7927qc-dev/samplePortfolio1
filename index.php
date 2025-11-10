<?php 
// --- STEP 1: START THE SESSION ---
session_start();

// Check if the 'visitor_name' parameter exists in the URL
$visitor_name = isset($_GET['visitor_name']) ? htmlspecialchars($_GET['visitor_name']) : null;

// --- Security Questions Configuration (Only for displaying the question text) ---
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


// --- STEP 2: HANDLE CANCEL ACTION (FIX FOR THE 'X' BUTTON) ---
if (isset($_GET['action']) && $_GET['action'] === 'cancel_security') {
    // Clear all temporary session data and redirect to a clean URL
    unset($_SESSION['auth_step']);
    unset($_SESSION['current_question_index']);
    unset($_SESSION['temp_username']);
    unset($_SESSION['temp_password']);
    unset($_SESSION['security_error']);
    header('Location: index.php');
    exit();
}

// --- STEP 3: CHECK SESSION FOR AUTHENTICATION STATE AND ERROR ---
$login_error_message = $_SESSION['login_error'] ?? null;
$security_error_message = $_SESSION['security_error'] ?? null;
$is_security_step = isset($_SESSION['auth_step']) && $_SESSION['auth_step'] === 'security';

// Prepare data for the security modal if needed
$current_question = null;
if ($is_security_step) {
    $q_index = $_SESSION['current_question_index'] ?? null;
    if ($q_index !== null && isset($security_questions[$q_index])) {
        $current_question = $security_questions[$q_index]['q'];
    }
}

// --- STEP 4: CLEAR THE SESSION ERRORS IMMEDIATELY (Flash Messages) ---
if ($login_error_message) {
    unset($_SESSION['login_error']);
}
if ($security_error_message) {
    unset($_SESSION['security_error']);
}

// Redirect already logged-in users (optional but good practice)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Set a body class to automatically show the security modal if in the step
$body_class = $is_security_step ? 'show-security-modal' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | IT Student</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* --- MODAL BASE STYLES (SHARED FOR WELCOME AND ADMIN) --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 62, 80, 0.95); /* Dark overlay */
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* NEW: Class to force display of security modal via PHP */
        .show-security-modal #security-modal {
            display: flex;
        }

        /* CSS TARGET HACK: Shows the modal when its ID matches the URL hash */
        #admin-modal:target,
        #security-modal:target {
            display: flex; 
        }
        
        /* Re-apply display:flex for the PHP-driven welcome modal if needed */
        .welcome-modal {
             display: flex;
        }

        /* NEW: Admin modal content size */
        #admin-modal .modal-content {
            width: 500px; /* Original size */
        }

        /* NEW: Security modal content size (small modal) */
        #security-modal .modal-content {
            padding: 30px;
        }


        .modal-content {
            background: #fff;
            color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            max-width: 90%;
            position: relative;
        }
        
        .modal-content h2 {
            font-size: 2.2rem;
            color: #3498db; /* Primary accent blue */
            margin-bottom: 1rem;
        }

        .modal-close {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .modal-close:hover {
            background: #2980b9;
        }
        
        .close-button {
            position: absolute;
            top: 15px;
            right: 15px;
            text-decoration: none;
            color: #95a5a6;
            font-size: 1.5rem;
            line-height: 1;
        }
        .close-button:hover {
            color: #34495e;
        }
        
        /* --- ADMIN FORM SPECIFIC STYLES --- */
        .login-content h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: bold;
            color: #333;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
        }
        
        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .btn-login {
            display: block;
            width: 100%;
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: bold;
            margin-top: 1.5rem;
        }

        .btn-login:hover {
           background: #136ca7ff;
        }
        
        /* NEW: Style for the clickable validation text */
        .validation-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #3498db;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: bold;
            text-decoration: underline;
        }
        .validation-link:hover {
            color: #2980b9;
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da; /* Light red background for visibility */
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        /* NEW: Security Modal Specific Styles */
        .security-content h3 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .security-content .question-text {
            display: block;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        /* --- END MODAL STYLES --- */


        /* Header */
        header {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 2rem 0; 
            text-align: center;
        }
    
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            flex-direction: column; 
            align-items: center;
            text-align: center;
        }

        .header-text {
            text-align: inherit; 
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        header p {
            font-size: 1.1rem;
            color: #ecf0f1;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1.5rem; 
            border: 5px solid #3498db; 
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Navigation */
        nav {
            background: #34495e;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Container for Nav Links */
        .nav-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .nav-links {
            display: flex;
            flex-grow: 1; 
            justify-content: flex-start; 
        }

        .nav-utility {
            display: flex;
            justify-content: flex-end; 
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            display: inline-block;
        }

        nav a:hover {
            background: #2c3e50;
        }
        
        /* Highlight the Admin/Contact link */
        .nav-utility a {
            background: #3498db; 
            border-radius: 5px;
            margin-left: 10px;
        }
        
        .nav-utility a:hover {
             background: #2980b9;
        }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Section */
        section {
            margin-bottom: 4rem;
        }

        section h2 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
        }

        /* About Section */
        .about-content {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
        }

        .about-content p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .education {
            background: white;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border-left: 4px solid #3498db;
        }

        .education h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .education p {
            color: #666;
        }

        /* Skills Section */
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .skill-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .skill-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        /* SKILL BAR STYLES */
        .skill-item {
            margin-bottom: 1rem;
        }

        .skill-name {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            font-weight: bold;
            color: #555;
        }

        .skill-bar {
            background: #ecf0f1;
            border-radius: 5px;
            height: 10px;
            overflow: hidden;
        }

        .skill-level {
            height: 100%;
            background: #3498db;
            transition: width 1s ease-in-out; 
        }
        /* END SKILL BAR STYLES */

        /* Projects Section */
        .projects-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 3rem;
        }

        .project-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            margin-bottom: 50px;
        }

        .project-header {
            color: white;
            text-align: center;
            font-size: 3rem;
            width: 100%;
            height: 50%;
        }

        .project-body {
            padding: 1.5rem;
        }

        .project-body h3 {
            color: #2c3e50;
            margin-bottom: 0.75rem;
        }

        .project-body p {
            color: #666;
            margin-bottom: 1rem;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag {
            background: #ecf0f1;
            color: #2c3e50;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
        }

        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        ul li {
            margin: 0px 0px 10px 5px;
        }
        
        /* Responsive */
        @media (min-width: 768px) {
            .header-content {
                flex-direction: row-reverse; 
                justify-content: space-between;
            }

            .header-text {
                text-align: left;
                max-width: 60%;
            }

            .profile-photo {
                margin-bottom: 0;
            }
        }
        
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            
            .nav-container {
                flex-direction: column;
                align-items: stretch;
            }

            .nav-links, .nav-utility {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                width: 100%;
            }

            nav a {
                display: block;
                padding: 0.75rem;
            }

            .nav-utility a {
                margin: 0; 
            }

            .container {
                padding: 2rem 1rem;
            }

            section h2 {
                font-size: 1.5rem;
            }

            .skills-grid,
            .projects-grid {
                grid-template-columns: 1fr;
            }

            .social-links {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="<?php echo $body_class; ?>">
    
    <?php if ($visitor_name): ?>
        <div id="welcome-modal" class="modal-overlay welcome-modal">
            <div class="modal-content">
                <h2>Welcome <?php echo $visitor_name; ?>!</h2>
                <p>Enjoy your visit to my Portfolio! 👋</p>
                <a href="index.php" class="modal-close">View Portfolio</a>
            </div>
        </div>
    <?php endif; ?>
    
    <div id="admin-modal" class="modal-overlay">
        <div class="modal-content login-content">
            <?php if (!$is_security_step): ?>
                <a href="#" class="close-button" title="Close Modal">&times;</a> 
            <?php endif; ?>
            
            <h2>Admin Panel Login</h2>

            <?php if ($login_error_message): ?>
                <div class="error-message">Invalid username or password. Please try again.</div>
            <?php endif; ?>

            <form method="POST" action="admin.php" id="admin-login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off" placeholder="admin" 
                           value="<?php echo htmlspecialchars($_SESSION['temp_username'] ?? ''); // Retain username for convenience ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="123456">
                </div>
                
                <button type="submit" class="btn-login" id="login-submit-button">Login</button>
                
                <a href="javascript:void(0)" class="validation-link" onclick="document.getElementById('login-submit-button').click();">
                    this is me
                </a>
            </form>
            
        </div>
    </div>
    
    <div id="security-modal" class="modal-overlay">
        <div class="modal-content security-content">
            
            <a href="index.php?action=cancel_security" class="close-button" title="Close Modal">&times;</a> 
            
            <h2>Security Validation</h2>
            
            <?php if ($security_error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($security_error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($current_question): ?>
                <form method="POST" action="admin.php">
                    <p class="question-text">
                        <?php echo htmlspecialchars($current_question); ?>
                    </p>
                    
                    <div class="form-group">
                        <label for="security_answer">Your Answer</label>
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['temp_username'] ?? ''); ?>">
                        <input type="hidden" name="password" value="<?php echo htmlspecialchars($_SESSION['temp_password'] ?? ''); ?>">
                        
                        <input type="text" id="security_answer" name="security_answer" required autocomplete="off" placeholder="Type your answer here">
                    </div>
                    
                    <button type="submit" class="btn-login">Submit Validation</button>
                </form>
            <?php else: ?>
                <div class="error-message">Error: Security check failed to initialize. Please <a href="index.php" style="color: #c0392b;">start over</a>.</div>
            <?php endif; ?>
            
        </div>
    </div>
    
    <header>
        <div class="header-content">
            <div class="profile-photo">
                <img src="images/e39df579-5c92-43db-9762-f2a1c58513a9.jpg" alt="Profile Photo"> 
            </div>
            <div class="header-text">
                <h1>Ryusuke S. Salcedo</h1>
                <p>2nd Year IT Student | Our Lady of Fatima University</p>
            </div>
        </div>
    </header>

    <nav>
        <div class="nav-container">
            <div class="nav-links">
                <a href="#about">About</a>
                <a href="#skills">Skills</a>
                <a href="#projects">Projects</a>
            </div>
            
            <div class="nav-utility">
                <a href="contact.php">Contact</a>
                <a href="#admin-modal">Admin 🔒</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <section id="about">
            <h2>About Me</h2>
            <div class="about-content">
                <p>Hello! I'm a 2nd year Information Technology student at Our Lady of Fatima University, focused on web development and problem-solving.</p>
                <p>I enjoy working on projects that challenge me and help me grow as a developer. My goal is to become a skilled IT professional and contribute to innovative solutions in the tech industry.</p>
                
                <div class="education">
                    <h3>Education</h3>
                    <p><strong>Our Lady of Fatima University</strong></p>
                    <p>Bachelor of Science in Information Technology</p>
                    <p>2nd Year Student | 2024 - Present</p>
                </div>
            
            </div>
        </section>

        <section id="skills">
            <h2>My Skills</h2>
            <div class="skills-grid">
                
                <div class="skill-card">
                    <h3>Programming Languages</h3>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>HTML</span>
                            <span>90%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 90%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>CSS</span>
                            <span>75%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 75%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>C</span>
                            <span>65%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 65%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>JAVA</span>
                            <span>45%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 45%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>PHP</span>
                            <span>30%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 30%;"></div>
                        </div>
                    </div>

                    <div class="skill-item">
                        <div class="skill-name">
                            <span>JAVASCRIPT</span>
                            <span>20%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 20%;"></div>
                        </div>
                    </div>
                </div>

                <div class="skill-card">
                    <h3>Tools & Technologies</h3>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>CANVA</span>
                            <span>95%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 95%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>VS CODE</span>
                            <span>75%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 75%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>BOOTSTRAP</span>
                            <span>50%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 50%;"></div>
                        </div>
                    </div>
                    
                    <div class="skill-item">
                        <div class="skill-name">
                            <span>PHOTOSHOP</span>
                            <span>30%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: 30%;"></div>
                        </div>
                    </div>
                    
                </div>

                <div class="skill-card">
                    <h3>Soft Skills</h3>
                    <ul>
                        <li>Problem Solving</li>
                        <li>Teamwork</li>
                        <li>Communication</li>
                        <li>Time Management</li>
                        <li>Decision Making </li>
                        <li>Planning</li>
                        <li>Observant</li>
                    </ul>
                </div>
            </div>
        </section>
        <section id="projects">
            <h2>My Projects</h2>
            <div class="projects-grid">
                <div class="project-card">
                    <img class="project-header" src="images/E-COMMERCE WEBSITE.png" alt="image" height="120px">
                    <div class="project-body">
                        <h3>E-Commerce Website</h3>
                        <p>Harvest Hub is an online grocery store selling fresh, high-quality ingredients. It promotes itself as a "Fresh Market Destination," emphasizing locally sourced and sustainable products. The site features standard e-commerce elements like Products navigation, a shopping cart, and a prominent "Order now" button.</p>
                        <div class="tags">
                            <span class="tag">HTML</span>
                            <span class="tag">CSS</span>
                        </div>
                    </div>
                </div>

                <div class="project-card">
                    <img class="project-header" src="images/FORUM WEBSITE.png" alt="image" height="120px">
                    <div class="project-body">
                        <h3>Forum Website</h3>
                        <p>AGORA is a dedicated forum platform designed for people to connect and discuss. It features sections for the Dashboard, creating posts, trending topics, and various communities.</p>
                        <div class="tags">
                            <span class="tag">HTML</span>
                            <span class="tag">CSS</span>
                            <span class="tag">JAVASCRIPT</span>
                        </div>
                    </div>
                </div>

                <div class="project-card">
                    <img class="project-header" src="images/PORTFOLIO.png" alt="image" height="120px">
                    <div class="project-body">
                        <h3>SELF PORTFOLIO</h3>
                        <p>It is designed to introduce professional identity as a 2nd Year IT Student focused on web development and problem-solving. By featuring dedicated sections for Skills and Projects, the portfolio serves to visually demonstrate competence and abilities to potential employers or collaborators.</p>
                        <div class="tags">
                            <span class="tag">HTML</span>
                            <span class="tag">CSS</span>
                            <span class="tag">PHP</span>
                            <span class="tag">MySQL </span>
                        </div>
                    </div>
                </div>

        
               

                
            </div>
        </section>

    </div>

    <footer>
        <p>&copy; 2025 Ryusuke Salcedo | IT Student | Our Lady of Fatima University</p>
    </footer>
</body>
</html>