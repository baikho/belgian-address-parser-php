# belgian-address-parser-php

[![Latest Stable Version](https://poser.pugx.org/baikho/belgian-address-parser-php/v/stable)](https://packagist.org/packages/baikho/belgian-address-parser-php)
[![Total Downloads](https://poser.pugx.org/baikho/belgian-address-parser-php/downloads)](https://packagist.org/packages/baikho/belgian-address-parser-php)
[![License](https://poser.pugx.org/baikho/belgian-address-parser-php/license)](https://packagist.org/packages/baikho/belgian-address-parser-php)

A Belgian address parser library in PHP

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
