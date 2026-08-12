<?php

namespace App\Services;

class RichTextSanitizer
{
    /**
     * Tags the rich text editor is allowed to produce.
     */
    protected const ALLOWED_TAGS = '<p><br><span><strong><b><em><i><u><s><ul><ol><li><a><h1><h2><h3><blockquote><pre><code><hr>';

    /**
     * Clean editor supplied HTML so it is safe to render back with {!! !!}.
     *
     * Returns null when the markup carries no actual content, which keeps
     * empty editors (`<p><br></p>`) from being stored as a value.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $clean = strip_tags($html, self::ALLOWED_TAGS);

        // Inline event handlers, e.g. onclick="..."
        $clean = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);

        // Script-bearing URLs on the tags we do allow.
        $clean = preg_replace('/\s(href|src)\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*\2/i', '', $clean);

        // CSS that can pull in remote content or execute.
        $clean = preg_replace('/\sstyle\s*=\s*("|\')[^"\']*(url\(|expression\()[^"\']*\1/i', '', $clean);

        if (trim(strip_tags($clean, '<img>')) === '' && ! str_contains($clean, '<hr')) {
            return null;
        }

        return $clean;
    }
}
