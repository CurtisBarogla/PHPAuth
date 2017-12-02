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

/**
 * Common to all entity processors
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AbstractEntityProcessor implements EntityProcessorInterface
{
    
    /**
     * Process state
     * 
     * @var bool
     */
    protected $process = false;
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface::isProcess()
     */
    public function isProcess(): bool
    {
        return $this->process;
    }
    
}
