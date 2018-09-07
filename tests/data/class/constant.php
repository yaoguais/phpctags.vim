<?php

namespace Foo;

class Bar
{
    const BAZ = "baz";
}

class Baz extends Bar implements BarBaz, BazBar
{
    use FooBar;
}

trait FooBar
{
    // const QUX = "qux"; // Traits cannot have constants
}

interface BarBaz
{
    const FOO = "foo";
}

interface BazBar
{
    const BAR = "bar";
}
