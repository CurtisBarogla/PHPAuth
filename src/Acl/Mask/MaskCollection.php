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

use Zoe\Component\Security\Exception\Acl\InvalidMaskException;
use Zoe\Component\Security\Common\JsonSerializable;

/**
 * Collection of masks
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollection implements JsonSerializable, \IteratorAggregate, \Countable
{
    
    /**
     * Collection identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Stored masks
     * 
     * @var Mask[]
     */
    private $masks;
    
    /**
     * Initiliaze collection
     * 
     * @param string $identifier
     *   Collection identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
    
    /**
     * {@inheritDoc}
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator(): \Generator
    {
        foreach ($this->masks as $name => $mask) {
            yield $name => $mask;
        }
    }
    
    /**
     * Get total value of all masks defined in the collection as a mask
     * 
     * @param string|null $totalIdentifier
     *   Mask total name
     * @param array|null $masks
     *   If only specific already defined mask must be added
     * 
     * @return Mask
     *   Mask totalizing all defined masks
     */
    public function total(?string $totalIdentifier = null, ?array $masks = null): Mask
    {
        $total = new Mask($totalIdentifier ?? "TOTAL_{$this->identifier}");
        foreach ($masks ?? $this->masks as $mask) {
            if($mask instanceof Mask)
                $total->add($mask);
            else {
                $this->checkMask($mask);
                $total->add($this->masks[$mask]);
            }
        }
        
        return $total;
    }
    
    /**
     * Add a mask to the collection
     * 
     * @param Mask $mask
     *   Mask to add
     */
    public function add(Mask $mask): void
    {
        $this->masks[$mask->getIdentifier()] = $mask;
    }
    
    /**
     * Get a mask from the collection
     * 
     * @param string $mask
     *   Mask name
     *   
     * @return Mask
     *   Mask
     *   
     * @throws InvalidMaskException
     *   When the given mask is not registered
     */
    public function get(string $mask): Mask
    {
        $this->checkMask($mask);
        
        return $this->masks[$mask];
    }
    
    /**
     * Check if a mask is setted into the collection
     * 
     * @param string $mask
     *   Mask name
     * 
     * @return bool
     *   True if the mask is in the collection. False otherwise
     */
    public function has(string $mask): bool
    {
        return isset($this->masks[$mask]);
    }
    
    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            "identifier"    =>  $this->identifier,
            "masks"         =>  $this->masks
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Countable::count()
     */
    public function count(): int
    {
        return \count($this->masks);
    }
    
    /**
     * @return MaskCollection
     *   MaskCollection restored
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Common\JsonSerializable::restoreFromJson()
     */
    public static function restoreFromJson($json): MaskCollection
    {
        if(!\is_array($json))
            $json = \json_decode($json, true);
        
        $collection = new MaskCollection($json["identifier"]);
        $collection->masks = \array_map(function(array $mask): Mask {
            return new Mask($mask["identifier"], $mask["value"]);
        }, $json["masks"]);
        
        return $collection;
    }

    /**
     * Check if a mask is registered into the collection
     * 
     * @param string $mask
     *   Mask name to check
     * 
     * @throws InvalidMaskException
     *   If the given mask is not registered
     */
    private function checkMask(string $mask): void
    {
        if(!isset($this->masks[$mask]))
            throw new InvalidMaskException(\sprintf("This mask '%s' into collection '%s' is not registered",
                $mask,
                $this->identifier));
    }
    
}
