# ROAD MAP

## constant

- [x] constant defined by function define()
- [ ] constant defined by keyword const

## variable

- [x] variable in main function
- [x] variable in global function
- [x] variable in class method
- [x] variable in closure function
- [ ] variable in comment
- [x] variable in function parameters
- [ ] variable in function document comment
- [x] variable in method parameters
- [ ] variable in method document comment
- [ ] variable in closure function parameters
- [ ] variable in closure function use parameters

## function

- [x] global function call
- [ ] function returns

## class

- [x] class
- [x] trait
- [x] interface

## method

- [x] method defined by class
- [ ] method defined by class document comment
- [ ] method defined by parent class
- [ ] method defined by parent class document comment
- [x] method call by class::method()
- [x] method call by self::method()
- [x] method call by static::method()
- [ ] method call by parent::method()
- [x] method call by $this->method()
- [x] method call by (new Class())->method()
- [ ] method call by anonymous class
- [ ] method call by function returns
- [ ] method call by other method returns

## property

- [x] class const property
- [ ] class const property defined by document comment
- [ ] class variable property
- [ ] class variable property defined by document comment
- [ ] class static variable property
- [ ] class static variable property defined by document comment


### constant defined by function define()

```php
index.php:

define('CONSTANT', 'CONSTANT STRING');
```

### constant defined by keyword const

```php
foo.php:

const CONSTANT = 'CONSTANT STRING';

bar.php:

namespace Foo;

const CONSTANT = 'CONSTANT STRING';
```

### variable in main function

```php
index.php:

$foo = $bar;
$bar = $baz;
$baz = $qux;
```

### variable in global function

```php
foo.php:

function foo() {
    $foo = $bar;
    $bar = $baz;
    $baz = $qux;
}
```

### variable in class method

```php
foo.php:

class Foo {
    function bar() {
        $foo = $bar;
        $bar = $baz;
        $baz = $qux;
    }
}
```

### variable in closure function

```php
foo.php:

$foo = function () {
    $foo = $bar;
    $bar = $baz;
    $baz = $qux;
}
```

### variable in comment

```php
foo.php:

/** @var string $foo */
/** @var $foo   string */
/** @var \Foo\Bar $bar */
/** @var $bar \Foo\Bar */
```

### variable in function parameters

```php
foo.php:

function foo($bar) {
    $baz = $bar;
    $qux = $baz;
}
```

### variable in function document comment

```php
foo.php:

/**
 * @param \Foo\Bar $bar
 * @return \stdClass
 */
function foo($bar) {
    return new \stdClass();
}
```

### variable in method parameters

```php
foo.php:

class Foo {
    function bar(\Foo\Bar $baz) {
        $qux = $baz;
    }
}
```

### variable in method document comment

```php
foo.php:

class Foo {
    /**
     * @param \Foo\Bar $bar
     * @return \Foo\Bar
     */
    function foo ($bar) {
        $baz = $bar;
        return $baz;
    }
}
```

### variable in closure function parameters

```php
foo.php:

$foo = function (\Foo\Bar $bar) {
    $baz = $bar;
    return $baz;
}
```

### variable in closure function use parameters

```php
foo.php:

$foo = function () use (\Foo\Bar $bar) {
    $baz = $bar;
    return $baz;
}
```

### global function call

```php
foo.php:

function foo() {

}

foo();
```

### function returns

```php
foo.php:

/**
 * @param \Foo\Bar $bar
 * @return \Foo\Bar
 */
function foo($bar) {
    return new \Foo\Bar();
}
```

### class

```php
foo.php:

class Foo {

}
```

### trait

```php
foo.php:

trait Foo {

}
```

### interface

```php
foo.php:

interface Foo {

}
```

### method defined by class

```php
foo.php:

class Foo {
    function bar() {
    }
}

$foo = new Foo();
$foo->bar();
```

### method defined by class document comment

```php
foo.php:

/**
 * Class Foo.
 *
 * @method bar(string $baz)
 */
class Foo
{
}

$foo = new Foo();
$foo->bar('bar');
```

### method defined by parent class

```php
foo.php:

class Foo {
    public function foo() {
    
    }
}

class Bar extends Foo {
}

$bar = new Bar();
$bar->foo();
```

### method defined by parent class document comment

```php
foo.php:

/**
 * Class Foo.
 *
 * @method foo(string $foo)
 */
class Foo
{
}
class Bar extends Foo {
}
$bar = new Bar();
$bar->foo('foo');
```

### method call by class::method()

```php
foo.php:

class Foo() {
    static function foo() {
    }
}
Foo::foo();
```

### method call by self::method()

```php
foo.php:

class Foo() {
    static function foo() {
    }
    
    static function bar() {
        self::foo();
    }
}
Foo::bar();
```

### method call by static::method()

```php
foo.php:

class Foo() {
    static function foo() {
    }
    
    static function bar() {
        static::foo();
    }
}
Foo::bar();
```

### method call by parent::method()

```php
foo.php:

class Foo() {
    static function foo() {
    }
}
class Bar extends Foo {
}
Bar::foo();
```

### method call by $this->method()

```php
foo.php:

class Foo {
    public function foo() {
    }
    public function bar() {
        $this->foo();
    }
}
$foo = new Foo();
$foo->bar();
```

### method call by (new Class())->method()

```php
foo.php:

class Foo {
    public function foo() {
    }
}
$foo = new Foo();
$bar = $foo;
$bar->foo();
```

### method call by anonymous class

```php
foo.php:

$foo = new class {
    function foo() {
    }
}
$foo->foo();
```

### method call by function returns

```php
foo.php:

function foo() {
    return new Bar();
}
class Bar {
    function bar() {
    }
}

$foo = foo();
$foo->bar();
foo()->bar();
```

### method call by other method returns

```php
foo.php:

class Foo {
    function foo() {
        return new Bar();
    }
}
class Bar {
    function bar() {
    }
}
$foo = new Foo();
$foo->foo()->bar();
```

### class const property

```php
foo.php:

class Foo {
    const Bar = 0;
}
echo Foo::Bar;
```

### class const property defined by document comment

    // TODO

### class variable property

    // TODO
    
### class variable property defined by document comment

    // TODO

### class static variable property

    // TODO
    
### class static variable property defined by document comment

    // TODO
