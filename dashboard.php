<?php
// CRUCIAL: Start the session to access login status
session_start();

// 1. Authentication Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// 2. Logout Logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php'); 
    exit;
}


// 3. Database connection and data retrieval (FIXED: USE INCLUDE)
// Note: It's safer to use a separate file for database credentials.
// include 'db_connect.php'; 
$conn = mysqli_connect('localhost', 'admin', '1234', 'portfolio_db');

// Check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
    exit;
}

//get database
$sql = 'SELECT ID, Name, Email, Message, Date_sent FROM contact_messages ORDER BY Date_sent DESC'; // Added ORDER BY

//make query & get result
$result = mysqli_query($conn, $sql);

//fetch the results 
$contact_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free result set and close connection
mysqli_free_result($result);
mysqli_close($conn);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Portfolio</title>
    <style>
        /* [Style definitions omitted for brevity] */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa; 
        }
        
        .dashboard-header {
            background: #2c3e50; 
            color: white;
            padding: 2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            font-size: 2rem;
            color: #3498db; 
        }
        
        .logout-button {
            background: #136ca7ff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout-button:hover {
            background: #0b629cff;
        }

        .container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }
        
        .container h2 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #3498db; 
            padding-bottom: 0.5rem;
        }

        .message-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            border-radius: 8px;
            overflow: hidden;
        }

        .message-table th, .message-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .message-table th {
            background: #3498db; 
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .message-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 62, 80, 0.95); 
            display: none; 
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            max-width: 90%;
            width: 400px;
            position: relative;
        }

        #logout-modal:target {
            display: flex; 
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

        .modal-content h2 {
            font-size: 1.8rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .modal-actions {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-cancel {
            background: #95a5a6; 
            color: white;
        }
        .btn-cancel:hover {
            background: #7f8c8d;
        }

        .btn-confirm {
            background: #3498db;
            color: white;
        }
        .btn-confirm:hover {
            background: #0d6199ff;
        }
        
        @media (max-width: 900px) {
            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
            }
            .message-table, .message-table thead, .message-table tbody, .message-table th, .message-table td, .message-table tr { 
                display: block; 
            }
            .message-table thead tr { 
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            .message-table tr { 
                border: 1px solid #ccc; 
                margin-bottom: 1rem;
                border-radius: 8px;
            }
            .message-table td { 
                border: none;
                border-bottom: 1px solid #eee; 
                position: relative;
                padding-left: 50%; 
                text-align: right;
            }
            .message-table td:before { 
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%; 
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #2c3e50;
            }
        }

    </style>
</head>
<body>
    
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        
        <a href="#logout-modal" class="logout-button">
            Logout
        </a>
    </div>

    <div class="container">
        <h2>Contact Messages</h2>
        
        <table class="message-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date Sent</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contact_messages)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No contact messages found in the database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contact_messages as $message): ?>
                        <tr>
                            <td data-label="Name"><?php echo htmlspecialchars($message['Name']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($message['Email']); ?></td>
                            <td data-label="Message"><?php echo htmlspecialchars($message['Message']); ?></td>
                            <td data-label="Date Sent"><?php echo htmlspecialchars($message['Date_sent']); ?></td>
                        </tr>
                    <?php endforeach;  ?>
                <?php endif; ?>
            </tbody>
        </table>
        
    </div>

    <div id="logout-modal" class="modal-overlay">
        <div class="modal-content">
            <a href="#" class="close-button" title="Close Modal">&times;</a>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to end your administrative session?</p>
            <div class="modal-actions">
                <a href="#" class="modal-btn btn-cancel">Cancel</a>
                <a href="dashboard.php?logout=true" class="modal-btn btn-confirm">Yes, Logout</a>
            </div>
        </div>
    </div>
    </body>
</html>