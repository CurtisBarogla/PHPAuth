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

namespace ZoeTest\Component\Security\Common;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Common\LazyLoadingTrait;

/**
 * LazyLoadingTrait testcase
 * 
 * @see \Zoe\Component\Security\Common\LazyLoadingTrait
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class LazyLoadTraitTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Common\LazyLoadingTrait::_lazyLoad()
     */
    public function test_lazyLoad(): void
    {
        $test = new TestLazyLoad(false);
        
        $test->GetWithoutLazyLoad("Foo", "Bar");
        $test->GetWithoutLazyLoad("Foo", "Bar");
        $test->GetWithoutLazyLoad("Foo", "Bar");
        
        $this->assertSame(3, TestLazyLoadFixture::$count);
        
        TestLazyLoadFixture::reset();
        
        $test->GetWithLazyLoad("Foo", "Bar");
        $test->GetWithLazyLoad("Foo", "Bar");
        $test->GetWithLazyLoad("Foo", "Bar");
        
        $this->assertSame(1, TestLazyLoadFixture::$count);
    }
    
    /**
     * @see \Zoe\Component\Security\Common\LazyLoadingTrait::_lazyLoad()
     */
    public function test_LazyLoad_shared(): void
    {
        TestLazyLoadFixture::reset();
        
        $test = new TestLazyLoad(true);
        $test2 = new TestLazyLoad(true);
        
        $test->GetWithLazyLoad("Foo", "Bar");
        $test2->GetWithLazyLoad("Foo", "Bar");
        $test->GetWithLazyLoad("Foo", "Bar");
        $test2->GetWithLazyLoad("Foo", "Bar");
        $test->GetWithLazyLoad("Foo", "Bar");
        
        $this->assertSame(1, TestLazyLoadFixture::$count);
    }
    
}

/**
 * For testing purpose
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class TestLazyLoad
{
    use LazyLoadingTrait;
    
    private $share;

    public function __construct(bool $share)
    {
        $this->share = $share;
    }
    
    public function GetWithLazyLoad(string $foo, string $bar): TestLazyLoadFixture
    {
        return $this->_lazyLoad("Foo", $this->share, [TestLazyLoadFixture::class, "init"], $foo, $bar);
    }
    
    public function GetWithoutLazyLoad(string $foo, string $bar): TestLazyLoadFixture
    {
        return TestLazyLoadFixture::init($foo, $bar);
    }

}

/**
 * For testing purpose
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class TestLazyLoadFixture
{
    
    /**
     * Instance count
     * 
     * @var int
     */
    public static $count = 0;

    public $foo;
    
    public $bar;

    public function __construct(string $foo, string $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public static function init(string $foo, string $bar): TestLazyLoadFixture
    {
        self::$count++;
        return new TestLazyLoadFixture($foo, $bar);
    }
    
    /**
     * Reset count
     */
    public static function reset(): void
    {
        self::$count = 0;
    }
    
}
