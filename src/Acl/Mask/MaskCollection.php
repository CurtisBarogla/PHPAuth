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

use Zoe\Component\Security\Exception\InvalidMaskException;
use Countable;

/**
 * Collection of masks
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollection implements \IteratorAggregate, \JsonSerializable, \Countable
{
    
    /**
     * Collection identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Registered masks
     * 
     * @var Mask[]
     */
    private $masks;
    
    /**
     * Initialize collection
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
        foreach ($this->masks as $identifier => $mask) {
            yield $identifier => $mask;
        }
    }
    
    /**
     * Get a mask initialize with the total value of all masks registered into the collection
     * 
     * @param string $identifier
     *   Identifier for the new generated mask
     * 
     * @return Mask
     *   Mask with total of all registered masks setted
     */
    public function total(string $identifier): Mask
    {
        $total = new Mask($identifier, 0);
        foreach ($this->masks as $mask) {
            $total->add($mask);
        }
        
        return $total;
    }
    
    /**
     * Add a mask to the collection
     * 
     * @param Mask $mask
     *   Mask instance
     */
    public function add(Mask $mask): void
    {
        $this->masks[$mask->getIdentifier()] = $mask;        
    }
    
    /**
     * Get a specific mask from the collection
     * 
     * @param string $mask
     *   Mask identifier
     *   
     * @return Mask
     *   Mask requested
     *   
     * @throws InvalidMaskException
     *   When the given mask identifier is not registered
     */
    public function get(string $mask): Mask
    {
        if(!isset($this->masks[$mask]))
            throw new InvalidMaskException(\sprintf("No mask '%s' registered into '%s'collection",
                $mask,
                $this->identifier));
            
        return $this->masks[$mask];
    }
    
    /**
     * Check if a mask is registered into the collection
     * 
     * @param string $mask
     *   Mask identifier
     *   
     * @return bool
     *   True if the mask is registered into the collection. False otherwise
     */
    public function has(string $mask): bool
    {
        return isset($this->masks[$mask]);
    }
    
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     * @see \Countable::count()
     */
    public function count(): int
    {
        return \count($this->masks);
    }
    
}
