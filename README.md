# Belgian Address Parser PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/baikho/belgian-address-parser-php.svg)](https://packagist.org/packages/baikho/belgian-address-parser-php)
[![Total Downloads](https://img.shields.io/packagist/dt/baikho/belgian-address-parser-php.svg)](https://packagist.org/packages/baikho/belgian-address-parser-php)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/baikho/belgian-address-parser-php.svg)](https://github.com/baikho/belgian-address-parser-php/issues)
[![GitHub stars](https://img.shields.io/github/stars/baikho/belgian-address-parser-php.svg)](https://github.com/baikho/belgian-address-parser-php/stargazers)

A Belgian address parser library in PHP.

## Requirements

- PHP 8.1 or higher

## Installation

You can install the package via composer:

```bash
composer require baikho/belgian-address-parser-php
```
## Usage

```php
// Create a parser instance
$parser = new \Baikho\BelgianAddressParser\Parser();

// Parse an address
$parsed = $parser->parse('Andreas Vesaliusstraat 47, 3000 Leuven, België');

// Output the parsed components
print_r($parsed);
Array
(
    [recipient] => 
    [street] => Andreas Vesaliusstraat
    [number] => 47
    [box] => 
    [postal_code] => 3000
    [city] => Leuven
    [country] => BELGIUM
)

// Validate the address
$validation = $parser->validate($parsed);
if ($validation['valid']) {
    echo "Address is valid!\n";
} else {
    echo "Address has issues: " . implode(', ', $validation['errors']) . "\n";
}

// Format the address back to string
$formatted = $parser->format($parsed);
echo $formatted;
```
