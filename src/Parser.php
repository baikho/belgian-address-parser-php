<?php

namespace Baikho\BelgianAddressParser;

/**
 * BelgianAddressParser - A class to parse and format Belgian addresses
 *
 * @package Baikho\BelgianAddressParser
 */
class Parser
{
    /**
     * Parse a Belgian address string into structured components.
     *
     * @param string $address_string The full address string to parse
     * @return array An array containing the parsed address components
     */
    public function parse($address_string)
    {
        // Clean up the input string
        $address_string = trim($address_string);

        // Split the address into lines
        $lines = array_filter(array_map('trim', explode("\n", $address_string)));
        $lines = array_values($lines); // Re-index array

        $result = [
            'recipient' => '',
            'street' => '',
            'number' => '',
            'box' => '',
            'postal_code' => '',
            'city' => '',
            'country' => 'BELGIUM'
        ];

        // Determine if the address contains "Belgium" in any form and remove it
        $last_line = isset($lines[count($lines) - 1]) ? strtoupper($lines[count($lines) - 1]) : '';
        $country_variants = ['BELGIUM', 'BELGIQUE', 'BELGIË', 'BELGIEN'];

        foreach ($country_variants as $country) {
            if (strpos($last_line, $country) !== false) {
                $result['country'] = array_pop($lines);
                break;
            }
        }

        // Process by number of lines
        if (count($lines) == 1) {
            // Only one line - try to extract everything from it
            $full_address = $lines[0];
            $this->parseSingleLine($full_address, $result);
        } elseif (count($lines) >= 2) {
            // First line is usually the recipient
            $result['recipient'] = $lines[0];

            // Last line usually contains postal code and city
            if (preg_match('/(\d{4})\s+(.+)/', $lines[count($lines) - 1], $matches)) {
                $result['postal_code'] = $matches[1];
                $result['city'] = $matches[2];

                // If we have more than 2 lines, the second-to-last is the street
                if (count($lines) > 2) {
                    $this->parseStreetAddress($lines[count($lines) - 2], $result);
                } elseif (count($lines) == 2) {
                    // If we only have 2 lines, try to extract street from the recipient line
                    // This is a fallback for addresses without explicit recipient
                    $this->parseStreetAddress($lines[0], $result);
                    // If we found a street in the first line, clear it from recipient
                    if (!empty($result['street'])) {
                        $result['recipient'] = '';
                    }
                }
            } else {
                // No postal code in the last line, assume it's the city
                $result['city'] = $lines[count($lines) - 1];

                // Try to find street in the line before
                if (count($lines) > 2) {
                    $this->parseStreetAddress($lines[count($lines) - 2], $result);
                } elseif (count($lines) == 2) {
                    $this->parseStreetAddress($lines[0], $result);
                    // If we found a street in the first line, clear it from recipient
                    if (!empty($result['street'])) {
                        $result['recipient'] = '';
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Parse a single line address into components
     *
     * @param string $address_line The single line address
     * @param array &$result Reference to the result array
     */
    private function parseSingleLine($address_line, &$result)
    {
        // Check if it's a comma-separated address
        if (strpos($address_line, ',') !== false) {
            $parts = array_map('trim', explode(',', $address_line));

            // Look for postal code and city in each part
            foreach ($parts as $index => $part) {
                if (preg_match('/(\d{4})\s+(.+)/', $part, $matches)) {
                    $result['postal_code'] = $matches[1];
                    $result['city'] = $matches[2];
                    unset($parts[$index]);
                    break;
                }
            }

            // First remaining part is likely the street address
            if (!empty($parts)) {
                $this->parseStreetAddress(reset($parts), $result);
            }
        } else {
            // Try to extract postal code and city
            if (preg_match('/(\d{4})\s+(.+)/', $address_line, $matches)) {
                $result['postal_code'] = $matches[1];
                $result['city'] = $matches[2];

                // Remove the postal code and city from the line
                $address_line = trim(str_replace($matches[0], '', $address_line));
                if (substr($address_line, -1) === ',') {
                    $address_line = trim(substr($address_line, 0, -1));
                }
            }

            // Try to extract street and number
            $this->parseStreetAddress($address_line, $result);
        }
    }

    /**
     * Parse the street address line to extract street name, number and box
     *
     * @param string $street_line The street line to parse
     * @param array &$result Reference to the result array
     */
    private function parseStreetAddress($street_line, &$result)
    {
        // Pattern for street number (possibly with box)
        // In Belgium, box numbers can be indicated as "16/2", "16 bus 2", "16 box 2", "16 bte 2", etc.
        $number_pattern = '/(\d+)(?:\s*(?:\/|bus|bte|box|b)\s*(\w+))?$/';

        // Find the last number in the string (likely the street number)
        if (preg_match($number_pattern, $street_line, $matches)) {
            $result['number'] = $matches[1];
            if (isset($matches[2])) {
                $result['box'] = $matches[2];
            }

            // Street is everything before the number
            $street_end_pos = strrpos($street_line, $matches[0]);
            if ($street_end_pos > 0) {
                $result['street'] = trim(substr($street_line, 0, $street_end_pos));
                if (substr($result['street'], -1) === ',') {
                    $result['street'] = trim(substr($result['street'], 0, -1));
                }
            }
        } else {
            // No number found, assume the whole line is the street
            $result['street'] = $street_line;
        }
    }

    /**
     * Format a structured Belgian address back to string format
     *
     * @param array $address_dict Array with parsed address components
     * @return string Formatted address string
     */
    public function format($address_dict)
    {
        $lines = [];

        if (!empty($address_dict['recipient'])) {
            $lines[] = $address_dict['recipient'];
        }

        // Format street address
        $street_address = $address_dict['street'] ?? '';
        if (!empty($address_dict['number'])) {
            $street_address .= ' ' . $address_dict['number'];
            if (!empty($address_dict['box'])) {
                $street_address .= ' bus ' . $address_dict['box'];
            }
        }

        if (!empty($street_address)) {
            $lines[] = $street_address;
        }

        // Format postal code and city
        $postal_city = '';
        if (!empty($address_dict['postal_code'])) {
            $postal_city .= $address_dict['postal_code'] . ' ';
        }
        if (!empty($address_dict['city'])) {
            $postal_city .= $address_dict['city'];
        }

        if (!empty($postal_city)) {
            $lines[] = $postal_city;
        }

        if (!empty($address_dict['country'])) {
            $lines[] = $address_dict['country'];
        }

        return implode("\n", $lines);
    }

    /**
     * Validate if an address is a properly formatted Belgian address
     *
     * @param array $address_dict The address components to validate
     * @return array An array with 'valid' (boolean) and 'errors' (array of error messages)
     */
    public function validate($address_dict)
    {
        $errors = [];

        // Check postal code format (4 digits)
        if (empty($address_dict['postal_code'])) {
            $errors[] = 'Missing postal code';
        } elseif (!preg_match('/^\d{4}$/', $address_dict['postal_code'])) {
            $errors[] = 'Invalid postal code format. Belgian postal codes must be 4 digits';
        }

        // Check for required fields
        if (empty($address_dict['street'])) {
            $errors[] = 'Missing street name';
        }

        if (empty($address_dict['city'])) {
            $errors[] = 'Missing city';
        }

        // House number is typically required in Belgium
        if (empty($address_dict['number'])) {
            $errors[] = 'Missing house number';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}