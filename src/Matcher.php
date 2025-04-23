<?php declare(strict_types=1);

namespace Packrat;
class Matcher
{
    public function __construct(
        public string $text,
        public int $start,
        public int $end,
        public array $children = [],
        public string $captured = '',
        public string $name = '',
    )
    {

    }

    public function setCaptured($captured)
    {
        $this->captured = $captured;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}