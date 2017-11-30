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

use Zoe\Component\Security\Acl\Mask\Mask;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;

/**
 * Generate mocked mask
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskMock extends Mock
{
    
    /**
     * Mask identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Initialize mocked mask
     * 
     * @param string $identifier
     *   Mask identifier
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $identifier, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(Mask::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $this->mock = $this->getMockBuilder(Mask::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
        $this->identifier = $identifier;
    }
    
    /**
     * Initialize a new mocked mask
     *
     * @param string $identifier
     *   Mocked user name returned by getIdentifier()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *   
     * @return MaskMock
     *   New mocked mask
     */
    public static function initMock(string $identifier, ?\ReflectionClass& $reflection = null): MaskMock
    {
        return new MaskMock($identifier, $reflection);
    }
    
    /**
     * Finalize this mocked mask
     *
     * @return Mask
     *   Mocked mask
     */
    public function finalizeMock(): Mask
    {
        return $this->mock;
    }
    
    /**
     * Mock getIdentifier()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     *
     * @return self
     *   Fluent
     */
    public function mockGetIdentifier(PhpUnitCallMethod $count): self
    {
        $mock = function(string $method) use ($count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($this->identifier));   
        };

        return $this->executeMock("getIdentifier", $mock, null);
    }
    
    /**
     * Mock getIdentifier() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string ...$identifiers
     *   Varidic identifiers returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetIdentifier_consecutive(PhpUnitCallMethod $count, string ...$identifiers): self
    {
        $mock = function(string $method) use ($identifiers, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$identifiers);   
        };
        
        return $this->executeMock("getIdentifier", $mock, null);
    }
    
    /**
     * Mock getValue()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int $value
     *   Mask value
     *
     * @return self
     *   Fluent
     */
    public function mockGetValue(PhpUnitCallMethod $count, int $value): self
    {
        $mock = function(string $method) use ($value, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($value));      
        };
        
        return $this->executeMock("getValue", $mock, null);
    }
    
    /**
     * Mock getValue() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int ...$values
     *   Varidic values returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetValue_consecutive(PhpUnitCallMethod $count, int ...$values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$values);
        };
        
        return $this->executeMock("getValue", $mock, null);
    }
    
    /**
     * Mock add()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask $mask
     *   Mocked mask
     *
     * @return self
     *   Fluent
     */
    public function mockAdd(PhpUnitCallMethod $count, Mask $mask): self
    {
        $mock = function(string $method) use ($mask, $count): void {
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
     *   Varidic mocked masks
     *
     * @return self
     *   Fluent
     */
    public function mockAdd_consecutive(PhpUnitCallMethod $count, Mask ...$masks): self
    {
        $mock = function(string $method) use ($masks, $count): void {
            $args = [];
            $returned = [];
            foreach ($masks as $variadicMasks) {
                foreach ($variadicMasks as $mask) {
                    $args[][] = $mask;
                    $returned[] = $this->returnValue(null);
                }
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("add", $mock, null);
    }
    
    /**
     * Mock sub()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask $mask
     *   Mocked mask
     *
     * @return self
     *   Fluent
     */
    public function mockSub(PhpUnitCallMethod $count, Mask $mask): self
    {
        $mock = function(string $method) use ($mask, $count): void {
            $this->mock->expects($count)->method($method)->with($mask)->will($this->returnValue(null));
        };
        
        return $this->executeMock("sub", $mock, null);
    }
    
    /**
     * Mock sub() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Mask ...$masks
     *   Varidic mocked masks
     *
     * @return self
     *   Fluent
     */
    public function mockSub_consecutive(PhpUnitCallMethod $count, Mask ...$masks): self
    {
        $mock = function(string $method) use ($masks, $count): void {
            $args = [];
            $returned = [];
            foreach ($masks as $variadicMasks) {
                foreach ($variadicMasks as $mask) {
                    $args[][] = $mask;
                    $returned[] = $this->returnValue(null);
                }
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("sub", $mock, null);
    }
    
    /**
     * Mock left()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int|null $value
     *   Bit to left
     *
     * @return self
     *   Fluent
     */
    public function mockLeft(PhpUnitCallMethod $count, ?int $value): self
    {
        $mock = function(string $method) use ($value, $count): void {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnSelf());   
        };
        
        return $this->executeMock("left", $mock, null);
    }
    
    /**
     * Mock left() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int|null ...$values
     *   Varidic left values
     *
     * @return self
     *   Fluent
     */
    public function mockLeft_consecutive(PhpUnitCallMethod $count, ?int ...$values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $args = [];
            $returned = [];
            foreach ($values as $value) {
                $args[][] = $value;
                $returned[] = $this->returnSelf();
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("left", $mock, null);
    }
    
    /**
     * Mock right()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int|null $value
     *   Bit to right
     *
     * @return self
     *   Fluent
     */
    public function mockRight(PhpUnitCallMethod $count, ?int $value): self
    {
        $mock = function(string $method) use ($value, $count): void {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnSelf());
        };
        
        return $this->executeMock("right", $mock, null);
    }
    
    /**
     * Mock right() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int|null ...$values
     *   Varidic right values
     *
     * @return self
     *   Fluent
     */
    public function mockRight_consecutive(PhpUnitCallMethod $count, ?int ...$values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $args = [];
            $returned = [];
            foreach ($values as $value) {
                $args[][] = $value;
                $returned[] = $this->returnSelf();
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("right", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for mocked mask '%s' has been already mocked",
            $method,
            $this->identifier); 
    }
    
}
