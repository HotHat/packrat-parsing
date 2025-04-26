<?php declare(strict_types=1);

namespace Packrat;

class Pattern
{
    private array $cache = [];
    private string $cacheStr = '';
    public function __construct(
        public readonly string $type,
        public readonly array  $patterns,
        public readonly string $name = '',
        public  ?Grammar $grammar = null,
    )
    {
    }

    public function __invoke(string $text, int $start): Option
    {
        return $this->match($text, $start);
    }

    public function match($text, $start)
    {
        if ($this->cacheStr !== $text) {
            $this->cacheStr = $text;
            $this->cache = [];
        }
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
        return Option::some(new Matcher($text, $start, $pos, name: $this->name));
    }

    function oneOf($text, $start) {
        foreach ($this->patterns as $pattern) {
            $opt = $pattern->match($text, $start);
            if ($opt->isSome()) {
                // $result = $opt->value();
                return Option::some(new Matcher($text, $start, $opt->value()->end, name: $this->name));
            }
        }
        return Option::none();
    }

    function repeat($text, $start)
    {
        $pattern = $this->patterns[0];
        $firstMatch = false;
        $pos = 0;

        // repeat test
        $match = $pattern->match($text, $start);
        while ($match->isSome()) {
            $pos = $match->value()->end;
            $firstMatch = true;
            $match = $pattern->match($text, $pos);
        }

        if ($firstMatch) {
            return Option::some(new Matcher($text, $start, $pos, name: $this->name));
        }

        // zero
        return Option::some(new Matcher($text, $start, $start, name: $this->name));
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
                $text, $result->start, $result->end, captured: $captured, name: $this->name);
            if ($this->grammar) {
                $this->grammar->addMatch($match);
            }
            return Option::some($match);
        }

        return Option::none();
    }

    function named($text, $start)
    {
        if ($this->grammar) {
            $this->grammar->pushNamed();
        }
        $opt = $this->patterns[0]($text, $start);
        if ($opt->isSome()) {
            $result = $opt->value();
            $match = new Matcher(
                $text,
                $result->start,
                $result->end,
                [$result],
                name: $this->name
            );
            if ($this->grammar) {
                $this->grammar->addNamedMatch($match);
            }
            return Option::some($match);
        }

        return Option::none();
    }

}

