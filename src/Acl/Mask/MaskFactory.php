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

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\InvalidArgumentException;

/**
 * Shortcuts for initializing masks for various sources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskFactory
{
    
    /**
     * Create a mask collection for his json representation.
     * Can be a dejsonified array value or its raw string representation
     *
     * @param string|array $json
     *   Mask collection json representation
     *
     * @return MaskCollection
     *   Mask collection with informations setted from json
     */
    public static function createCollectionFromJson($json): MaskCollection
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
            
        $collection = new MaskCollection($json["identifier"]);
        foreach ($json["masks"] as $identifier => $mask) {
            $collection->add(new Mask($identifier, $mask["value"]));
        }
        
        return $collection;
    }
    
    /**
     * Create a mask and initialize it depending of the behaviour of the resource
     * 
     * @param ResourceInterface $resource
     *   Resource instance
     * @param string $name
     *   Mask name
     * @param array $permissions
     *   Permission to apply or deny depending of the resource behaviour
     *   
     * @return Mask
     *   Mask initialized with permissions from the resource setted
     *   
     * @throws InvalidArgumentException
     *   When the behaviour is invalid
     */
    public static function createMaskFromResource(
        ResourceInterface $resource, 
        string $name,
        array $permissions): Mask
    {
        switch ($resource->getBehaviour()) {
            case ResourceInterface::BLACKLIST_BEHAVIOUR:
                $mask = $resource->getPermissions()->total($name);
                foreach ($permissions as $permission) {
                    $mask->sub($resource->getPermission($permission));
                }
                break;
            case ResourceInterface::WHITELIST_BEHAVIOUR:
                $mask = new Mask($name, 0x0000);
                foreach ($permissions as $permission) {
                    $mask->add($resource->getPermission($permission));
                }
                break;
        }
        
        return $mask;
    }
    
}
