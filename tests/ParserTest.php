<?php

namespace Baikho\BelgianAddressParser\Tests;

use Baikho\BelgianAddressParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testParsingMultiLineAddress()
    {
        $address = "Marie Dupont\nRue de la Loi 16\n1000 Brussels\nBELGIUM";
        $parsed = $this->parser->parse($address);

        $this->assertEquals('Marie Dupont', $parsed['recipient']);
        $this->assertEquals('Rue de la Loi', $parsed['street']);
        $this->assertEquals('16', $parsed['number']);
        $this->assertEquals('1000', $parsed['postal_code']);
        $this->assertEquals('Brussels', $parsed['city']);
        $this->assertEquals('BELGIUM', $parsed['country']);
    }

    public function testParsingAddressWithBox()
    {
        $address = "Company NV\nAvenue des Arts 56 bus 4A\n1000 Bruxelles\nBelgique";
        $parsed = $this->parser->parse($address);

        $this->assertEquals('Company NV', $parsed['recipient']);
        $this->assertEquals('Avenue des Arts', $parsed['street']);
        $this->assertEquals('56', $parsed['number']);
        $this->assertEquals('4A', $parsed['box']);
        $this->assertEquals('1000', $parsed['postal_code']);
        $this->assertEquals('Bruxelles', $parsed['city']);
        $this->assertEquals('Belgique', $parsed['country']);
    }

    public function testParsingSingleLineAddress()
    {
        $address = "Rue de la Loi 16, 1000 Brussels, Belgium";
        $parsed = $this->parser->parse($address);

        $this->assertEquals('Rue de la Loi', $parsed['street']);
        $this->assertEquals('16', $parsed['number']);
        $this->assertEquals('1000', $parsed['postal_code']);
        $this->assertEquals('Brussels', $parsed['city']);
        $this->assertEquals('Belgium', $parsed['country']);
    }

    public function testFormattingAddress()
    {
        $addressComponents = [
            'recipient' => 'Marie Dupont',
            'street' => 'Rue de la Loi',
            'number' => '16',
            'postal_code' => '1000',
            'city' => 'Brussels',
            'country' => 'BELGIUM'
        ];

        $formatted = $this->parser->format($addressComponents);
        $expected = "Marie Dupont\nRue de la Loi 16\n1000 Brussels\nBELGIUM";

        $this->assertEquals($expected, $formatted);
    }

    public function testValidation()
    {
        // Valid address
        $valid = [
            'street' => 'Rue de la Loi',
            'number' => '16',
            'postal_code' => '1000',
            'city' => 'Brussels'
        ];

        $validation = $this->parser->validate($valid);
        $this->assertTrue($validation['valid']);

        // Invalid address - missing postal code
        $invalid = [
            'street' => 'Rue de la Loi',
            'number' => '16',
            'city' => 'Brussels'
        ];

        $validation = $this->parser->validate($invalid);
        $this->assertFalse($validation['valid']);
        $this->assertContains('Missing postal code', $validation['errors']);
    }
}