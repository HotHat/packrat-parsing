<?php

namespace Packrat;

class Grammar
{
    private array $rules;
    private array $namedStack = [[]];

    public function __set($rule, $pattern)
    {
        $newPattern = named($rule, $pattern);
        $newPattern->grammar = $this;
        $this->rules[$rule] = $newPattern;
    }

    public function __get($rule)
    {
        $pattern = $this->rules[$rule] ?? null;
        if (empty($pattern)) {
            throw new \Exception("Not pattern");
        }
        return $pattern;
    }

    public function pushNamed() {
        $this->namedStack[] = [];
    }

    public function popNamed() {
        return array_pop($this->namedStack);
    }

    public function addMatch(Matcher $match)
    {
        $size = count($this->namedStack);
        $this->namedStack[$size-1][] = $match;
    }

    public function addNamedMatch(Matcher $match)
    {
        $top = $this->popNamed();
        $match->children = $top;
        $this->addMatch($match);
    }

}