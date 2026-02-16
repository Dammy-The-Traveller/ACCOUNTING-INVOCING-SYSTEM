<?php 

namespace Core;
use Core\Database;
/**
 * Class Authenticator
 *
 * Handles user authentication, login, and logout processes.
 */
 
/**
 * Attempts to authenticate a user with the provided email and password.
 *
 * @param string $email    The user's email address.
 * @param string $password The user's password.
 * @return bool|string     Returns true on successful authentication, 'blocked' if the user is blocked, or false on failure.
 */
 
/**
 * Logs in the user by storing their information in the session.
 *
 * @param array $user  An associative array containing user details:
 *                     - id (int): User ID
 *                     - firstname (string): User's first name
 *                     - lastname (string): User's last name
 *                     - email (string): User's email address
 *                     - user_type (int): User type identifier
 * @return void
 */
 
/**
 * Logs out the current user by destroying the session.
 *
 * @return void
 */
class Authenticator{
  public function attempt($email, $password)
    {
        $user = App::resolve(Database::class)
            ->query('SELECT * FROM users WHERE email = :email', [
            'email' => $email
        ])->find();

        if ($user) {
          if ($user['block'] === 'Y') {
            return 'blocked';     
        }
  
            if (password_verify($password, $user['password'])) {
                $this->login([
                    'id' => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type'],
                ]);
                return true;
            }
        }
        return false;
    }


 public function login($user){
     // register user in session
     
     $oldSessionData = [
      'user' => [
        'ID' => (int) $user['id'],
        'firstname' => $user['firstname'],
        'lastname' => $user['lastname'],
           'email' => $user['email'],
           'UserType' => (int) $user['user_type'],
      ]
  ];
 
    // $oldSessionData = $_SESSION['user'];
     session_regenerate_id(true);
     $_SESSION = $oldSessionData;
    
    
}

public function logout(){
    Session::destroy();
  
}

}