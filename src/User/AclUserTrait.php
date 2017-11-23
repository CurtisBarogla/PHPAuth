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

namespace Zoe\Component\Security\User;

use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\MaskFactory;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\InvalidMaskException;
use Zoe\Component\Security\Exception\ResourceNotFoundException;

/**
 * Common trait for acl user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
trait AclUserTrait
{

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\AclUserInterface::getPermission()
     */
    public function getPermission(string $resource): Mask
    {
        try {
            return $this->getPermissionsContainer()->get($resource);
        } catch (InvalidMaskException $e) {
            throw new ResourceNotFoundException(\sprintf("This resource '%s' is not registered",
                $resource));
        }
    }
    
    /**
     * Apply permissions over a resource on user
     *
     * @param Resource $resource
     *   Resource instance
     * @param array $permissions
     *   Permissions to apply
     * @param string $action
     *   Action on the mask to execute
     */
    protected function apply(ResourceInterface $resource, array $permissions, string $action): void
    {
        try {
            $mask = $this->getPermissionsContainer()->get($resource->getName());
            
            foreach ($permissions as $permission) {
                $mask->{$action}($resource->getPermission($permission));
            }
        } catch (InvalidMaskException $e) {
            $mask = MaskFactory::createMaskFromResource($resource, $resource->getName(), $permissions);
        }
        
        try {
            $this->getPermissionsContainer()->refresh($mask);
        } catch (InvalidMaskException $e) {
            $this->getPermissionsContainer()->add($mask);
        }
    }
    
    /**
     * Initialize permissions container
     * 
     * @return MaskCollection
     *   Permissions container
     */
    abstract protected function getPermissionsContainer(): MaskCollection;
    
}
