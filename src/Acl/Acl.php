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

use Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;
use Zoe\Component\Security\Exception\ResourceNotFoundException;
use Zoe\Component\Security\Exception\RuntimeException;
use Zoe\Component\Security\User\StorableAclUser;

/**
 * Basic acl implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Acl implements AclInterface
{
    
    /**
     * Acl resource loader
     * 
     * @var ResourceLoaderInterface
     */
    private $loader;
    
    /**
     * Permissions values already processed 
     * 
     * @var int[]
     */
    private $permissions = [];
    
    /**
     * Entity processors
     * 
     * @var EntityProcessorInterface[]
     */
    private $processors;
    
    /**
     * Objects binded with the acl
     * 
     * @var AclBindableInterface[]|null
     */
    private $binded = null;
    
    /**
     * Initialize acl
     * 
     * @param ResourceLoaderInterface $loader
     *   Resource loader
     */
    public function __construct(ResourceLoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::addEntityProcessor()
     */
    public function addEntityProcessor(EntityProcessorInterface $processor): void
    {
        $this->processors[$processor->getName()] = $processor;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::executeProcessables()
     */
    public function executeProcessables(AclUserInterface $user): void
    {
        foreach ($this->getResources() as $name => $resource) {
            $entities = $resource->getEntities();
            if(!empty($entities)) {
                foreach ($entities as $entity) {
                    $processor = $entity->getProcessor();
                    if(null !== $processor) {
                        if(!isset($this->processors[$processor])) {
                            throw new RuntimeException(\sprintf("This processor '%s' for entity '%s' on resource '%s' is not registered into the acl",
                                $processor,
                                $entity->getName(),
                                $name));
                        }
                        $this->processors[$processor]->processUser($user, $resource, $entity);
                    }
                }   
            }
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::getResource()
     */
    public function getResource(string $resource): ResourceInterface
    {
        return $this->loader->loadResource($resource);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::getResources()
     */
    public function getResources(): array
    {
        $resources = [];
        foreach ($this->loader->register() as $resource) {
            $loaded = $this->getResource($resource);
            $resources[$loaded->getName()] = $loaded;
        }
        
        return $resources;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::isGranted()
     */
    public function isGranted(AclUserInterface $user, string $resource, array $permissions): bool
    {
        if(empty($permissions))
            return true;
        
        try {
            $resourceLoaded = $this->getResource($resource);
            $permissionsNormalized = \implode(", ", $permissions);
            $name = $resourceLoaded->getName();
            if(isset($this->permissions[$name][$permissionsNormalized])) {
                $permissions = $this->permissions[$name][$permissionsNormalized];
            } else {
                $permissions = $resourceLoaded->getPermissions($permissions)->total("PERMISSIONS")->getValue();
                $this->permissions[$name][$permissionsNormalized] = $permissions;
            }

            if(isset($this->binded[$name])) {
                $cloned = new StorableAclUser($user->getName());

                $this->binded[$name]->_onBind($cloned, $resourceLoaded);
            }
            
            if(isset($cloned))
                return (bool)($cloned->getPermission($resource)->getValue() & $permissions);
            else 
                return (bool)($user->getPermission($resource)->getValue() & $permissions);
        } catch (ResourceNotFoundException $e) {
            return false;
        } catch (InvalidResourcePermissionException $e) {
            return false;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclInterface::bind()
     */
    public function bind(AclBindableInterface $bindable): void
    {
        $this->binded[$bindable->_getName()] = $bindable;
    }

}
