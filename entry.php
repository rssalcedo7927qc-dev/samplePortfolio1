<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Portfolio Entry</title>
    <style>
        /* Shared Styles from Portfolio */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #ecf0f1; /* Light gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        /* Form Container - Using a muted dark color */
        .entry-container {
            background: #ffffff; /* White card */
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.15); /* Shadow using header color */
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .entry-container h1 {
            color: #2c3e50; /* Dark header color */
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #34495e; /* Navigation bar color */
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #3498db; /* Accent blue color */
        }

        /* Submit Button - Using the primary accent color */
        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background: #3498db; /* Primary accent blue */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: bold;
        }

        .submit-btn:hover {
            background: #2980b9; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="entry-container">
        <h1>Welcome to My Portfolio! 👋</h1>
        <p style="margin-bottom: 2rem; color: #666;">Please enter your name to proceed.</p>

        <form action="index.php" method="GET">
            <div class="form-group">
                <label for="visitor_name">Your Name:</label>
                <input type="text" id="visitor_name" name="visitor_name" required placeholder="e.g., Mr. Recruiter">
            </div>
            
            <button type="submit" class="submit-btn">
                View Ryusuke's Portfolio
            </button>
        </form>
    </div>
</body>
</html>