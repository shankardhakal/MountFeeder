<?php

namespace App\ParseRules;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ParserExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array  // ✅ Added ": array" to fix error
    {
        return [
            ExpressionFunction::fromPhp('App\ParseRules\contains', 'contains'),
            ExpressionFunction::fromPhp('App\ParseRules\notContains', 'notContains'),
            ExpressionFunction::fromPhp('App\ParseRules\equals', 'equals'),
            ExpressionFunction::fromPhp('App\ParseRules\notEquals', 'notEquals'),
            ExpressionFunction::fromPhp('App\ParseRules\equalsTo', 'equalsTo'),
        ];
    }
}

/**
 * @param  string  $string
 * @param  string  $contains
 * @return bool
 */
function contains(string $string, string $contains): bool
{
    $containsStringsAnd = array_filter(array_map('trim', explode('AND', $contains)));
    $containsStringsOr = array_filter(array_map('trim', explode('OR', $contains)));

    if (count($containsStringsAnd) > 1) {
        foreach ($containsStringsAnd as $andString) {
            if (stripos($string, $andString) === false) {
                return false;
            }
        }
        return true;
    }

    if (count($containsStringsOr) > 1) {
        foreach ($containsStringsOr as $orString) {
            if (stripos($string, $orString) !== false) {
                return true;
            }
        }
        return false;
    }

    return stripos($string, $contains) !== false;
}

/**
 * @param  string  $string
 * @param  string  $contains
 * @return bool
 */
function notContains(string $string, string $contains): bool
{
    return !contains($string, $contains);
}

/**
 * @param  string  $mainString
 * @param  string  $compareString
 * @return bool
 */
function equals(string $mainString, string $compareString): bool
{
    return $mainString === $compareString;
}

/**
 * @param  string  $mainString
 * @param  string  $compareString
 * @return bool
 */
function equalsTo(string $mainString, string $compareString): bool
{
    return $mainString === $compareString;
}

/**
 * @param  string  $mainString
 * @param  string  $compareString
 * @return bool
 */
function notEquals(string $mainString, string $compareString): bool
{
    return !equals($mainString, $compareString);
}
