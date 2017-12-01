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

use Zoe\Component\Security\Acl\Mask\MaskCollection;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\InvalidMaskException;

/**
 * Generate mocked mask collection
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionMock extends Mock
{
    
    /**
     * Initialize mocked mask collection

     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(MaskCollection::class);
        
        $methods = $this->reflection_extractMethods($reflection);
        $this->mock = $this->getMockBuilder(MaskCollection::class)->disableOriginalConstructor()->setMethods($methods)->getMock();
    }
    
    /**
     * Initialize mocked mask collection
     * 
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public static function initMock(?\ReflectionClass& $reflection = null): MaskCollectionMock
    {
        return new MaskCollectionMock($reflection);
    }
    
    /**
     * Finalize this mocked mask collection
     *
     * @return MaskCollection
     *   Mocked mask collection
     */
    public function finalizeMock(): MaskCollection
    {
        return $this->mock;
    }
    
    /**
     * Mock total()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   Identifier mask name total
     * @param Mask $total
     *   Mocked mask returned as total
     *
     * @return self
     *   Fluent
     */
    public function mockTotal(PhpUnitCallMethod $count, string $identifier, Mask $total): self
    {
        $mock = function(string $method) use ($identifier, $total, $count) {
            $this->mock->expects($count)->method($method)->with($identifier)->will($this->returnValue($total));
        };
        
        return $this->executeMock("total", $mock, null);
    }
    
    /**
     * Mock add()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask $mask
     *   Mocked mask to add
     *
     * @return self
     *   Fluent
     */
    public function mockAdd(PhpUnitCallMethod $count, Mask $mask): self
    {
        $mock = function(string $method) use ($mask, $count) {
            $this->mock->expects($count)->method($method)->with($mask)->will($this->returnValue(null)); 
        };
        
        return $this->executeMock("add", $mock, null);
    }
    
    /**
     * Mock add() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask ...$masks
     *   Variadic number of masks to add on each call
     *
     * @return self
     *   Fluent
     */
    public function mockAdd_consecutive(PhpUnitCallMethod $count, Mask ...$masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $args = [];
            $returned = [];
            foreach ($masks as $mask) {
                $args[][] = $mask;
                $returned[] = $this->returnValue(null);
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("add", $mock, null);
    }
    
    /**
     * Mock get()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $maskName
     *   Mask name given as argument
     * @param Mask $maskReturned
     *   Mocked mask return or null to throw exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet(PhpUnitCallMethod $count, string $maskName, ?Mask $maskReturned): self
    {
        $mock = function(string $method) use ($maskName, $maskReturned, $count) {
            $return = (null === $maskReturned) ? $this->throwException(new InvalidMaskException()) : $this->returnValue($maskReturned);
            $this->mock->expects($count)->method($method)->with($maskName)->will($return);
        };
        
        return $this->executeMock("get", $mock, null);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $masks
     *   Index mask name and value the mocked mask or null to throw exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutive(PhpUnitCallMethod $count, array $masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $args = [];
            $returned = [];
            foreach ($masks as $name => $mask) {
                $args[][] = $name;
                $returned[] = (null === $mask) ? $this->throwException(new InvalidMaskException()) : $this->returnValue($mask);
            }
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("get", $mock, null);
    }
    
    /**
     * Mock has()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $maskName
     *   Mask name argument
     * @param bool $result
     *   Bool result
     *
     * @return self
     *   Fluent
     */
    public function mockHas(PhpUnitCallMethod $count, string $maskName, bool $result): self
    {
        $mock = function(string $method) use ($maskName, $result, $count) {
            $this->mock->expects($count)->method($method)->with($maskName)->will($this->returnValue($result)); 
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * Mock has() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $masks
     *   Index is mask name and value the result
     *
     * @return self
     *   Fluent
     */
    public function mockHas_consecutive(PhpUnitCallMethod $count, array $masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($masks, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * Mock refresh()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask $mask
     *   Mock mask to refresh
     * @param bool $exception
     *   Set to true to throw simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockRefresh(PhpUnitCallMethod $count, Mask $mask, bool $exception): self
    {
        $mock = function(string $method) use ($mask, $exception, $count) {
            $return = ($exception) ? $this->throwException(new InvalidMaskException()) : $this->returnValue(null);
            $this->mock->expects($count)->method($method)->with($mask)->will($return);
        };
        
        return $this->executeMock("refresh", $mock, null);
    }
    
    /**
     * Mock refresh() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array ...$masks
     *   Variadic number of arrays as first value of each array determine the mocked mask to refresh and the second if an exception is thrown
     *
     * @return self
     *   Fluent
     */
    public function mockRefresh_consecutive(PhpUnitCallMethod $count, array ...$masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $args = [];
            $returned = [];
            foreach ($masks as $variadicMasks) {
                foreach ($variadicMasks as $value) {
                    if(!\is_bool($value)) {
                        $args[][] = $value;
                    } else {
                        $returned[] = (true === $value) ? $this->throwException(new InvalidMaskException()) : $this->returnValue(null);
                    }
                }
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("refresh", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' has been already mocked for this mocked mask collection",
            $method);
    }
    
}
