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
 * Basic bit mask
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
    public function __construct(string $identifier, int $value)
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
     * Add a mask value
     * 
     * @param Mask $mask
     *   Mask to add
     */
    public function add(Mask $mask): void
    {
        $this->value |= $mask->getValue();
    }
    
    /**
     * Sub a mask value
     * 
     * @param Mask $mask
     *   Mask to sub
     */
    public function sub(Mask $mask): void
    {
        $this->value &= ~($mask->getValue());
    }
    
    /**
     * Push bit to left
     * 
     * @param int|null $value
     *   Number of bits to left or null to left one
     * 
     * @return self
     *   self
     */
    public function left(?int $value = null): self
    {
        $this->value <<= $value ?? 1; 
        
        return $this;
    }
    
    /**
     * Push bit to right
     *
     * @param int|null $value
     *   Number of bits to right or null to right one
     *
     * @return self
     *   self
     */
    public function right(?int $value = null): self
    {
        $this->value >>= $value ?? 1;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
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
