<?php
use Core\App;
use Core\Authenticator;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);
$firstName = $_POST['FirstName'];
$lastName = $_POST['LastName'];
$email = $_POST['email'];
$password = $_POST['Password'];
$RepeatPassword = $_POST['RepeatPassword'];
$token = $_POST['token'];
$userType = 2;
$block = 'N';
$errors = [];


if (!Validator::string($firstName, 2, 255)) {
    $errors['firstName'] = 'Please provide a valid first name.';
    
}

if (!Validator::string($lastName, 2, 255)) {
    $errors['lastName'] = 'Please provide a valid last name.';
    
}



if (!Validator::email($email)) {
   $errors['email'] = 'Please provide a valid email address.';
   
}


if (!Validator::string($password, 7, 255)) {
    $errors['password'] = 'Please provide a password of at least seven characters.';
    
}
if ($password !== $RepeatPassword) {
    $errors['password'] = 'Passwords do not match.';
    
}
if (!Validator::string($token, 2, 255)) {
    $errors['token'] = 'Please provide a valid token.';
   
}


$tokenData = $db->query(
    'SELECT token, status, expires_at FROM admin_verification 
    WHERE email = :email 
    ORDER BY created_at DESC 
    LIMIT 1', 
   ['email' => $email]
)->find();

if (!$tokenData) {
    $errors['token'] = 'Token not found.';
} elseif ($tokenData['token'] !== $token) {
    $errors['token'] = 'Invalid token.';
} elseif ($tokenData['status'] === 'Used') {
    $errors['token'] = 'Token has already been used.';
} elseif (strtotime($tokenData['expires_at']) < time()) {
    $errors['token'] = 'Token has expired.';
}

if (! empty($errors)) {
    return views('registration/create.view.php', [
        'errors' => $errors
    ]);
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
}


$user = $db->query('SELECT * FROM clinic_admins where email = :email', [
    'email' => $email
])->find();

if ($user) {
    header('location: /AIS/');
    exit();
} else {
    $users = $db->query('INSERT INTO clinic_admins(firstname, lastname, email, password, user_type, block) VALUES (:firstName, :lastName, :email, :password, :userType, :Block)', [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'userType' => $userType,
        'Block' => $block
    ]);


    $db->query(
        'UPDATE admin_verification 
        SET status = "Used" 
        WHERE email = :email AND token = :token', 
        [
            'email' => $email,
            'token' => $token
        ]
    );


    header('location: /AIS/');
    exit();
}
