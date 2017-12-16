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

/**
 * Wrapper around resource to set it in an immutable state.
 * All methods able to modify the resource MUST throw a BadMethodCallException
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface ImmutableResourceInterface extends ResourceInterface
{
    //
}