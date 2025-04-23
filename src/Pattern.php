<?php declare(strict_types=1);

namespace Packrat;

class Pattern
{
    private array $cache = [];
    public function __construct(
        public readonly string $type,
        public readonly array  $patterns,
        public readonly string $name = '',
    )
    {
    }

    public function __invoke(string $text, int $start): Option
    {
        return $this->{$this->type}($text, $start);
    }

    public function match($text, $start)
    {
        if (!isset($this->cache[$start])) {
            $this->cache[$start] = $this->{$this->type}($text, $start);
        }
        return $this->cache[$start];
    }

    function literal($text, $start) {
        if (strpos($text, $this->patterns[0], $start) === $start) {
            return Option::some(new Matcher($text, $start, $start+strlen($this->patterns[0]), name: $this->name));
        }
        return Option::none();
    }

    function chain($text, $start) {
        $children = [];
        $pos = $start;
        foreach ($this->patterns as $pattern) {
            $opt = $pattern->match($text, $pos);
            if ($opt->isNone()) {
                return Option::none();
            }
            $result = $opt->value();
            $children[] = $result;
            $pos = $result->end;
        }
        return Option::some(new Matcher($text, $start, $pos, $children, name: $this->name));
    }

    function oneOf($text, $start) {
        foreach ($this->patterns as $pattern) {
            $opt = $pattern->match($text, $start);
            if ($opt->isSome()) {
                $result = $opt->value();
                $children = [$result];
                return Option::some(new Matcher($text, $start, $opt->value()->end, $children, name: $this->name));
            }
        }
        return Option::none();
    }

    function repeat($text, $start)
    {
        $a = new Pattern('chain', [...$this->patterns, new Pattern('repeat', $this->patterns)]);
        $b = new Pattern('literal', [""]);
        $c = new Pattern('oneOf', [$a, $b]);

        return $c->match($text, $start);
    }

    function not($text, $start) {
        $opt = $this->patterns[0]->match($text, $start);
        if ($opt->isNone()) {
            return Option::some(new Matcher($text, $start, $start, name: $this->name));
        }
        return Option::none();
    }

    function anyChar($text, $start) {
        if ($start < strlen($text)) {
            return Option::some(new Matcher($text, $start, $start+1, name: $this->name));
        }
        return Option::none();
    }

    function capture($text, $start)
    {
        $opt = $this->patterns[0]($text, $start);
        if ($opt->isSome()) {
            $result = $opt->value();
            $captured = substr($text, $start, $result->end-$start);
            $match = new Matcher(
                $text, $result->start, $result->end, [$result], captured: $captured, name: $this->name);
            return Option::some($match);
        }

        return Option::none();
    }

    function named($text, $start)
    {
        $opt = $this->patterns[0]($text, $start);
        if ($opt->isSome()) {
            $result = $opt->value();
            $match = new Matcher(
                $text, $result->start, $result->end, [$result], name: $this->name);
            return Option::some($match);
        }

        return Option::none();
    }

}

