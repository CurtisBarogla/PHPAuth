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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\EntityMock;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Exception\InvalidEntityException;

/**
 * EntityMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\EntityMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockGetIterator()
     */
    public function testMockGetIterator(): void
    {
        $values = $this->getGenerator(["Foo" => ["Foo", "Bar"], "Bar" => ["Moz", "Poz"]]);
        $entity = EntityMock::initMock("Foo")->mockGetIterator($this->once(), $values)->finalizeMock();
        
        $loop = 0;
        foreach ($entity as $name => $permissions) {
            switch ($loop) {
                case 0:
                    $this->assertSame("Foo", $name);
                    $this->assertSame(["Foo", "Bar"], $permissions);
                    break;
                case 1:
                    $this->assertSame("Bar", $name);
                    $this->assertSame(["Moz", "Poz"], $permissions);
                    break;
            }
            
            $loop++;
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockAdd()
     */
    public function testMockAdd(): void
    {
        $entity = EntityMock::initMock("Foo")->mockAdd($this->any(), "Foo", ["Foo", "Bar"])->finalizeMock();
        
        $this->assertInstanceOf(Entity::class, $entity->add("Foo", ["Foo", "Bar"]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockAdd_consecutively()
     */
    public function testMockAdd_consecutively(): void
    {
        $values = [
            "Foo"   =>  ["Foo", "Bar"],
            "Bar"   =>  ["Foo", "Bar"],
            "Moz"   =>  ["Foo", "Bar"]
        ];
        $entity = EntityMock::initMock("Foo")->mockAdd_consecutively($this->exactly(3), $values)->finalizeMock();
        
        $this->assertInstanceOf(Entity::class, $entity->add("Foo", ["Foo", "Bar"]));
        $this->assertInstanceOf(Entity::class, $entity->add("Bar", ["Foo", "Bar"]));
        $this->assertInstanceOf(Entity::class, $entity->add("Moz", ["Foo", "Bar"]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockHas()
     */
    public function testMockHas(): void
    {
        $entity = EntityMock::initMock("Foo")->mockHas($this->any(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($entity->has("Foo"));
        
        $entity = EntityMock::initMock("Foo")->mockHas($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($entity->has("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockHas_consecutively()
     */
    public function testMockHas_consecutively(): void
    {
        $entity = EntityMock::initMock("Foo")->mockHas_consecutively($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])->finalizeMock();
        
        $this->assertTrue($entity->has("Foo"));
        $this->assertFalse($entity->has("Bar"));
        $this->assertTrue($entity->has("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockGet()
     */
    public function testMockGet(): void
    {
        $entity = EntityMock::initMock("Foo")->mockGet($this->any(), "Foo", ["Foo", "Bar"])->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $entity->get("Foo"));
        
        $this->expectException(InvalidEntityException::class);
        $entity = EntityMock::initMock("Foo")->mockGet($this->any(), "Foo", null)->finalizeMock();
        $entity->get("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockGet_consecutively()
     */
    public function testMockGet_consecutively(): void
    {
        $values = [
            "Foo"   =>  ["Foo", "Bar"],
            "Bar"   =>  ["Foo", "Bar"],
            "Moz"   =>  ["Foo", "Bar"]
        ];
        
        $entity = EntityMock::initMock("Foo")->mockGet_consecutively($this->exactly(3), $values)->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $entity->get("Foo"));
        $this->assertSame(["Foo", "Bar"], $entity->get("Bar"));
        $this->assertSame(["Foo", "Bar"], $entity->get("Moz"));
        
        $values = [
            "Foo"   =>  ["Foo", "Bar"],
            "Bar"   =>  null
        ];
        
        $this->expectException(InvalidEntityException::class);
        $entity = EntityMock::initMock("Foo")->mockGet_consecutively($this->exactly(2), $values)->finalizeMock();
        
        $entity->get("Foo");
        $entity->get("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $entity = EntityMock::initMock("Foo")->mockGetName($this->any())->finalizeMock();
        
        $this->assertSame("Foo", $entity->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockGetProcessor()
     */
    public function testMockGetProcessor(): void
    {
        $entity = EntityMock::initMock("Foo")->mockGetProcessor($this->any(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $entity->getProcessor());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockIsEmpty()
     */
    public function testMockIsEmpty(): void
    {
        $entity = EntityMock::initMock("Foo")->mockIsEmpty($this->any(), true)->finalizeMock();
        
        $this->assertTrue($entity->isEmpty());
        
        $entity = EntityMock::initMock("Foo")->mockIsEmpty($this->any(), false)->finalizeMock();
        
        $this->assertFalse($entity->isEmpty());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock::mockIsEmpty_consecutively()
     */
    public function testMockIsEmpty_consecutively(): void
    {
        $entity = EntityMock::initMock("Foo")->mockIsEmpty_consecutively($this->exactly(3), true, false, true)->finalizeMock();
        
        $this->assertTrue($entity->isEmpty());
        $this->assertFalse($entity->isEmpty());
        $this->assertTrue($entity->isEmpty());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityMock
     */
    public function testExceptionWhenMethodAlreadyMock(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'has' for entity 'Foo' has been already mocked");
        
        $entity = EntityMock::initMock("Foo")->mockHas($this->any(), "Foo", true)->mockHas($this->any(), "Foo", true)->finalizeMock();
    }
    
}
