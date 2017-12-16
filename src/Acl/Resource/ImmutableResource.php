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

namespace Zoe\Component\Security\Acl\Resource;

use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;

/**
 * Wrapper around resource to set it as immutable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ImmutableResource implements ImmutableResourceInterface
{
    
    /**
     * Resource wrapped
     * 
     * @var ResourceInterface
     */
    private $resource;
    
    /**
     * Initialize resource
     * 
     * @param ResourceInterface $resource
     *   Resource to wrap
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getName()
     */
    public function getName(): string
    {
        return $this->resource->getName();
    }
    
    /**
     * @throws \BadMethodCallException
     *   Resource is immutable
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::addPermission()
     */
    public function addPermission(string $permission): void
    {
        throw new \BadMethodCallException(\sprintf("Cannot add permission on resource '%s' as it is set to an immutable state",
            $this->resource->getName()));
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermissions()
     */
    public function getPermissions(?array $permissions = null): MaskCollection
    {
        return $this->resource->getPermissions($permissions);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getPermission()
     */
    public function getPermission(string $permission): Mask
    {
        return $this->resource->getPermission($permission);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::hasPermission()
     */
    public function hasPermission(string $permission): bool
    {
        return $this->resource->hasPermission($permission);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Resource\ResourceInterface::getBehaviour()
     */
    public function getBehaviour(): int
    {
        return $this->resource->getBehaviour();
    }
    
}
