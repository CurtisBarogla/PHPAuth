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

namespace Zoe\Component\Security\Acl\Entity\Processor;

use Zoe\Component\Security\Acl\AclUserInterface;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Process the user attributing permissions over roles defined into it
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleEntityProcessor implements EntityProcessorInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::process()
     */
    public function process(EntityInterface $entity, AclUserInterface $user): void
    {
        $roles = $user->getRoles();
        if(empty($roles) || $entity->isEmpty())
            return;
        $resource = $entity->getResource();
        
        switch ($resource->getBehaviour()) {
            case ResourceInterface::WHITELIST:
                $permissions = [];
                foreach ($roles as $role) {
                    if(!$entity->has($role))
                        continue;
                    foreach ($entity->get($role) as $permission) {
                        $permissions[] = $permission;
                    }
                }
                $user->grant($resource, \array_unique($permissions));
                break;
            case ResourceInterface::BLACKLIST:
                $last = null;
                $toDeny = [];
                foreach ($roles as $role) {
                    if(!$entity->has($role))
                        continue;
                    $this->setLowestOrEqualsPermissionsToDeny($last, $toDeny, $entity->get($role));
                    if(\count($toDeny) === 0) {
                        $user->deny($resource, []);
                        return;
                    }
                }
                
                if(!\is_array($toDeny[0]))
                    // no equivoque has been setted, so current is a string
                    $user->deny($resource, $toDeny);
                else
                    $user->deny($resource, $this->determinePermissionsToDenyOverMultiple($resource, $toDeny));
                break;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return "RoleProcessor";
    }
    
    /**
     * Determine to lowest permissions to deny over a set of permissions given iteratively.
     * If multiple set of permissions has the same count, they will be setted as a sub array into $toDeny  
     * 
     * @param int|null $last
     *   Last count setted for the last set of permissions given
     * @param array $toDeny
     *   Current state of the permissions to deny
     * @param array $permissions
     *   Permissions currently processed
     */
    private function setLowestOrEqualsPermissionsToDeny(?int& $last, array& $toDeny, array $permissions): void
    {
        if(null === $last)
            $last = \count($permissions);
        
        $result = ($current = \count($permissions)) <=> $last;
        switch ($result) {
            case -1:
                $toDeny = $permissions;
                $last = $current;
                break;
            case 0:
                $toDeny[] = $permissions;
                break;
        }
    }
    
    /**
     * Determine the permissions to deny over mutiple sets of permission over the value combined of each. 
     * 
     * @param ResourceInterface $resource
     *   Resource to process
     * @param array $permissionsSet
     *   Permissions set
     * 
     * @return array 
     *   Permissions to deny
     */
    private function determinePermissionsToDenyOverMultiple(ResourceInterface $resource, array $permissionsSet): array
    {
        $toDeny = [];
        foreach ($permissionsSet as $permissions) {
            $toDeny[$resource->getPermissions($permissions)->total()->getValue()] = $permissions;
        }
        \ksort($toDeny);
        
        return \current($toDeny);
    }

}
