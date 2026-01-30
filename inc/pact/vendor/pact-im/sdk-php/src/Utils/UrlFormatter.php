<?php

namespace Pact\Utils;

use Pact\Exception\InvalidArgumentException;

class UrlFormatter
{
    /**
     * Formats string using template by pasting ids
     * 
     * @param string Template
     * @param array List of ids to paste
     * @param array Additional parameters in uri
     * @return string
     */
    public static function format(string $template, array $ids = [], array $query = [])
    {
        foreach ($ids as $id) {
            if (null === $id || '' === trim($id)) {
                $msg = 'The resource ID cannot be null or whitespace.';

                throw new InvalidArgumentException($msg);
            }
        }
        $query = http_build_query($query);
        if (strlen($query)) {
            $query = "?${query}";
        }

        return sprintf($template, ...array_map('urlencode', $ids)) . $query;
    }
}
