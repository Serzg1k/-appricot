<?php

namespace App\Infrastructure\Movies\Support;

trait NormalizesTitles
{
    /**
     * Normalize arbitrary vendor payload into string[] of titles.
     * - Unwraps ['titles'=>...] or (object)->titles
     * - Traverses nested arrays/objects
     * - Collects strings only when they are list items
     * - From associative arrays collects only 'title'|'name' (case-insensitive)
     */
    protected function normalizeTitles($raw): array
    {
        // unwrap envelopes
        if (is_object($raw) && isset($raw->titles)) {
            $raw = $raw->titles;
        } elseif (is_array($raw) && array_key_exists('titles', $raw)) {
            $raw = $raw['titles'];
        }

        $out = [];

        $isList = function (array $a): bool {
            // true if keys are 0..n-1
            $i = 0;
            foreach ($a as $k => $_) {
                if ($k !== $i) return false;
                $i++;
            }
            return true;
        };

        $extract = function ($node, bool $inList = false) use (&$extract, &$out, $isList) {
            if (is_string($node)) {
                // collect only if this string is an element of a list (e.g., BAZ: ['titles'=>['str','str']])
                if ($inList) {
                    $t = trim($node);
                    if ($t !== '') $out[] = $t;
                }
                return;
            }

            if (is_object($node)) {
                $node = (array) $node;
            }

            if (is_array($node)) {
                // associative: try to extract 'title'|'name' only
                $hasNumericKeys = $isList($node);

                if (!$hasNumericKeys) {
                    foreach (['title', 'name', 'Title', 'Name'] as $k) {
                        if (array_key_exists($k, $node) && is_scalar($node[$k])) {
                            $t = trim((string) $node[$k]);
                            if ($t !== '') $out[] = $t;
                        }
                    }
                    foreach ($node as $v) {
                        $extract($v, false);
                    }
                    return;
                }

                foreach ($node as $v) {
                    $extract($v, true);
                }
            }
        };

        $extract($raw, is_array($raw) && $isList($raw));

        return array_values(array_unique($out));
    }
}
