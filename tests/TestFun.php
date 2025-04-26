<?php declare(strict_types=1);

namespace Tests;

use Packrat\Grammar;
use Packrat\Pattern;
use PHPUnit\Framework\TestCase;

use function Packrat\{capture, literal, chain, named, oneOf, repeat, not, anyChar};

class TestFun extends TestCase
{
    public function testLiteral() {

        $HelloPattern = literal("hello");
        $m1 = $HelloPattern->match("hello world", 0); // -> new Match(text, 0, 5))
        var_dump($m1);
        $this->assertTrue($m1->isSome());
        $m2 = $HelloPattern->match("goodbye", 0); // -> no match (undefined)
        var_dump($m2);
        $this->assertTrue($m2->isNone());
        $m3 = $HelloPattern->match("hello world", 5); // -> no match at index 5
        var_dump($m3);
        $this->assertTrue($m3->isNone());
    }

    public function testLiteral2() {
        $HelloPattern = new Pattern("literal", ["hello"]);
        var_dump($HelloPattern->match("hello world", 0));; // -> new Match(text, 0, 5))
        var_dump($HelloPattern->match("goodbye", 0)); // -> no match (undefined)
        var_dump($HelloPattern->match("hello world", 5)); // -> no match at index 5
    }

    public function testChain() {
        $HelloWorldPattern = chain(
            literal("hello"),
            literal(" "),
            literal("world")
        );
        $m1 = $HelloWorldPattern("hello world", 0); // -> new Match(text, 0, 11))
        var_dump($m1);
        $this->assertTrue($m1->isSome());
        $m2 = $HelloWorldPattern("goodbye", 0); // -> undefined
        var_dump($m2);
        $this->assertTrue($m2->isNone());

    }

    public function testChain2() {

    }

    public function testOneOf()
    {
        $oneOfPattern =  oneOf(literal("a"), literal(""));
        $m1 = $oneOfPattern("a", 0); // -> new Match(text, 0, 11))
        var_dump($m1);
        $this->assertTrue($m1->isSome());
        $m2 = $oneOfPattern("b", 0); // -> new Match(text, 0, 11))
        var_dump($m2);
        $this->assertTrue($m2->isSome());
        $m3 = $oneOfPattern("c", 0); // -> new Match(text, 0, 11))
        var_dump($m3);
        $this->assertTrue($m3->isSome());
    }

    public function testOneOf2()
    {
        $oneOfPattern =  oneOf(literal("a"), literal("b"));
        $m1 = $oneOfPattern("a", 0); // -> new Match(text, 0, 11))
        var_dump($m1);
        $this->assertTrue($m1->isSome());
        $m2 = $oneOfPattern("b", 0); // -> new Match(text, 0, 11))
        var_dump($m2);
        $this->assertTrue($m2->isSome());
        $m3 = $oneOfPattern("c", 0); // -> new Match(text, 0, 11))
        var_dump($m3);
        $this->assertTrue($m3->isNone());
    }


    public function testRepeat()
    {
        $RepeatPattern = repeat(literal("a"));
        $m1 = $RepeatPattern("aaa", 0); // -> new Match(text, 0, 11))
        var_dump($m1);
        $this->assertTrue($m1->isSome());
        $m2 = $RepeatPattern("bbaaa", 3); // -> new Match(text, 0, 11))
        var_dump($m2);
        $this->assertTrue($m2->isSome());
        $m3 = $RepeatPattern("hello sky", 0); // -> undefined
        var_dump($m3);
        $this->assertTrue($m3->isSome());
    }

    public function testCSV()
    {
        // Equivalent to the regex: [^,\n]*
        $CSVItem = repeat(chain(not(literal(",")), not(literal("\n")), anyChar()));
        // var_dump($CSVItem("abc", 0));
        // Sorta like: CSVItem ("," CSVItem)*
        $CSVLine = chain($CSVItem, repeat(chain(literal(","), $CSVItem)));
        // Sorta like: CSVLine ("\n" CSVLine)*
        $CSVFile = chain($CSVLine, repeat(chain(literal("\n"), $CSVLine)));

        // It works (but only for validation)
        $myFile = "x,y,z\na,b,c";
        $m1 = $CSVFile($myFile, 0); // new Match(...)
        var_dump($m1);
        $this->assertTrue($m1->isSome());
    }

    public function testCSV2()
    {
        $CSVItem = named('Item',
            capture(
                repeat(chain(not(literal(",")), not(literal("\n")), anyChar()))
            )
        );
        // $t = $CSVItem("1234", 0);
        // var_dump($t);
        // $rp = repeat(chain(literal(","), $CSVItem));

        // $p = $rp(",2,3,4", 0);
        // var_dump($p->value());
        // Sorta like: CSVItem ("," CSVItem)*
        $CSVLine = named('Line',
            chain($CSVItem, repeat(chain(literal(","), $CSVItem)))
        );

        // Sorta like: CSVLine ("\n" CSVLine)*
        $CSVFile = named('File',
            chain($CSVLine, repeat(chain(literal("\n"), $CSVLine)))
        );

        // It works (but only for validation)
        $myFile = "1,2,3\n4,5,6";
        $m1 = $CSVFile($myFile, 0); // new Match(...)
        var_dump($m1);
        // $this->assertTrue($m1->isSome());

    }

    public function testGrammar() {
        $grammar = new Grammar();
        $grammar->AnyChar = named("AnyChar", literal("."));
        $match = $grammar->AnyChar->match(".", 0);
        var_dump($match);
    }

    public function testCSV3()
    {
        $grammar = new Grammar();
        $grammar->Item = named('Item',
            capture(
                repeat(chain(not(literal(",")), not(literal("\n")), anyChar()))
            )
        );
        $grammar->Line = named('Line',
            chain($grammar->Item, repeat(chain(capture(literal(",")), $grammar->Item)))
        );

        $grammar->File = named('File',
            chain($grammar->Line, repeat(chain(literal("\n"), $grammar->Line)))
        );
        $myFile = "1,2,3\n4,5,6";
        $m1 = $grammar->File->match($myFile, 0); // new Match(...)
         var_dump($m1);
    }

}

//
// type2




// $AddressWorldPattern = Packrat::chain(
//     Packrat::oneOf(Packrat::literal("hello"), Packrat::literal("goodbye")),
//     Packrat::literal(" "), Packrat::literal("world")
// );
// var_dump($AddressWorldPattern("hello world", 0)); // -> new Match(text, 0, 11))
// var_dump($AddressWorldPattern("goodbye world", 0)); // -> new Match(text, 0, 13)
// var_dump($AddressWorldPattern("hello sky", 0)); // -> undefined



// type 2

