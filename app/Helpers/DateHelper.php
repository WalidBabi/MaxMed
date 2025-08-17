<?php

if (!function_exists('formatDubaiDate')) {
    /**
     * Format a date to Dubai timezone
     */
    function formatDubaiDate($date, $format = 'M d, Y H:i')
    {
        if (!$date) {
            return null;
        }
        
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->setTimezone('Asia/Dubai')->format($format);
    }
}

if (!function_exists('formatDubaiDateForHumans')) {
    /**
     * Format a date to Dubai timezone for humans
     */
    function formatDubaiDateForHumans($date)
    {
        if (!$date) {
            return null;
        }
        
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->setTimezone('Asia/Dubai')->diffForHumans();
    }
}

if (!function_exists('nowDubai')) {
    /**
     * Get current time in Dubai timezone
     */
    function nowDubai($format = null)
    {
        $now = \Carbon\Carbon::now('Asia/Dubai');
        
        return $format ? $now->format($format) : $now;
    }
}

if (!function_exists('numberToWords')) {
    /**
     * Convert number to words with currency support
     */
    function numberToWords($number, $currency = 'AED')
    {
        if (!is_numeric($number)) {
            return '';
        }

        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        $wholePart = (int) $parts[0];
        $decimalPart = isset($parts[1]) ? (int) $parts[1] : 0;

        $words = '';
        
        if ($wholePart == 0) {
            $words = 'Zero';
        } else {
            $words = convertNumberToWords($wholePart);
        }

        // Add currency name
        $currencyWords = getCurrencyWords($currency, $wholePart);
        $words .= ' ' . $currencyWords['major'];

        // Add decimal part if exists
        if ($decimalPart > 0) {
            $words .= ' and ' . convertNumberToWords($decimalPart) . ' ' . $currencyWords['minor'];
        }

        return $words . ' Only';
    }
}

if (!function_exists('convertNumberToWords')) {
    /**
     * Convert a number to words (helper function)
     */
    function convertNumberToWords($number)
    {
        $ones = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen'
        ];

        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];

        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return $ones[intval($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . convertNumberToWords($number % 100) : '');
        } elseif ($number < 1000000) {
            return convertNumberToWords(intval($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . convertNumberToWords($number % 1000) : '');
        } elseif ($number < 1000000000) {
            return convertNumberToWords(intval($number / 1000000)) . ' Million' . ($number % 1000000 ? ' ' . convertNumberToWords($number % 1000000) : '');
        } else {
            return convertNumberToWords(intval($number / 1000000000)) . ' Billion' . ($number % 1000000000 ? ' ' . convertNumberToWords($number % 1000000000) : '');
        }
    }
}

if (!function_exists('getCurrencyWords')) {
    /**
     * Get currency words for major and minor units
     */
    function getCurrencyWords($currency, $amount)
    {
        $currencies = [
            'AED' => [
                'major' => $amount == 1 ? 'Dirham' : 'Dirhams',
                'minor' => 'Fils'
            ],
            'USD' => [
                'major' => $amount == 1 ? 'Dollar' : 'Dollars',
                'minor' => 'Cents'
            ],
            'CNY' => [
                'major' => $amount == 1 ? 'Yuan' : 'Yuan',
                'minor' => 'Jiao'
            ],
            'EUR' => [
                'major' => $amount == 1 ? 'Euro' : 'Euros',
                'minor' => 'Cents'
            ]
        ];

        return $currencies[$currency] ?? [
            'major' => $amount == 1 ? 'Unit' : 'Units',
            'minor' => 'Subunits'
        ];
    }
} 