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
 * Kinda lazy loading "proxy"
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
trait LazyLoadingTrait
{
    
    /**
     * Container restricted to current instance
     * 
     * @var array
     */
    private $Self_Container = [];
    
    /**
     * Values setted into the container are shared over all instances of the same object
     * 
     * @var array
     */
    private static $Shared_Container = [];
    
    /**
     * Lazy load value
     * 
     * @param string $loadId
     *   Lazy load value id (unique)
     * @param bool $shared
     *   Set to true to share values over all instances of the same object
     * @param callable $value
     *   Value to lazy load (can store pretty much everything that can be stored into an array)
     * @param mixed ...$args
     *   Args passed to the callback
     * 
     * @return mixed
     *   Value lazy loaded
     */
    public function _lazyLoad(string $loadId, bool $shared, callable $value, ...$args)
    {        
        if($shared)
            $container = &self::$Shared_Container;
        else
            $container = &$this->Self_Container;
        
        if(!isset($container[$loadId])) {
            $value = \call_user_func($value, ...$args);
            
            $container[$loadId] = $value;
            
            return $value;
        }
        
        return $container[$loadId];
    }
    
}
