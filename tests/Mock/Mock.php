<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Security component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace ZoeTest\Component\Security\Mock;

use ZoeTest\Component\Security\SecurityTestCase;

/**
 * Common to all mock generator class
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class Mock extends SecurityTestCase
{
    
    /**
     * Mocked user
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mock;
    
    /**
     * Current methods mocked
     *
     * @var array
     */
    protected $methodsMocked = [];
    
    /**
     * Add a method to the mocked methods array
     *
     * @param string $method
     *   Method name to add
     */
    protected function addMethodMocked(string $method): void
    {
        $this->methodsMocked[$method] = true;
    }
    
    /**
     * Check if a method has been already mocked
     *
     * @param string $method
     *   Method name
     *
     * @return bool
     *   True if the method has been already mocked. False otherwise
     */
    protected function hasBeenMocked(string $method): bool
    {
        return isset($this->methodsMocked[$method]);
    }
    
    
    /**
     * Extract and assign by reference indexes and values from variadic array source
     *
     * @param array $variadics
     *   Variadic array source
     * @param array $indexes
     *   Array index to fill
     * @param array $values
     *   Array value to fill
     */
    protected function extractArrayVariadic(array $variadics, array& $indexes, array& $values): void
    {
        foreach ($variadics as $mainIndex => $variadicsValues) {
            if(\is_array($variadicsValues)) {
                foreach ($variadicsValues as $index => $value) {
                    $indexes[][] = $index;
                    $values[] = ($value instanceof \Exception) ? $this->throwException($value) : $value;
                }
            } else {
                $indexes[][] = $mainIndex;
                $values[] = ($variadicsValues instanceof \Exception) ? $this->throwException($variadicsValues) : $variadicsValues;
            }
        }
    }
    
    /**
     * Execute a mock, check user type if needed, check if it has been already mocked and add it to the mocked methods
     *
     * @param string $method
     *   Method name
     * @param callable $mock
     *   Callback to execute (basically the core mocking process)
     * @param mixed $extra
     *   Extra params passed to the function. Not handled natively
     *
     * @return self
     *   Fluent
     */
    protected function executeMock(string $method, callable $mock, ...$extra)
    {
        $this->throwExceptionIfMocked($method);
            
        \call_user_func($mock, $method);
            
        $this->addMethodMocked($method);
            
        return $this;
    }
    
    /**
     * Throw exception if a method has been already mocked for this user
     *
     * @param string $method
     *   Method name
     * 
     * @throws \LogicException
     *   When method already mocked
     */
    protected function throwExceptionIfMocked(string $method): void
    {
        if($this->hasBeenMocked($method))
            throw new \LogicException($this->getMessageForExceptionIfMocked($method));
    }
    
    /**
     * Get exception message when method has been already mocked
     *
     * @return string
     *   Exception message
     */
    abstract protected function getMessageForExceptionIfMocked(string $method): string;
    
}
