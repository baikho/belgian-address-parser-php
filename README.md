# belgian-address-parser-php
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
$address = "
Andreas Vesaliusstraat 47
3000 Leuven
België";

$parsed = $parser->parse($address);

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
