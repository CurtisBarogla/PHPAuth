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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Exception\InvalidEntityException;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * EntityTest testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Entity
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getIterator()
     */
    public function testGetIterator(): void
    {
        $entity = new Entity("Foo", "Bar");
        $entity->add("Foo", ["foo", "bar"]);
        $entity->add("Bar", ["foo", "bar"]);
        
        $expected = $this->getGenerator(["Foo" => ["foo", "bar"], "Bar" => ["foo", "bar"]]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $entity->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::add()
     */
    public function testAdd(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertInstanceOf(Entity::class, $entity->add("Foo", []));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::has()
     */
    public function testHas(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertFalse($entity->has("Foo"));
        
        $entity->add("Foo", []);
        
        $this->assertTrue($entity->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::get()
     */
    public function testGet(): void
    {
        $entity = new Entity("Foo", "Bar");
        $entity->add("Foo", ["foo", "bar"]);
        
        $this->assertSame(["foo", "bar"], $entity->get("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getName()
     */
    public function testGetName(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertSame("Foo", $entity->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::getProcessor()
     */
    public function testGetProcessor(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertSame("Bar", $entity->getProcessor());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::isEmpty()
     */
    public function testIsEmpty(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertTrue($entity->isEmpty());
        
        $entity->add("Foo", []);
        
        $this->assertFalse($entity->isEmpty());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $this->assertNotFalse(\json_encode($entity));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::createEntityFromJson()
     */
    public function testCreateEntityFromJson(): void
    {
        $entity = new Entity("Foo", "Bar");
        
        $json = \json_encode($entity);

        $this->assertEquals($entity, Entity::createEntityFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($entity, Entity::createEntityFromJson($json));
        
        $entity = new Entity("Foo", "Bar");
        $entity->add("Foo", ["foo", "bar"])->add("Bar", ["bar", "foo"]);
        
        $json = \json_encode($entity);
        
        $this->assertEquals($entity, Entity::createEntityFromJson($json));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Entity::get()
     */
    public function testExceptionGetInvalidEntityValue(): void
    {
        $this->expectException(InvalidEntityException::class);
        $this->expectExceptionMessage("This value 'Foo' into 'Bar' resource is not registered");
        
        $entity = new Entity("Bar", "Bar");
        
        $entity->get("Foo");
    }
    
}
