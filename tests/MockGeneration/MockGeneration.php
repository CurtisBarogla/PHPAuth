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

namespace ZoeTest\Component\Security\MockGeneration;

use Zoe\Component\Internal\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * Common to all mock generation class
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class MockGeneration extends TestCase
{

    use ReflectionTrait;
    
    /**
     * Mock identifier
     * 
     * @var string
     */
    protected $mockId;
    
    /**
     * All methods already mocked
     * 
     * @var array
     */
    protected $mocked;
    
    /**
     * Current value for mock currently generated
     * 
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mock;
    
    /**
     * Construct a mocked object with all methods setted from a given object name
     * 
     * @param string $mockId
     *   Mock id
     * @param string $objectName
     *   Complet object name to mock
     * @param mixed ...$argsConstructor
     *   Variadic number of arguments to pass to the constructor of the mocked object. Let empty to disable constructor
     */
    public function __construct(string $mockId, string $objectName, ...$argsConstructor)
    {
        $reflection = new \ReflectionClass($objectName);
        
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder($objectName)->setMethods($methods);

        if(empty($argsConstructor))
            $mock->disableOriginalConstructor();
        else
            $mock->setConstructorArgs($argsConstructor);
        
        $this->mock = $mock->getMock();
        $this->mockId = $mockId;
    }
    
    /**
     * Check if a method has been already mocked
     * 
     * @param string $method
     *   Method to check
     * 
     * @return bool
     *   True if the method has been already mocked. False otherwise
     */
    protected function isMocked(string $method): bool
    {
        return isset($this->mocked[$method]);
    }
    
    /**
     * Add a method to the mocked methods array
     * 
     * @param string $method
     *   Method name
     */
    protected function addMocked(string $method): void
    {
        $this->mocked[$method] = $method;
    }
    
    /**
     * Stub a value to simulate an exception on null or directly return it
     * 
     * @param \Throwable $exceptionOnNull
     *   Exception thrown when the value is null
     * @param mixed ...$value
     *   Stubs to return
     * 
     * @return \PHPUnit_Framework_MockObject_Stub|array[\PHPUnit_Framework_MockObject_Stub]
     *   Stub value
     */
    protected function stubThrowableOnValue(\Throwable $exceptionOnNull, ...$values)
    {
        if(\count($values) === 1) {
            return ($values[0] instanceof \Throwable) ? $this->throwException($exceptionOnNull) : $this->returnValue($values[0]);
        } else {
            foreach ($values as $index => $value)
                $values[$index] = ($value instanceof \Throwable) ? $this->throwException($exceptionOnNull) : $this->returnValue($value);
            
            return $values;
        }
    }
    
    /**
     * Execute a mocked method
     * 
     * @param string $method
     *   Method name to execute
     * @param callable $mock
     *   Mock to execute
     * 
     * @return self
     *   Self
     *   
     * @throws \LogicException
     *   When the method has been already executed
     */
    protected function executeMock(string $method, callable $mock): self
    {
        if($this->isMocked($method))
            throw new \LogicException(\sprintf("This method '%s' has been already mocked for this mocked ID object",
                $method,
                $this->mockId));
        
        \call_user_func($mock, $method);
        
        $this->addMocked($method);
        
        return $this;
    }
    
}
