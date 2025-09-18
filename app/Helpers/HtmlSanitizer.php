<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Sanitize HTML content while preserving rich formatting from Outlook/Word
     * 
     * @param string $html
     * @return string
     */
    public static function sanitizeRichContent($html)
    {
        if (empty($html)) {
            return '';
        }

        // Check if content is plain text (no HTML tags) and convert newlines to breaks
        if (!preg_match('/<[^>]+>/', $html)) {
            // This is plain text, convert newlines to HTML breaks
            $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
            $html = nl2br($html);
        } else {
            // This is HTML content, but we still need to handle newlines that aren't already in tags
            // Convert standalone newlines (not within tags) to <br> tags
            $html = preg_replace('/(?<=>)\s*\n+\s*(?=<)/', '<br />', $html);
            $html = preg_replace('/(?<=>)\s*\n+\s*(?=[^<])/', '<br />', $html);
            $html = preg_replace('/(?<=[^>])\s*\n+\s*(?=<)/', '<br />', $html);
            $html = preg_replace('/(?<=[^>])\s*\n+\s*(?=[^<])/', '<br />', $html);
        }

        // Comprehensive list of allowed tags for rich text formatting
        $allowedTags = [
            // Basic formatting
            'p', 'br', 'div', 'span',
            
            // Text styling
            'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del', 'ins', 'mark', 'small', 'big',
            'sup', 'sub', 'tt', 'code', 'kbd', 'samp', 'var',
            
            // Headers
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            
            // Lists
            'ul', 'ol', 'li', 'dl', 'dt', 'dd',
            
            // Tables
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'colgroup', 'col',
            
            // Links and media
            'a', 'img',
            
            // Quotes and blocks
            'blockquote', 'cite', 'q', 'pre',
            
            // Horizontal rule
            'hr',
            
            // Outlook/Word specific elements
            'font', 'center', 'nobr', 'wbr'
        ];

        // Comprehensive list of allowed attributes
        $allowedAttributes = [
            // Style attributes for colors, fonts, etc.
            'style',
            
            // Common attributes
            'class', 'id', 'title', 'dir', 'lang',
            
            // Link attributes
            'href', 'target', 'rel',
            
            // Image attributes
            'src', 'alt', 'width', 'height',
            
            // Table attributes
            'colspan', 'rowspan', 'cellpadding', 'cellspacing', 'border',
            'align', 'valign', 'bgcolor',
            
            // Font attributes (for Outlook compatibility)
            'color', 'face', 'size',
            
            // List attributes
            'type', 'start',
            
            // General layout attributes
            'width', 'height', 'align', 'valign'
        ];

        // Build the allowed tags string for strip_tags
        $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
        
        // First pass: Remove dangerous tags but keep formatting
        $sanitized = strip_tags($html, $allowedTagsString);
        
        // Remove dangerous attributes while preserving safe ones
        $sanitized = self::sanitizeAttributes($sanitized, $allowedAttributes);
        
        // Clean up common Outlook artifacts
        $sanitized = self::cleanOutlookArtifacts($sanitized);
        
        // Preserve line breaks
        return $sanitized;
    }

    /**
     * Sanitize HTML attributes while preserving safe formatting attributes
     * 
     * @param string $html
     * @param array $allowedAttributes
     * @return string
     */
    private static function sanitizeAttributes($html, $allowedAttributes)
    {
        // Remove dangerous event handlers and scripts
        $dangerousPatterns = [
            '/\s*on\w+\s*=\s*["\'][^"\']*["\']/',  // onclick, onload, etc.
            '/\s*javascript\s*:/i',                 // javascript: URLs
            '/\s*vbscript\s*:/i',                   // vbscript: URLs
            '/\s*data\s*:/i',                       // data: URLs (can be dangerous)
            '/\s*expression\s*\(/i',                // CSS expressions
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }
        
        return $html;
    }

    /**
     * Clean up common Outlook/Word artifacts while preserving formatting
     * 
     * @param string $html
     * @return string
     */
    private static function cleanOutlookArtifacts($html)
    {
        // Remove Outlook conditional comments but preserve content
        $html = preg_replace('/<!--\[if[^>]*>/', '', $html);
        $html = preg_replace('/<!\[endif\]-->/', '', $html);
        
        // Remove empty paragraphs that Outlook often creates
        $html = preg_replace('/<p[^>]*>\s*<\/p>/', '', $html);
        
        // Clean up excessive whitespace but preserve intentional spacing
        $html = preg_replace('/\s{3,}/', ' ', $html);
        
        // Remove Word's namespace declarations but keep the tags
        $html = preg_replace('/\s*xmlns:[^=]*="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*o:[^=]*="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*w:[^=]*="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*m:[^=]*="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*v:[^=]*="[^"]*"/i', '', $html);
        
        return $html;
    }

    /**
     * Quick sanitize for basic content (backwards compatibility)
     * 
     * @param string $html
     * @return string
     */
    public static function sanitizeBasic($html)
    {
        $basicTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6><blockquote><pre><code><hr><img><sup><sub><strike><del><ins><mark><small>';
        return strip_tags($html, $basicTags);
    }
}

