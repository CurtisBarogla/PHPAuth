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
        foreach ($this->loader->register() as $resourceName) {
            $resource = $this->loader->loadResource($resourceName);
            $entities = $resource->getEntities();
            if(!empty($entities)) {
                foreach ($entities as $entity) {
                    $processor = $entity->getProcessor();
                    if(null !== $processor) {
                        if(!isset($this->processors[$processor])) {
                            throw new RuntimeException(\sprintf("This processor '%s' for entity '%s' on resource '%s' is not registered into the acl",
                                $processor,
                                $entity->getName(),
                                $resourceName));
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
     * @see \Zoe\Component\Security\Acl\AclInterface::isGranted()
     */
    public function isGranted(AclUserInterface $user, string $resource, array $permissions): bool
    {
        if(empty($permissions))
            return true;
        
        try {
            $resourceLoaded = $this->loader->loadResource($resource);
            $permissions = $resourceLoaded->getPermissions($permissions)->total("PERMISSIONS")->getValue();
            
            if(isset($this->binded[$resourceLoaded->getName()]))
                $this->binded[$resourceLoaded->getName()]->_onBind($user, $resourceLoaded);

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
