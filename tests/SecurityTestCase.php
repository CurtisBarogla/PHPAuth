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

namespace ZoeTest\Component\Security;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Internal\GeneratorTrait;
use Zoe\Component\Internal\ReflectionTrait;

/**
 * Common class for Security component testcases
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class SecurityTestCase extends TestCase
{
    
    use ReflectionTrait;
    use GeneratorTrait;
    
}
