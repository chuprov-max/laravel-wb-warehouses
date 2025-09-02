<?php

namespace App\Helpers;

class StringHelper
{

    public static function escapeMarkdown(string $text): string
    {
        $escape = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        foreach ($escape as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        return $text;
    }
}
