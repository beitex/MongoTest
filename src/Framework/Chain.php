<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/7/14
 * Time: 10:17 AM
 */

namespace Framework;


class Chain {
    private static $instance 	= null;
    public  static $currentClass 	= '';
    public  static $failOnError	= false;
    private $storage  		= array();


    private function __construct(){ }  // Protect from creating object by new Singleton
    private function __clone()    { }  // Protect from cloning
    private function __wakeup()   { }  // Protect from unserializing

    /**
     * Get value from class
     * @param string  $param
     * @throws Exception
     * @return mixed
     */
    public function __get($param)
    {
        $chain = self::getInstance();

        try
        {
            $currentObj = &$chain->storage[self::$currentClass]['class'];

            if(!is_object($currentObj))
                throw new Exception("Class ". self::$currentClass ." wasn't initialized");

            switch($param)
            {
                case 'instance':
                    return $currentObj;
                case 'result':
                    return $chain->storage[self::$currentClass]['result'];
                case 'error':
                    return $chain->storage[self::$currentClass]['error'];
                default:
                    return $currentObj->$param;
            }

            return $currentObj->$param;
        }
        catch(Exception $e)
        {
            self::setError($e->getMessage());
        }
    }

    /**
     * Set class variable
     * @param string $param
     * @param mixed $value
     * @throws Exception
     */
    public function __set($param,$value)
    {
        $chain = self::getInstance();

        try
        {
            $currentObj = &$chain->storage[self::$currentClass]['class'];

            if(!is_object($currentObj))
                throw new Exception("Class ". self::$currentClass ." wasn't initialized");

            switch($param)
            {
                case 'instance':
                    if(is_object($value))
                        $chain->storage[self::$currentClass]['class'] = $value;
                    else
                        throw new Exception("Couldn't set instance of the class " . self::$currentClass);
                    break;
                default:
                    $currentObj->$param = $value;
            }

        }
        catch(Exception $e)
        {
            self::setError($e->getMessage());
        }
    }

    /**
     * Call class method
     * @param string $method
     * @param array $params
     * @throws Exception
     * @return bool|object
     */
    public function __call($method,$params)
    {
        $chain = self::getInstance();

        try
        {
            $currentObj = &$chain->storage[self::$currentClass]['class'];

            if(!is_object($currentObj))
                throw new Exception("Class ". self::$currentClass ." wasn't initialized");

            if(!method_exists($currentObj,$method))
                throw new Exception("Class " . self::$currentClass . " has no method " . $method);

            if(in_array('result',$params))
                $params[array_search('result',$params)] = $chain->storage[self::$currentClass]['result'];

            $chain->storage[self::$currentClass]['result'] = call_user_func_array(array($currentObj, $method), $params);

            if($chain->storage[self::$currentClass]['flush'])
            {
                $chain->storage[self::$currentClass]['flush'] = false;
                $chain->storage[self::$currentClass]['error'] = array();
            }

            return $chain;
        }
        catch(Exception $e)
        {
            return self::setError($e->getMessage());
        }

    }

    /**
     * Get instance
     * @return object
     */
    private static function getInstance()
    {
        if(!self::$instance instanceOf Chain)
            self::$instance = new Chain();

        return self::$instance;
    }

    /**
     * Initialize called class and return self instance
     * @param string $class
     * @param array $args
     * @throws Exception
     * @return bool|object
     */
    public static function __callStatic($class,$args)
    {
        $chain = self::getInstance();
        self::$currentClass = $class;

        if(!(array_key_exists($class,$chain->storage) && $chain->storage[$class]['class'] instanceOf $class))
        {
            try
            {
                if(!class_exists($class))
                    throw new Exception("Class ". $class ." doen't exist");

                $chain->storage[$class] = array();
                $obj = new ReflectionClass($class);
                $chain->storage[$class]['class'] = $obj->newInstanceArgs($args);
                $chain->storage[$class]['result'] = null;
                $chain->storage[$class]['error'] = array();
                $chain->storage[$class]['flush'] = false;

                return $chain;
            }
            catch(Exception $e)
            {
                return self::setError($e->getMessage());
            }
        }
        else
        {
            $chain->storage[$class]['flush'] = true;
            return $chain;
        }
    }

    /**
     * Set error
     * @param string $string
     * @return bool|object
     */
    private static function setError($string)
    {
        $chain = self::getInstance();
        $chain->storage[self::$currentClass]['error'][]= $string;
        return self::$failOnError ? false : $chain;
    }

} 