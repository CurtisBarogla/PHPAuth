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

use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;

/**
 * Build entity over user roles
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleEntityProcessor extends AbstractEntityProcessor
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::buildUser()
     */
    public function processUser(AclUserInterface $user, ResourceInterface $resource, Entity $entity): void
    {
        $roles = $user->getRoles();
        if(empty($roles) || $entity->isEmpty()) {
            $this->process = true;
            
            return;
        }
    
        switch ($resource->getBehaviour()) {
            case ResourceInterface::BLACKLIST_BEHAVIOUR:
                $map = [];
                $lastCount = null;
                foreach ($roles as $role) {
                    \var_dump($role);
                    if(!$entity->has($role))
                        continue;
                    $clearPermissions = $entity->get($role);
                    $permissions = $resource->getPermissions($clearPermissions);
                    $count = count($permissions);
                    if($count === 0) {
                        unset($map);
                        unset($permissions);
                        $user->deny($resource, []);
                        $this->process = true;
                        return;
                    }
                    if(null === $lastCount)
                        $lastCount = $count;
                    if($count < $lastCount) {
                        $lastCount = $count;
                        unset($map);
                    }
                    $value = -$permissions->total("TOTAL_{$role}")->getValue();
                    $map[$value]["clear"] = $clearPermissions;
                    unset($value);
                }
                \ksort($map);
                $permissions = \current($map);
                unset($map);
                $user->deny($resource, $permissions["clear"] ?? []);
                break;
            case ResourceInterface::WHITELIST_BEHAVIOUR:
                $permissionsToGrant = [];
                foreach ($entity as $role => $permissions) {
                    if(!$user->hasRole($role))
                        continue;
                    foreach ($permissions as $permission) {
                        if(!isset($permissionsToGrant[$permission]))
                            $permissionsToGrant[] = $permission;
                    }
                }
                $user->grant($resource, $permissionsToGrant);
                break;
        }
        
        $this->process = true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::getName()
     */
    public function getName(): string
    {
        return "RoleProcessor";
    }
    
}
