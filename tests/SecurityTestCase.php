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
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;

/**
 * Common class for Security component testcases
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class SecurityTestCase extends TestCase
{
    
    use ReflectionTrait;

    /**
     * Get a mocked user
     * 
     * @param string $userType
     *   Type of user to generate (Storable, Credential, Mutable)
     * @param string $name
     *   Returned bu getName()
     * @param bool $isRoot
     *   Return by isRoot()
     * @param int|null $placeholdersCount
     *   Number of placeholders attributes (and credentials if CredentialUser) to generate (returned by getAttributes() and getCredentials()) 
     * @param int $rolesCount
     *   Number of roles to generate (return by getRoles())
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked user type
     */
    public function getMockedUser(
        string $userType,
        string $name, 
        bool $isRoot = false,
        ?int $placeholdersCount = null, 
        ?int $rolesCount = null): \PHPUnit_Framework_MockObject_MockObject
    {
        $methods = $this->reflection_extractMethods(new \ReflectionClass($userType));
        
        $mock = $this->getMockBuilder($userType)->setMethods($methods)->getMock();
        $mock->method("getName")->will($this->returnValue($name));
        $mock->method("isRoot")->will($this->returnValue($isRoot));
        if(null !== $placeholdersCount) {
            $mock->method("getAttributes")->will($this->returnValue($this->getPlaceholders($placeholdersCount)));
            if($userType === CredentialUserInterface::class) {
                $mock->method("getCredentials")->will($this->returnValue($this->getPlaceholders($placeholdersCount)));
            }
        }
        if(null !== $rolesCount) {
            $mock->method("getRoles")->will($this->returnValue($this->getRolePlaceholders($rolesCount)));
        }
        
        return $mock;
    }
    
    public function getMockedUserLoader(string $identifier): \PHPUnit_Framework_MockObject_MockObject
    {
        $methods = $this->reflection_extractMethods(new \ReflectionClass(UserLoaderInterface::class));
        $mock = $this->getMockBuilder(UserLoaderInterface::class)->setMethods($methods)->getMock();
        $mock->method("identify")->will($this->returnValue($identifier));
        
        return $mock;
    }
    
    /**
     * Generate placeholders for attributes or credentials
     * 
     * @param int $count
     *   Number of placeholders to generate
     * @return array
     *   Placeholders
     * @throws \LogicException
     *   if > placeholders given
     */
    private function getPlaceholders(int $count): array
    {
        $placeholders = ["foo" => "bar", "bar" => "foo", "poz" => "moz"];
        $keys = \array_keys($placeholders);
        if($count > $c = \count($placeholders)) {
            throw new \LogicException(\sprintf("Count cannot be > {$c}. '%d' given",
                $count));
        }
        
        $return = [];
        for ($i = 0; $i < $count; $i++) {
            $return[$keys[$i]] = $placeholders[$keys[$i]];
        }

        return $return;
    }
    
    /**
     * Generate role placeholders
     * 
     * @param int $count
     *   Number of roles to generate
     * @return array
     *   Roles placeholders
     * @throws \LogicException
     *   If > placeholders given
     */
    private function getRolePlaceholders(int $count): array
    {
        $placeholders = ["foo", "bar", "poz"];
        
        if($count > $c = \count($placeholders)) {
            throw new \LogicException(\sprintf("Count cannot be > {$c}. '%d' given",
                $count));
        }
        
        $return = [];
        
        for ($i = 0; $i < $count; $i++) {
            $return[$placeholders[$i]] = $placeholders[$i];
        }
        
        return $return;
    }
    
}
