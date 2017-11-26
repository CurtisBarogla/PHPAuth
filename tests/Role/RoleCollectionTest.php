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

namespace ZoeTest\Component\Security\Role;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Role\RoleCollection;
use Zoe\Component\Security\Exception\RoleNotFoundException;

/**
 * RoleCollection testcase
 * 
 * @see \Zoe\Component\Security\Role\RoleCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::getIterator()
     */
    public function testGetIterator(): void
    {
        $collection = new RoleCollection();
        $collection->add("Foo");
        $collection->add("Bar", ["Foo"]);
        $collection->add("Moz", ["Bar"]);
        
        $expected = $this->getGenerator(["Foo" => null, "Bar" => ["Foo"], "Moz" => ["Bar", "Foo"]]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $collection->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::add()
     */
    public function testAdd(): void
    {
        $collection = new RoleCollection();
        $this->assertNull($collection->add("Foo"));
        $this->assertNull($collection->add("Bar", ["Foo"]));
        $this->assertNull($collection->add("Moz", ["Bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::getRole()
     */
    public function testGetRole(): void
    {
        $collection = new RoleCollection();
        $collection->add("ROLE1");
        $collection->add("ROLE2");
        $collection->add("ROLE3", ["ROLE1"]);
        $collection->add("ROLE4", ["ROLE3"]);
        $collection->add("ROLE5", ["ROLE1", "ROLE4"]);
        $collection->add("ROLE6", ["ROLE2", "ROLE4"]);
        
        $this->assertSame(["ROLE1"], $collection->get("ROLE1"));
        $this->assertSame(["ROLE2"], $collection->get("ROLE2"));
        $this->assertSame(["ROLE3", "ROLE1"], $collection->get("ROLE3"));
        $this->assertSame(["ROLE4", "ROLE3", "ROLE1"], $collection->get("ROLE4"));
        $this->assertSame(["ROLE5", "ROLE1", "ROLE4", "ROLE3"], $collection->get("ROLE5"));
        $this->assertSame(["ROLE6", "ROLE2", "ROLE4", "ROLE3", "ROLE1"], $collection->get("ROLE6"));
    }
    
    /**
     * @see  \Zoe\Component\Security\Role\RoleCollection::has()
     */
    public function testHasRole(): void
    {
        $collection = new RoleCollection();
        
        $this->assertFalse($collection->has("Foo"));
        
        $collection->add("Foo");
        
        $this->assertTrue($collection->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $collection = new RoleCollection();
        $collection->add("ROLE1");
        $collection->add("ROLE2");
        $collection->add("ROLE3", ["ROLE1"]);
        $collection->add("ROLE4", ["ROLE3"]);
        $collection->add("ROLE5", ["ROLE1", "ROLE4"]);
        $collection->add("ROLE6", ["ROLE2", "ROLE4"]);
        
        $this->assertNotFalse(\json_encode($collection));
    }
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::createRoleCollectionFromJson()
     */
    public function testCreateRoleCollectionFromJson(): void
    {
        $collection = new RoleCollection();
        $collection->add("ROLE1");
        $collection->add("ROLE2");
        $collection->add("ROLE3", ["ROLE1"]);
        $collection->add("ROLE4", ["ROLE3"]);
        $collection->add("ROLE5", ["ROLE1", "ROLE4"]);
        $collection->add("ROLE6", ["ROLE2", "ROLE4"]);
        
        $json = \json_encode($collection);
        
        $this->assertEquals($collection, RoleCollection::createRoleCollectionFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($collection, RoleCollection::createRoleCollectionFromJson($json));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::add()
     */
    public function testExceptionOnInvalidParentRole(): void
    {
        $this->expectException(RoleNotFoundException::class);
        $this->expectExceptionMessage("This parent role 'Bar' for role 'Foo' is not registered");
        
        $collection = new RoleCollection();
        $collection->add("Foo", ["Bar"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollection::getRole()
     */
    public function testExceptionOnGettingInvalidRole(): void
    {
        $this->expectException(RoleNotFoundException::class);
        $this->expectExceptionMessage("This role 'Foo' is not setted");
        
        $collection = new RoleCollection();
        $collection->get("Foo");
    }
    
}
