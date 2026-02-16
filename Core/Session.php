<?php 
namespace Core;
/**
 * Class Session
 *
 * Provides static methods for managing session data, including setting, retrieving,
 * flashing, and destroying session variables.
 *
 * Methods:
 * @method static bool has(string $key) 
 *      Checks if a session key exists and has a non-empty value.
 *
 * @method static void put(string $key, mixed $value)
 *      Stores a value in the session under the specified key.
 *
 * @method static mixed get(string $key, mixed $default = null)
 *      Retrieves a value from the session by key, or returns the default if not set.
 *      Gives priority to flash data if available.
 *
 * @method static void flash(string $key, mixed $value)
 *      Stores a flash value in the session, intended for one-time retrieval.
 *
 * @method static void unflash()
 *      Removes all flash data from the session.
 *
 * @method static void flush()
 *      Clears all session data.
 *
 * @method static void destroy()
 *      Destroys the session, clears all session data, and removes the session cookie.
 */
class Session{
    public static function has($key){
        return (bool) static::get($key);
    }

    public static function put($key, $value){
        $_SESSION[$key] = $value;
    }
    public static function get($key, $default = null){
        if(isset($_SESSION['_flash'][$key])){
            return $_SESSION['_flash'][$key];
        }
      return $_SESSION[$key] ?? $default;
    }
    public static function flash($key, $value){

        $_SESSION['_flash'][$key] = $value;
    }
    public static function unflash(){

        unset($_SESSION['_flash']);
    }
    public static function flush(){ 
        $_SESSION=[];
      
    }
    public static function destroy(){
        static::flush();
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID','', time() -3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']) ;
    }
    
}