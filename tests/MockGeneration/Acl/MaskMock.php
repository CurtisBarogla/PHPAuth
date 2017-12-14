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

namespace ZoeTest\Component\Security\MockGeneration\Acl;

use ZoeTest\Component\Security\MockGeneration\MockGeneration;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Acl\Mask\Mask;

/**
 * Responsible to mock mask
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskMock extends MockGeneration
{
    
    /**
     * Initialize a new mask mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return MaskMock
     *   New mask mock generation
     */
    public static function init(string $mockId): MaskMock
    {
        return new MaskMock($mockId, Mask::class);
    }
    
    /**
     * Finalize the mocked mask
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
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Identifier returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetIdentifier(MethodCount $count, string $identifier): self
    {
        $mock = function(string $method) use ($identifier, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($identifier));
        };
        
        return $this->executeMock("getIdentifier", $mock);
    }
    
    /**
     * Mock getValue()
     *
     * @param MethodCount $count
     *   Called count
     * @param int $value
     *   Mask value
     *
     * @return self
     *   Fluent
     */
    public function mockGetValue(MethodCount $count, int $value): self
    {
        $mock = function(string $method) use ($value, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($value));
        };
        
        return $this->executeMock("getValue", $mock);
    }
    
    /**
     * Mock getValue() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param int ...$values
     *   Varidiac values. Returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetValue_consecutive(MethodCount $count, int ...$values): self
    {
        $mock = function(string $method) use ($values, $count) {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$values);
        };
        
        return $this->executeMock("getValue", $mock);
    }
    
    /**
     * Mock add()
     *
     * @param MethodCount $count
     *   Called count
     * @param Mask $mask
     *   Mocked mask to add
     *
     * @return self
     *   Fluent
     */
    public function mockAdd(MethodCount $count, Mask $mask): self
    {
        $mock = function(string $method) use ($mask, $count) {
            $this->mock->expects($count)->method($method)->with($mask)->will($this->returnValue(null));
        };
        
        return $this->executeMock("add", $mock);
    }
    
    /**
     * Mock add() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $masks
     *   Arrays of array containing all params for each call 
     * 
     * @return self
     *   Fluent
     */
    public function mockAdd_consecutive(MethodCount $count, array $masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$masks)->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock("add", $mock);
    }
    
    /**
     * Mock sub()
     *
     * @param MethodCount $count
     *   Called count
     * @param Mask $mask
     *   Mocked mask to sub
     *
     * @return self
     *   Fluent
     */
    public function mockSub(MethodCount $count, Mask $mask): self
    {
        $mock = function(string $method) use ($mask, $count) {
            $this->mock->expects($count)->method($method)->with($mask)->will($this->returnValue(null));
        };
        
        return $this->executeMock("sub", $mock);
    }
    
    /**
     * Mock add() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $masks
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockSub_consecutive(MethodCount $count, array $masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$masks)->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock("sub", $mock);
    }
    
    /**
     * Mock rshift()
     *
     * @param MethodCount $count
     *   Called count
     * @param int $value
     *   Bits to shift
     *
     * @return self
     *   Fluent
     */
    public function mockRshift(MethodCount $count, int $value): self
    {
        $mock = function(string $method) use ($value, $count) {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnValue(null));
        };
        
        return $this->executeMock("rshift", $mock);
    }
    
    /**
     * Mock rshift() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $values
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockRshift_consecutive(MethodCount $count, array $values): self
    {
        $mock = function(string $method) use ($values, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$values)->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock("rshift", $mock);
    }
    
    /**
     * Mock lshift()
     *
     * @param MethodCount $count
     *   Called count
     * @param int $value
     *   Bits to shift
     *
     * @return self
     *   Fluent
     */
    public function mockLshift(MethodCount $count, int $value): self
    {
        $mock = function(string $method) use ($value, $count) {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnValue(null));
        };
        
        return $this->executeMock("lshift", $mock);
    }
    
    /**
     * Mock lshift() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $values
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockLshift_consecutive(MethodCount $count, array $values): self
    {
        $mock = function(string $method) use ($values, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$values)->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock("lshift", $mock);
    }
    
}
