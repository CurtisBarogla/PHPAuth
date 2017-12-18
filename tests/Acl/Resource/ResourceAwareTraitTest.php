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

namespace ZoeTest\Component\Security\Acl\Resource;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Acl\Resource\ResourceAwareTrait;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * ResourceAwareTrait testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\ResourceAwareTrait
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceAwareTraitTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ResourceAwareTrait::getResource()
     */
    public function testGetResource(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->finalizeMock();
        $trait = $this->getTrait();
        
        $trait->setResource($resource);
        
        $this->assertSame($resource, $trait->getResource());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ResourceAwareTrait::setResource()
     */
    public function testSetResource(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->finalizeMock();
        $trait = $this->getTrait();
        
        $this->assertNull($trait->setResource($resource));
    }
    
    /**
     * Get mocked trait
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked trait
     */
    private function getTrait(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockForTrait(ResourceAwareTrait::class);
    }
    
}
