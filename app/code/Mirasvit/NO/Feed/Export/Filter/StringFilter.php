<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Feed\Export\Filter;

class StringFilter
{
    /**
     * Format csv column value
     *
     * @param string $input
     * @param string $delimiter
     * @param string $enclosure
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function csv($input, $delimiter = ',', $enclosure = '')
    {
        if (is_array($input)) {
            $input = implode(', ', $input);
        }

        if (is_scalar($input)) {
            $input      = (string)$input;
            $escapeChar = '\\';
            $str        = $enclosure;
            $escaped    = 0;
            $len        = strlen($input);
            for ($i = 0; $i < $len; $i++) {
                if ($input[$i] == $escapeChar) {
                    $escaped = 1;
                } elseif (!$escaped && $input[$i] == $enclosure) {
                    $str .= $enclosure;
                } else {
                    $escaped = 0;
                }
                $str .= $input[$i];
            }
            $str .= $enclosure;

            $input = $str;
        }

        return (string)$input;
    }

    /**
     * Replace
     * | Replaces all occurrences of a string with a substring
     *
     * @param string $input
     * @param string $search
     * @param string $replace
     *
     * @return string
     */
    public function replace($input, $search, $replace): string
    {
        return is_string($input) ? str_replace($search, $replace, $input) : (string)$input;
    }

    /**
     * Lowercase
     * | Converts a string into lowercase
     *
     * @param string $input
     *
     * @return string
     */
    public function lowercase($input): string
    {
        return is_string($input) ? mb_strtolower($input) : (string)$input;
    }

    /**
     * Uppercase
     * | Converts a string into uppercase
     *
     * @param string $input
     *
     * @return string
     */
    public function uppercase($input): string
    {
        return is_string($input) ? mb_strtoupper($input) : (string)$input;
    }

    /**
     * Append
     * | Appends characters to a string
     *
     * @param string $input
     * @param string $suffix
     *
     * @return string
     */
    public function append($input, $suffix): string
    {
        return is_string($input) ? $input . $suffix : (string)$input;
    }

    /**
     * Prepend
     * | Prepends characters to a string
     *
     * @param string $input
     * @param string $prefix
     *
     * @return string
     */
    public function prepend($input, $prefix): string
    {
        return is_string($input) ? $prefix . $input : (string)$input;
    }

    /**
     * Capitalize
     * | Capitalizes the first word in a string
     *
     * @param string $input
     *
     * @return string
     */
    public function capitalize($input): string
    {
        return is_string($input) ? ucfirst($input) : (string)$input;
    }

    /**
     * Escape
     * | Converts special characters to HTML entities
     *
     * @param string $input
     *
     * @return string
     */
    public function escape($input): string
    {
        return is_string($input) ? htmlspecialchars($input) : (string)$input;
    }

    /**
     * HTML Entity Decode
     * | Convert HTML entities to their corresponding characters
     *
     * @param string $input
     *
     * @return string
     */
    public static function html_entity_decode($input): string
    {
        return is_string($input) ? html_entity_decode($input) : (string)$input;
    }

    /**
     * Newline to <br>
     * | Inserts a <br > linebreak HTML tag in front of each line break in a string
     *
     * @param string $input
     *
     * @return string
     */
    public function nl2br($input): string
    {
        return is_string($input) ? nl2br($input) : (string)$input;
    }

    /**
     * Remove
     * | Removes all occurrences of a substring from a string
     *
     * @param string $input
     * @param string $text
     *
     * @return string
     */
    public function remove($input, $text): string
    {
        return is_string($input) ? str_replace($text, '', $input) : (string)$input;
    }

    /**
     * Strip HTML tags
     * | Strips all HTML tags from a string
     *
     * @param string $input
     *
     * @return string
     */
    public function stripHtml($input): string
    {
        return is_string($input) ? strip_tags($input) : (string)$input;
    }

    /**
     * Strip style tag
     * | Strips the <style> tag with its content
     *
     * @param string $input
     *
     * @return string
     */
    public function stripStyleTag($input): string
    {
        return is_string($input) ? preg_replace('/<style[^>]*>.*?<\/style>/s', '', $input) : (string)$input;
    }

