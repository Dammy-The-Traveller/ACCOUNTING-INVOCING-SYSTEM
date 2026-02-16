<?php 
namespace Core;

/**
 * Class Validator
 *
 * Provides static methods for validating different types of input data.
 *
 * Methods:
 * - string($value, $min = 1, $max = INF): Validates that a string's length is within the specified range.
 * - email($value): Validates that the value is a properly formatted email address.
 * - greaterThan(int $value, int $greaterThan): Checks if a value is greater than a specified number.
 */
class Validator{

    public static function string($value, $min=1, $max = INF){
        $value= trim($value);
        return strlen($value)>= $min && strlen($value)<= $max;
    }
    public static function email($value){
return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    // public static function studentID($value){
    //     return preg_match('/^[a-zA-Z0-9]{1,20}$/', $value);
    // }
    
    public static function greaterThan( int $value, int $greaterThan):bool{
        return $value > $greaterThan;
    }

    /**
     * Validates that a value is a number (integer or float) with a minimum value.
     *
     * @param mixed $value The value to validate
     * @param float $min Minimum value (inclusive)
     * @param float|null $max Maximum value (inclusive, optional)
     * @return bool True if valid, false otherwise
     */
    public static function number($value, float $min = PHP_INT_MIN, ?float $max = null): bool
    {
        // Check if the value is numeric (integer or float)
        if (!is_numeric($value)) {
            return false;
        }

        // Convert to float for comparison
        $number = (float) $value;

        // Check minimum value
        if ($number < $min) {
            return false;
        }

        // Check maximum value if provided
        if ($max !== null && $number > $max) {
            return false;
        }

        return true;
    }

    /**
     * Validates that a value is a valid date string in a specified format.
     *
     * @param mixed $value The value to validate
     * @param string $format Date format (default: 'Y-m-d')
     * @return bool True if valid, false otherwise
     */
    public static function date($value, string $format = 'Y-m-d'): bool
    {
        if (!is_string($value) || empty(trim($value))) {
            return false;
        }

        // Create DateTime object from the value and format
        $date = \DateTime::createFromFormat($format, $value);

        // Check if the date was created successfully and matches the input
        return $date && $date->format($format) === $value;
    }
}
