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

namespace ZoeTest\Component\Security\MockGeneration\Tests\Acl;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use Zoe\Component\Internal\GeneratorTrait;
use Zoe\Component\Security\Exception\Acl\InvalidEntityValueException;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;

/**
 * EntityMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityMockTest extends TestCase
{
    
    use GeneratorTrait;
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGetIterator()
     */
    public function testMockGetIterator(): void
    {
        $values = $this->getGenerator(["Foo" => ["Foo", "Bar"], "Bar" => ["Moz", "Poz"]]);
        
        $entity = EntityMock::init("Foo")->mockGetIterator($this->once(), $values)->finalizeMock();
        
        foreach ($entity as $value => $permissions) {
            switch ($value) {
                case "Foo":
                    $this->assertSame(["Foo", "Bar"], $permissions);
                    break;
                case "Bar":
                    $this->assertSame(["Moz", "Poz"], $permissions);
                    break;
            }
        }
    }
        
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGetIdentifier()
     */
    public function testMockGetIdentifier(): void
    {
        $entity = EntityMock::init("Foo")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $entity->getIdentifier());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGet()
     */
    public function testMockGet(): void
    {
        $entity = EntityMock::init("Foo")->mockGet($this->once(), "Foo", ["Foo", "Bar"])->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $entity->get("Foo"));
        
        $this->expectException(InvalidEntityValueException::class);
        $entity = EntityMock::init("Foo")->mockGet($this->once(), "Foo", null)->finalizeMock();
        
        $entity->get("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGet()
     */
    public function testExceptionParameterInvalidValueWhenMockGet(): void
    {
        $entity = EntityMock::init("Foo")->mockGet($this->once(), "Foo", null)->finalizeMock();
        
        try {
            $entity->get("Foo");
        } catch (InvalidEntityValueException $e) {
            $this->assertSame("Foo", $e->getInvalidValue());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGet_consecutive()
     */
    public function testMockGet_consecutive(): void
    {
        $entity = EntityMock::init("Foo")
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Foo"], ["Bar"]],
                                    null,
                                    ["Foo", "Bar"], ["Moz", "Poz"])
                            ->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $entity->get("Foo"));
        $this->assertSame(["Moz", "Poz"], $entity->get("Bar"));
        
        $this->expectException(InvalidEntityValueException::class);
        $entity = EntityMock::init("Foo")
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Foo"], ["Bar"]], 
                                    null,
                                    ["Foo", "Bar"], null)
                            ->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $entity->get("Foo"));
        $entity->get("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGet_consecutive()
     */
    public function testExceptionParameterInvalidValueWhenMockGet_consecutive(): void
    {
        $entity = EntityMock::init("Foo")
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Foo"], ["Bar"]], 
                                    "Bar", 
                                    ["Foo", "Bar"], null)
                            ->finalizeMock();
        
        try {
            $entity->get("Foo");
            $entity->get("Bar");
        } catch (InvalidEntityValueException $e) {
            $this->assertSame("Bar", $e->getInvalidValue());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockHas()
     */
    public function testMockHas(): void
    {
        $entity = EntityMock::init("Foo")->mockHas($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($entity->has("Foo"));
        
        $entity = EntityMock::init("Foo")->mockHas($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($entity->has("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockHas_consecutive()
     */
    public function testMockHas_consecutive(): void
    {
        $entity = EntityMock::init("Foo")->mockHas_consecutive($this->exactly(2), [["Foo"], ["Bar"]], true, false)->finalizeMock();
        
        $this->assertTrue($entity->has("Foo"));
        $this->assertFalse($entity->has("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockIsEmpty()
     */
    public function testMockIsEmpty(): void
    {
       $entity = EntityMock::init("Foo")->mockIsEmpty($this->once(), true)->finalizeMock();
       
       $this->assertTrue($entity->isEmpty());
       
       $entity = EntityMock::init("Foo")->mockIsEmpty($this->once(), false)->finalizeMock();
       
       $this->assertFalse($entity->isEmpty());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockIsEmpty_consecutive()
     */
    public function testMockIsEmpty_consecutive(): void
    {
       $entity = EntityMock::init("Foo")->mockIsEmpty_consecutive($this->exactly(2), true, false)->finalizeMock();
       
       $this->assertTrue($entity->isEmpty());
       $this->assertFalse($entity->isEmpty());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGetProcessor()
     */
    public function testMockGetProcessor(): void
    {
        $entity = EntityMock::init("Foo")->mockGetProcessor($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $entity->getProcessor());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityMock::mockGetResource()
     */
    public function testMockGetResource(): void
    {
        $resource = ResourceMock::init("ResourceSettedIntoEntity")->finalizeMock();
        $entity = EntityMock::init("Foo")->mockGetResource($this->once(), $resource)->finalizeMock();
        
        $this->assertSame($resource, $entity->getResource());
        
    }
    
}
