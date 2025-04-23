<?php declare(strict_types=1);

namespace Packrat;

use Closure;

function literal($pattern): Pattern
{
    return new Pattern('literal', [$pattern]);
}

function chain(...$patterns): Pattern
{
    return new Pattern('chain', $patterns);
}

function oneOf(...$patterns): Pattern
{
    return new Pattern('oneOf', $patterns);
}

function repeat($pattern): Pattern
{
    return new Pattern('repeat', [$pattern]);
}

function not($pattern): Pattern
{
    return new Pattern('not', [$pattern]);
}

function anyChar(): Pattern
{
    return new Pattern('anyChar', []);
}

function capture($pattern): Pattern
{
    return new Pattern('capture', [$pattern]);
}

function named($name, $pattern): Pattern
{
    return new Pattern('named', [$pattern], $name);
}
