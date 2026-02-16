<?php
use Core\App;
use Core\Database;
use Core\Authenticator;
use Http\Forms\LoginForm;

// Step 1: Validate role from URL
$role = filter_input(INPUT_GET, 'role', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 5]
]);

if ($role === false || $role === null) {
    $_SESSION['error'] = 'Invalid or missing role selected.';
    redirect('/AIS/');
}

// Step 2: Define demo users
$demoUsers = [
    1 => ['email' => 'owner@demo.com', 'password' => 'Relicdemo', 'user_type' => 1],
    2 => ['email' => 'salesmanager@demo.com', 'password' => 'Relicdemo', 'user_type' => 2],
    3 => ['email' => 'salesperson@demo.com', 'password' => 'Relicdemo', 'user_type' => 3],
    4 => ['email' => 'accountant@demo.com', 'password' => 'Relicdemo', 'user_type' => 4],
    5 => ['email' => 'manager@demo.com', 'password' => 'Relicdemo', 'user_type' => 5],
];

$credentials = $demoUsers[$role] ?? null;
if (!$credentials) {
    $_SESSION['error'] = 'Invalid role specified.';
    redirect('/AIS/');
}

// Step 3: Validate and attempt login
$form = LoginForm::validate([
    'email' => $credentials['email'],
    'password' => $credentials['password'],
]);

$signedIn = (new Authenticator)->attempt($credentials['email'], $credentials['password']);

if ($signedIn === 'blocked') {
    $form->error('email', 'Your account has been blocked. Please contact support.')->throw();
}

if (!$signedIn) {
echo "<script>alert('Demo user not found'); window.location.href='/AIS/';</script>";
    exit;
}

// Step 4: Verify UserType matches role
$logged_user_type = $_SESSION['user']['UserType'] ?? 0;
if ($logged_user_type != $role) {
    echo "<script>alert('Role mismatch for demo user'); window.location.href='/AIS/';</script>";
    exit;
}

// Step 5: Redirect based on UserType
switch ($logged_user_type) {
    case 1:
        redirect('/AIS/dashboard');
        break;
    case 2:
    case 3:
        redirect('/AIS/manage');
        break;
    case 4:
        redirect('/AIS/account-manage');
        break;
    case 5:
        redirect('/AIS/dashboard');
        break;
    default:
       echo "<script>alert('Invalid user type.'); window.location.href='/AIS/';</script>";
    exit;
}
?>