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

namespace Zoe\Component\Security\Acl;

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface;
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Resource\ResourceAwareInterface;
use Zoe\Component\Security\Common\LazyLoadingTrait;

class Acl implements AclInterface
{
    
    use LazyLoadingTrait;
    
    /**
     * Resource loader
     * 
     * @var ResourceLoaderInterface
     */
    private $loader;
    
    /**
     * Entity processors
     * 
     * @var EntityProcessorInterface[]
     */
    private $processors = [];
    
    public function __construct(ResourceLoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    
    public function registerProcessor(EntityProcessorInterface $processor): void
    {
        $this->processors[$processor->getIdentifier()] = $processor;        
    }
    
    public function getResource(string $resource): ResourceInterface
    {
        return $this->_lazyLoad("ACL_RESOURCE_{$resource}", true, [$this->loader, "loadResource"], $resource);
    }
    
    public function isAllowed(AuthenticatedUserInterface $user, string $resource, array $permissions, ?callable $process = null): bool
    {
        $resourceLoaded = $this->getResource($resource);
        $required = $this->_lazyLoad(
            "REQUIRED_{$resource}_".\implode("", $permissions), 
            true, 
            function() use ($permissions, $resourceLoaded): int {
                return $resourceLoaded->getPermissions($permissions)->total()->getValue();
            }
        );
        
        $user = $this->_lazyLoad("USER_{$resource}", true, [$this, "createAclUser"], $resourceLoaded, $user);
        
        if(!$resourceLoaded->isProcessed())
            $resourceLoaded->process($user, $this->processors);
        
        $current = (bool) ( ($user->getPermissions()->getValue() & $required) === $required );
        
        $exec = (null === $process) 
                    ? null 
                    : (null !== $toProcess = \call_user_func($process, $user, $current)) 
                        ?: null; 
        
        if(null === $exec) {
            return $current;
        }
        
        $user = clone $user;
        $toProcess($resourceLoaded, $user);
        
        return (bool) ( ($user->getPermissions()->getValue() & $required) === $required );
    }
    
    private function createAclUser(ResourceInterface $resource, AuthenticatedUserInterface $user): AclUserInterface
    {
        $value = ($resource->getBehaviour() === ResourceInterface::BLACKLIST) ? $resource->getPermissions()->total()->getValue() : 0;
        
        return new AclUser(new Mask("PERMISSION_{$user->getName()}_RESOURCE_{$resource->getName()}", $value), $user);
    }

    
}
