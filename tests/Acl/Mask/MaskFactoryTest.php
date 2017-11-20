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

namespace Zoe\Component\Security\Acl\Mask;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Exception\InvalidArgumentException;

/**
 * MaskFactory testcase
 * 
 * @see \Zoe\Component\Security\Acl\Mask\MaskFactory
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskFactoryTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskFactory::createMaskFromResource()
     */
    public function testCreateMaskFromResource(): void
    {
        // blacklist behaviour generation
        
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        
        $mask = MaskFactory::createMaskFromResource($resource, "foo", ["foo"]);
        
        $this->assertSame("foo", $mask->getIdentifier());
        $this->assertSame(0x0002, $mask->getValue());
        
        // whitelist behaviour generation
        
        $resource = new Resource("foo", ResourceInterface::WHITELIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        
        $mask = MaskFactory::createMaskFromResource($resource, "foo", ["foo", "bar"]);
        
        $this->assertSame("foo", $mask->getIdentifier());
        $this->assertSame(0x0003, $mask->getValue());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskFactory::createMaskFromResource()
     */
    public function testExceptionWhenInvalidBehaviourIsGiven(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("This behaviour '5' for the resource 'foo' is invalid");
        
        $resource = new Resource("foo", 5);
        $resource->addPermission("foo");
        
        $mask = MaskFactory::createMaskFromResource($resource, "foo", ["foo"]);
    }
    
}
