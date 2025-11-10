<?php 
// Get name from Step 1 (POST for form submission, GET for redirects)
$visitor_name = null;

// Check POST first (Step 1 form submission)
if (isset($_POST['name']) && !empty($_POST['name'])) {
    $visitor_name = htmlspecialchars($_POST['name']);
}
// Check GET (redirects from contact_submit.php)
elseif (isset($_GET['name']) && !empty($_GET['name'])) {
    $visitor_name = htmlspecialchars($_GET['name']);
}

$is_step_two = !empty($visitor_name);

// Check for submission status from contact_submit.php (GET parameter 'status')
$submission_status = isset($_GET['status']) ? $_GET['status'] : null;

// Name for display
$name_for_display = $visitor_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <style>
        /* Base Styling - Consistent with Portfolio Index */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #ecf0f1; /* Light background */
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header (Same Dark Navy) */
        header {
            background: #2c3e50;
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }

        header h1 {
            font-size: 2rem;
        }

        /* Main Content Container */
        .container {
            flex-grow: 1; 
            max-width: 600px;
            margin: 4rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Forms and Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db; 
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        /* Buttons (Accent Blue) */
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        .btn:hover {
            background: #2980b9;
        }
        
        /* Message Styling */
        .welcome-message {
            background: #f8f9fa; 
            border-left: 5px solid #3498db; 
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 5px;
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: bold;
        }
        
        .welcome-message span {
            color: #3498db; 
        }
        
        /* Submission Status Messages */
        .status-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Back Button for Navigation */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: #3498db;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Contact Page</h1>
    </header>

    <div class="container">
        <?php if ($submission_status === 'success'): ?>
            <div class="status-message status-success">
                ✓ Thank you, <?php echo $name_for_display; ?>! Your message has been successfully sent.
            </div>
        <?php elseif ($submission_status === 'error'): ?>
            <div class="status-message status-error">
                ✗ An error occurred while saving your message. Please try again.
            </div>
        <?php endif; ?>
        
        <?php if (!$is_step_two): ?>
            <h2>Let's Start with Your Name</h2>
            <p>Please enter your name so I can greet you properly!</p>
            
            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>
                <button type="submit" class="btn">Continue to Contact Form</button>
            </form>
            
        <?php else: ?>
            <div class="welcome-message">
                Welcome <span><?php echo $visitor_name; ?></span>!
            </div>
            
            <h2>Send Me a Message</h2>
            <p>I appreciate you reaching out! Please fill out the form below and I will get back to you soon.</p>
            
            <form method="POST" action="contact_submit.php"> 
                <!-- FIXED: Changed from visitor_name to Name to match database -->
                <input type="hidden" name="Name" value="<?php echo htmlspecialchars($visitor_name); ?>">
                
                <div class="form-group">
                    <label for="Email">Your Email</label>
                    <input type="email" id="Email" name="Email" required placeholder="e.g., your.email@example.com">
                </div>
                
                <div class="form-group">
                    <label for="Message">Message</label>
                    <!-- FIXED: Changed from message to Message to match database -->
                    <textarea id="Message" name="Message" required placeholder="Write your message here..."></textarea>
                </div>

                <button type="submit" class="btn">Send Message</button>
            </form>
            
        <?php endif; ?>
        
        <a href="index.php" class="back-link">← Back to Portfolio</a>
    </div>

</body>
</html>