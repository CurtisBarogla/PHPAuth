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

/**
 * Bit mask
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Mask implements \JsonSerializable
{
    
    /**
     * Mask identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Mask value
     * 
     * @var int
     */
    private $value;
    
    /**
     * Initialize a mask
     * 
     * @param string $identifier
     *   Mask identifier
     * @param int $value
     *   Mask value
     */
    public function __construct(string $identifier, int $value = 0)
    {
        $this->identifier = $identifier;
        $this->value = $value;
    }
    
    /**
     * Get mask identifier
     * 
     * @return string
     *   Mask identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    
    /**
     * Get mask value
     * 
     * @return int
     *   Mask value
     */
    public function getValue(): int
    {
        return $this->value;
    }
    
    /**
     * Add a mask value to this one
     * 
     * @param Mask $mask
     *   Mask to add
     */
    public function add(Mask $mask): void
    {
        $this->value |= $mask->getValue();
    }
    
    /**
     * Sub a mask value to this one
     * 
     * @param Mask $mask
     *   Mask to sub
     */
    public function sub(Mask $mask): void
    {
        $this->value &= ~($mask->getValue());
    }
    
    /**
     * Shift bit to right by one or by a defined value
     * 
     * @param int $value
     *   Number of bits to shift to right
     */
    public function rshift(int $value = 1): void
    {
        $this->value >>= $value;
    }
    
    /**
     * Shift bit to left by one or a by a defined value
     * 
     * @param int $value
     *   Number of bits to shift to left
     */
    public function lshift(int $value = 1): void
    {
        $this->value <<= $value;
    }
    
    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "identifier"    =>  $this->identifier,
            "value"         =>  $this->value
        ];
    }
    
}
