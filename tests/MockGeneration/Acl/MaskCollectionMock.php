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
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Exception\Acl\InvalidMaskException;

/**
 * Responsible to mock mask collection
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionMock extends MockGeneration
{
    
    /**
     * Initialize a new mask mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return MaskCollectionMock
     *   New mask collection mock generation
     */
    public static function init(string $mockId): MaskCollectionMock
    {
        return new MaskCollectionMock($mockId, MaskCollection::class);
    }
    
    /**
     * Finalize the mocked mask collection
     *
     * @return MaskCollection
     *   Mocked mask collection
     */
    public function finalizeMock(): MaskCollection
    {
        return $this->mock;
    }
    
    /**
     * Mock getIterator()
     *
     * @param MethodCount $count
     *   Called count
     * @param \Generator $masks
     *   All masked returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetIterator(MethodCount $count, \Generator $masks): self
    {
        $mock = function(string $method) use ($masks, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($masks));
        }; 
        
        return $this->executeMock("getIterator", $mock);
    }
    
    /**
     * Mock total()
     *
     * @param MethodCount $count
     *   Called count
     * @param string|null $identifier
     *   Total mask identifier
     * @param array|null $masks
     *   Masks to add to total
     * @param Mask|null $total
     *   Total returned or null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockTotal(MethodCount $count, ?string $identifier, ?array $masks, ?Mask $total): self
    {
        $mock = function(string $method) use ($identifier, $masks, $total) {
            $return = $this->stubThrowableOnNull(new InvalidMaskException(), $total);
            $this->mock->method($method)->with($identifier, $masks)->will($return);
        };
        
        return $this->executeMock("total", $mock);
    }
    
    /**
     * Mock total() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $identifiersAndMasks
     *   Arrays of array containing all params for each call
     * @param Mask|null ...$total
     *   Total masks return on each call. Set to null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockTotal_consecutive(MethodCount $count, array $identifiersAndMasks, ?Mask ...$total): self
    {
        $mock = function(string $method) use ($identifiersAndMasks, $total, $count) {
            $return = $this->stubThrowableOnNull(new InvalidMaskException(), ...$total);
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiersAndMasks)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("total", $mock);
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
     * Mock get()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $mask
     *   Mask name
     * @param Mask $maskReturned
     *   Mocked mask returned
     *
     * @return self
     *   Fluent
     */
    public function mockGet(MethodCount $count, string $mask, ?Mask $maskReturned): self
    {
        $mock = function(string $method) use ($mask, $maskReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidMaskException(), $maskReturned);
            $this->mock->expects($count)->method($method)->with($mask)->will($return);
        };
        
        return $this->executeMock("get", $mock);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $masks
     *   Arrays of array containing all params for each call
     * @param Mask|null ...$masksReturned
     *   Mask returned on each call. Set to null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutive(MethodCount $count, array $masks, ?Mask ...$masksReturned): self
    {
        $mock = function(string $method) use ($masks, $masksReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidMaskException(), ...$masksReturned);
            $this->mock->expects($count)->method($method)->withConsecutive(...$masks)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("get", $mock);
    }
    
    /**
     * Mock has()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $mask
     *   Mask name
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockHas(MethodCount $count, string $mask, bool $result): self
    {
        $mock = function(string $method) use ($mask, $result, $count) {
            $this->mock->expects($count)->method($method)->with($mask)->will($this->returnValue($result));
        };
        
        return $this->executeMock("has", $mock);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $masks
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Result for each call
     *
     * @return self
     *   Fluent
     */
    public function mockHas_consecutive(MethodCount $count, array $masks, bool ...$results): self
    {
        $mock = function(string $method) use ($masks, $results, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$masks)->willReturnOnConsecutiveCalls(...$results);
        };
        
        return $this->executeMock("has", $mock);
    }
    
}