    /**
     * Strip new lines
     * | Strip all newlines (\n, \r) from string
     *
     * @param string $input
     *
     * @return string
     */
    public static function strip_newlines($input): string
    {
        return is_string($input) ? str_replace([
            "\n", "\r",
        ], '', $input) : (string)$input;
    }

    /**
     * Replace new lines
     * | Replace each newline (\n) with html break
     *
     * @param string $input
     *
     * @return string
     */
    public static function newline_to_br($input): string
    {
        return is_string($input) ? str_replace([
            "\n", "\r",
        ], '<br />', $input) : (string)$input;
    }

    /**
     * Truncate
     * | Truncates a string down to 'x' characters
     *
     * @param string $input
     * @param int    $len
     *
     * @return string
     */
    public function truncate($input, $len): string
    {
        return is_string($input) ? mb_substr($input, 0, intval($len)) : (string)$input;
    }

    /**
     * Truncate words
     * | Truncate string down to x words
     *
     * @param string $input
     * @param int    $words
     *
     * @return string
     */
    public static function truncatewords($input, $words = 3): ?string
    {
        if (is_string($input)) {
            $wordlist = explode(" ", $input);

            if (count($wordlist) > $words) {
                return implode(" ", array_slice($wordlist, 0, intval($words)));
            }
        }

        return (string)$input;
    }

    /**
     * Split
     * | Split input string into an array of substrings separated by given pattern
     *
     * @param string $input
     * @param string $pattern
     *
     * @return array
     */
    public static function split($input, $pattern): array
    {
        return is_string($input) ? explode($pattern, $input) : [$input];
    }

    /**
     * Plain format
     * | Converts any text to plain
     *
     * @param string $input
     *
     * @return string
     */
    public function plain($input): string
    {
        if (empty($input)) {
            return (string)$input;
        }
        if (!is_string($input)) {
            return (string)$input;
        }
        // 194 -> 32
        $input = str_replace(' ', ' ', $input);

        $input = strip_tags($input);

        $input = str_replace('\\\'', '\'', $input);
        $input = preg_replace('/\s+/', ' ', $input);

        //{{block type="cms/block" block_id="product-3-in-1" template="cms/content.phtml"}}
        $input = preg_replace('/({{.*}})/is', '', $input);

        $input = trim($input);

        return (string)$input;
    }

    /**
     * @param string $input
     * @param string $eval
     *
     * @return string
     * @return string
     */
    public function php($input, $eval): string
    {
        return "";
    }

    /**
     * If Empty
     * | Sets output if attribute has no value
     *
     * @param string $input
     * @param string $default
     *
     * @return string
     */
    public function ifEmpty($input, $default = ''): string
    {
        if (!$input || $input == '') {
            return (string)$default;
        }

        return (string)$input;
    }

    /**
     * Format date
     * | Converts a string to specified date-time format
     *
     * @param string $input
     * @param string $format
     *
     * @return string
     */
    public function dateFormat($input, $format = 'd.m.Y'): string
    {
        if ($input) {
            if (is_numeric($input)) {
                return date($format, $input);
            } else {
                return date($format, strtotime($input));
            }
        }

        return '';
    }

    /**
     * Rtrim
     * | Strip whitespace (or other characters) from the end of a string
     *
     * @param string $input
     * @param string $mask
     *
     * @return string
     */
    public function rtrim($input, $mask = ' '): string
    {
        return rtrim((string)$input, $mask);
    }

    /**
     * JSON Encode
     *
     * @param string $input
     *
     * @return string
     */
    public function json($input): string
    {
        return json_encode((string)$input);
    }

    /**
     * Clean
     * | Remove all non-utf-8 characters from string
     *
     * @param string $input
     *
     * @return string $input
     */
    public function clean($input): string
    {
        $input = preg_replace('/[^(\x20-\x7F)]*/', '', (string)$input);
        $input = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[\x00-\x7F][\x80-\xBF]+' .
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '',
            $input
        );
        $input = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' .
            '|\xED[\xA0-\xBF][\x80-\xBF]/S', '', $input);

        return $input;
    }
}
