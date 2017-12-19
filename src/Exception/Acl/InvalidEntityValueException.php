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

namespace Zoe\Component\Security\Exception\Acl;

/**
 * When an invalid value from an entity is given
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidEntityValueException extends \InvalidArgumentException
{
    
    /**
     * Invalid value
     * 
     * @var string
     */
    private $value;
    
    /**
     * Set invalid value
     * 
     * @param string $value
     *   Invalid value
     */
    public function setInvalidValue(string $value): void
    {
        $this->value = $value;
    }
    
    /**
     * Get invalid value
     * 
     * @return string
     *   Invalid value
     */
    public function getInvalidValue(): string
    {
        return $this->value;
    }
    
}
