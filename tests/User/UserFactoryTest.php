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

namespace ZoeTest\Component\Security\User;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\MutableUser;
use Zoe\Component\Security\User\UserFactory;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\CredentialUser;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\Acl\User\AclUser;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * UserFactory testcase
 * 
 * @see \Zoe\Component\Security\User\UserFactory
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserFactoryTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createCredentialUser()
     */
    public function testCreateCredentialUser(): void
    {
        $user = new MutableUser("foo", true);
        $user->addRole("foo")->addRole("bar")->addAttribute("foo", "bar")->addAttribute("bar", "foo");
        
        $credentialUser = UserFactory::createCredentialUser($user, "foo", ["foo" => "bar", "bar" => "foo"]);
        
        $this->assertInstanceOf(CredentialUserInterface::class, $credentialUser);
        $this->assertInstanceOf(MutableUserInterface::class, $credentialUser);
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $credentialUser->getRoles());
        $this->assertTrue($credentialUser->isRoot());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $credentialUser->getAttributes());
        $this->assertSame("foo", $credentialUser->getPassword());
        $this->assertSame(["foo" => "bar", "bar" => "foo", "password" => "foo"], $credentialUser->getCredentials());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUser()
     */
    public function testCreateStorableUserFromMutable(): void
    {
        $user = new MutableUser("foo", true);
        $user->addRole("foo")->addRole("bar")->addAttribute("foo", "bar")->addAttribute("bar", "foo");
        
        $storableUser = UserFactory::createStorableUser($user, "foo");
        
        $this->assertInstanceOf(StorableUserInterface::class, $storableUser);
        $this->assertFalse($storableUser instanceof MutableUserInterface);
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $storableUser->getRoles());
        $this->assertTrue($storableUser->isRoot());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $storableUser->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUser()
     */
    public function testCreateStorableUserFromCredential(): void
    {
        $user = new CredentialUser("foo", "bar");
        
        $storableUser = UserFactory::createStorableUser($user);
        
        $this->assertInstanceOf(StorableUserInterface::class, $storableUser);
        $this->assertFalse($storableUser instanceof MutableUserInterface);
        $this->assertFalse($storableUser instanceof CredentialUserInterface);
        
        $this->assertFalse($storableUser->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUserFromJson()
     */
    public function testCreateStorableUserFromJson(): void
    {
        $user = new StorableUser("foo", true, ["foo"], ["foo" => "bar"]);
        $json = \json_encode($user);
        
        $this->assertEquals($user, UserFactory::createStorableUserFromJson($json));
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUserFromJson()
     */
    public function testCreateStorableUserWithAclAttributes(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        $resource2 = new Resource("bar", ResourceInterface::WHITELIST_BEHAVIOUR);
        $resource2->addPermission("foo");
        $resource2->addPermission("bar");
        $user = new AclUser("foo");
        $user->deny($resource, ["foo"]);
        $user->grant($resource2, ["foo", "bar"]);
        
        $json = \json_encode($user);
        
        $this->assertEquals($user, UserFactory::createStorableUserFromJson($json));
    }
    
}