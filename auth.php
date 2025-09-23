<?php
session_start();

// ===== Single User Configuration =====
$username = 'THISSIWHEREYOURUSERNAMEGOES';  // Change this to whatever you want
$password = 'BLABLABLABLA';  // Change this or ELSE and make it GOOD.
// ====================================

function check_login() {
    global $username, $password;
    
    if (!isset($_SESSION['logged_in'])) {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            if ($_POST['username'] === $username && $_POST['password'] === $password) {
                $_SESSION['logged_in'] = true;
                return true;
            }
        }
        
        show_login_form();
        exit;
    }
    return true;
}

function show_login_form() {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <style>
            body {
                background-color: #f5f5f5;
                color: #333;
                font-family: "MS PGothic", "Osaka", Arial, sans-serif;
                font-size: 14px;
                line-height: 1.6;
                margin: 0;
                padding: 0;
                background-image:url("images/wallpaperghibli.jpg");
            }

            p {
                margin: 3px;
                padding: 0px;
            }
            .login-container {
                max-width: 300px;
                margin: 50px auto;
                padding: 20px;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .login-container input {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 3px;
                box-sizing: border-box;
            }
            .login-container button {
                width: 100%;
                padding: 10px;
                background-color:rgb(144, 236, 255);
                color: white;
                border: none;
                border-radius: 3px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Login</h2>
            <form method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>';
}
?>
