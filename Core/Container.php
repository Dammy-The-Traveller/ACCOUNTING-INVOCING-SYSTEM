<?php 
namespace Core;
use Exception;
/**
 * Class Container
 *
 * A simple dependency injection container for managing class bindings and resolutions.
 *
 * @property array $bindings An associative array storing key-resolver pairs.
 *
 * Methods:
 * @method void bind(string $key, callable $resolver) Binds a key to a resolver function.
 * @method mixed resolve(string $key) Resolves and returns the instance associated with the given key.
 *
 * Usage:
 * - Use bind() to register a resolver for a specific key.
 * - Use resolve() to retrieve the instance created by the resolver.
 *
 * @throws \Exception If no binding is found for the given key during resolution.
 */
class Container{
    protected $bindings = [];
    public function bind($key, $resolver){
      $this->bindings[$key] = $resolver;
    }

    public function resolve($key){

        if(!array_key_exists($key, $this->bindings)){
            throw new \Exception("No matching binding find for your string");
        }

        $resolver = $this->bindings[$key];
        return call_user_func($resolver);
    }
    
}
?>