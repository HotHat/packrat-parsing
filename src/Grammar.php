<?php

namespace Packrat;

class Grammar
{
    private array $rules;
    private array $namedStack = [[]];

    public function __set($rule, $pattern)
    {
        // $newPattern = named($rule, $pattern);
        // $newPattern->grammar = $this;
        $this->markNamedAndCapture($pattern);
        $this->rules[$rule] = $pattern;
    }

    public function markNamedAndCapture($pattern)
    {
        if ($pattern->type == 'named' || $pattern->type == 'capture') {
            $pattern->grammar = $this;
        }

        foreach ($pattern->patterns as $child) {
            $this->markNamedAndCapture($child);
        }

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