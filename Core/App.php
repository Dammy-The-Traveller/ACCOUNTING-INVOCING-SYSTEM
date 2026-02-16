<?php 
namespace Core;

/**
 * Class App
 *
 * Acts as a static facade for a dependency injection container.
 *
 * @method static void setContainer($container) Sets the container instance.
 * @method static mixed Container() Retrieves the current container instance.
 * @method static void bind($key, $resolver) Binds a key to a resolver in the container.
 * @method static mixed resolve($key) Resolves and returns the instance associated with the given key.
 *
 * @property static $container The underlying container instance.
 */
class App{
    protected static $container;
    public static function setContainer($container){
        static::$container = $container;
    }
    public static function Container(){
        return static::$container;
    }
    public static function bind($key, $resolver){
        static::Container()->bind($key, $resolver);
    }
    public static function resolve($key){
       return static::Container()->resolve($key);
    }
}