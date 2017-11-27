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
use Zoe\Component\Security\Role\RoleCollectionFactory;

/**
 * RoleCollectionFactory testcase
 * 
 * @see \Zoe\Component\Security\Role\RoleCollectionFactory
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollectionFactoryTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Role\RoleCollectionFactory::createRoleCollectionFromArray()
     */
    public function testCreateRoleCollectionFromArray(): void
    {
        $roles = [
            "Foo",
            "Bar",
            "Moz"   =>  ["Foo"],
            "Poz"   =>  ["Bar"],
            "Loz"   =>  ["Moz", "Poz"]
        ];
        
        $expectedCollection = new RoleCollection();
        $expectedCollection->add("Foo");
        $expectedCollection->add("Bar");
        $expectedCollection->add("Moz", ["Foo"]);
        $expectedCollection->add("Poz", ["Bar"]);
        $expectedCollection->add("Loz", ["Moz", "Poz"]);
        
        $this->assertEquals($expectedCollection, RoleCollectionFactory::createRoleCollectionFromArray($roles));
    }
    
}
