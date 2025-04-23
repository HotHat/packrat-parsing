<?php declare(strict_types=1);

namespace Packrat;
final class Option
{

    private bool $isSome;
    private mixed $value;
    private function __construct($value, bool $isSome) {
        $this->isSome = $isSome;
        $this->value = $value;
    }

    public static  function some($value): Option
    {
        return new Option($value, true);
    }

    public static function none(): Option
    {
        return new Option(false, false);
    }

    public function isSome(): bool {
        return $this->isSome;
    }

    public function isNone(): bool
    {
        return !$this->isSome;
    }

    public function value() {
        return $this->value;
    }

}