<?php declare(strict_types=1);

namespace Packrat;

use Closure;

function literal($pattern): Pattern
{
    return new Pattern('literal', [$pattern]);
    // return function ($text, $start) use ($pattern) {
    //     if (str_starts_with(substr($text, $start), $pattern)) {
    //         return Option::some(new Matcher($text, $start, $start+strlen($pattern)));
    //     }
    //     return Option::none();
    // };
}

function chain(...$patterns): Pattern
{
    return new Pattern('chain', $patterns);
    // return function ($text, $start) use ($patterns) {
    //     $children = [];
    //     $pos = $start;
    //     foreach ($patterns as $pattern) {
    //         $opt = $pattern($text, $pos);
    //         if ($opt->isNone()) {
    //             return Option::none();
    //         }
    //         $result = $opt->value();
    //         $children[] = $result;
    //         $pos = $result->end;
    //     }
    //     return Option::some(new Matcher($text, $start, $pos, $children));
    // };
}

function oneOf(...$patterns): Pattern
{
    return new Pattern('oneOf', $patterns);
    // return function ($text, $start) use ($patterns) {
    //     foreach ($patterns as $pattern) {
    //         $opt = $pattern($text, $start);
    //         if ($opt->isSome()) {
    //             return Option::some(new Matcher($text, $start, $opt->value()->end));
    //         }
    //     }
    //     return Option::none();
    // };
}

function repeat($pattern): Pattern
{
    return new Pattern('repeat', [$pattern]);
    // return one_of(
    //     chain(
    //         $pattern,
    //         function ($text, $start) use ($pattern) {
    //             return repeat($pattern)($text, $start);
    //         }
    //     ),
    //     literal("")
    // );
}

function not($pattern): Pattern
{
    return new Pattern('not', [$pattern]);
    // return function ($text, $start) use ($pattern) {
    //     $opt = $pattern($text, $start);
    //     if ($opt->isNone()) {
    //         return Option::some(new Matcher($text, $start, $start));
    //     }
    //     return Option::none();
    // };
}

function anyChar(): Pattern
{
    return new Pattern('anyChar', []);
    // return function($text, $start) {
    //     if ($start < strlen($text)) {
    //         return Option::some(new Matcher($text, $start, $start+1));
    //     }
    //     return Option::none();
    // };
}

function capture($pattern): Pattern
{
    return new Pattern('capture', [$pattern]);
}

function named($name, $pattern): Pattern
{
    return new Pattern('named', [$pattern], $name);
}
