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

namespace ZoeTest\Component\Security\Acl\Entity;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Internal\GeneratorTrait;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\Resource\ResourceAwareInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Common\JsonSerializable;
use Zoe\Component\Security\Exception\Acl\InvalidEntityValueException;

/**
 * Entity testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Entity
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityTest extends TestCase
{
    
    use GeneratorTrait;
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity
     */
    public function testInterface(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertInstanceOf(EntityInterface::class, $entity);
        $this->assertInstanceOf(ResourceAwareInterface::class, $entity);
        $this->assertInstanceOf(JsonSerializable::class, $entity);
        $this->assertInstanceOf(\IteratorAggregate::class, $entity);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getIterator()
     */
    public function testGetIterator(): void
    {
        $entity = new Entity("Foo");
        
        $entity->add("Foo", ["Foo", "Bar"]);
        $entity->add("Bar", ["Moz", "Poz"]);
        
        $expected = $this->getGenerator(["Foo" => ["Foo", "Bar"], "Bar" => ["Moz", "Poz"]]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $entity->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getIdentifier()
     */
    public function testGetIdentifier(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertSame("Foo", $entity->getIdentifier());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::add()
     */
    public function testAdd(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertNull($entity->add("Foo", ["Foo", "Bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::get()
     */
    public function testGet(): void
    {
        $entity = new Entity("Foo");
        $entity->add("Foo", ["Foo", "Bar"]);
        
        $expected = ["Foo", "Bar"];
        
        $this->assertSame($expected, $entity->get("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::has()
     */
    public function testHas(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertFalse($entity->has("Foo"));
        
        $entity->add("Foo", ["Foo", "Bar"]);
        
        $this->assertTrue($entity->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::isEmpty()
     */
    public function testIsEmpty(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertTrue($entity->isEmpty());
        
        $entity->add("Foo", ["Foo", "Bar"]);
        
        $this->assertFalse($entity->isEmpty());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getProcessor()
     */
    public function testGetProcessor(): void
    {
        $entity = new Entity("Foo");
        
        $this->assertNull($entity->getProcessor());
        
        $entity = new Entity("Foo", "FooProcessor");
        
        $this->assertSame("FooProcessor", $entity->getProcessor());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getResource()
     */
    public function testGetResource(): void
    {
        $resource = ResourceMock::init("ResourceLinkedToEntity")->finalizeMock();
        
        $entity = new Entity("Foo");
        $entity->setResource($resource);
        
        $this->assertSame($resource, $entity->getResource());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $entity = new Entity("Foo", "FooProcessor");
        $entity->add("Foo", ["Foo", "Bar"]);
        
        $this->assertNotFalse(\json_encode($entity));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::restoreFromJson()
     */
    public function testRestoreFromJson(): void
    {
        $entity = new Entity("Foo", "FooProcessor");
        $entity->add("Foo", ["Foo", "Bar"]);
        
        $json = \json_encode($entity);
        
        $this->assertEquals($entity, Entity::restoreFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($entity, Entity::restoreFromJson($json));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::get()
     */
    public function testExceptionGetWhenEntityValueIsInvalidWhenAResourceIsLinked(): void
    {
        $this->expectException(InvalidEntityValueException::class);
        $this->expectExceptionMessage("This value 'Foo' for entity 'Bar' linked to 'Moz' resource is invalid");
        
        $resource = ResourceMock::init("ResourceLinkedToEntityForExceptionTest", ResourceInterface::class)
                                    ->mockGetName($this->once(), "Moz")
                                ->finalizeMock();
        
        $entity = new Entity("Bar");
        $entity->setResource($resource);
        $entity->get("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::get()
     */
    public function testExceptionGetWhenEntityValueIsInvalidWhenAResouceIsNotLinked(): void
    {
        $this->expectException(InvalidEntityValueException::class);
        $this->expectExceptionMessage("This value 'Foo' for entity 'Bar' not linked is invalid");
        
        $entity = new Entity("Bar");
        $entity->get("Foo");
    }
    
}
