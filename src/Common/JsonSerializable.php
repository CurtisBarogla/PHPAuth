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

namespace Zoe\Component\Security\Common;

/**
 * Extends basic interface to set a "factory like" object creation from his json representation
 *  
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface JsonSerializable extends \JsonSerializable
{
    
    /**
     * Restore an object from his json representation.
     * This representation can be either a string or an array
     * 
     * @param string|array $json
     *   Json array or string object representation
     *   
     * @return mixed
     *   Restored object
     */
    public static function restoreFromJson($json);
    
}
