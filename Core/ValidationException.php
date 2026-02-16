<?php 
namespace Core; 
/**
 * Exception thrown when form validation fails.
 *
 * This exception contains the validation errors and the old input data.
 *
 * @property-read array $errors The validation errors.
 * @property-read array $old The old input data.
 *
 * @method static void throw(array $errors, array $old) Throws a ValidationException with the given errors and old input.
 */
class ValidationException extends \Exception{
    public readonly array $errors;
    public readonly array $old;

    public static function throw($errors, $old){
        $instance = new static('The form failed to validate.');
        $instance->errors = $errors;
        $instance->old = $old;
        throw $instance;
    }
}