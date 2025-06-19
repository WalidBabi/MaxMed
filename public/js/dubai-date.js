/**
 * Dubai Date Formatting Utilities
 * Converts dates to Dubai timezone for consistent display
 */

// Set Dubai timezone
const DUBAI_TIMEZONE = 'Asia/Dubai';

/**
 * Format a date string in Dubai timezone
 * @param {string|Date} dateString - The date to format
 * @param {Object} options - Intl.DateTimeFormat options
 * @returns {string} Formatted date string
 */
function formatDubaiDate(dateString, options = {}) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'Invalid Date';
    
    const defaultOptions = {
        timeZone: DUBAI_TIMEZONE,
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    
    const formatOptions = { ...defaultOptions, ...options };
    
    try {
        return new Intl.DateTimeFormat('en-US', formatOptions).format(date);
    } catch (error) {
        console.error('Error formatting date:', error);
        return date.toString();
    }
}

/**
 * Format a date for display with time
 * @param {string|Date} dateString 
 * @returns {string}
 */
function formatDubaiDateTime(dateString) {
    return formatDubaiDate(dateString, {
        timeZone: DUBAI_TIMEZONE,
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

/**
 * Format a date for display without time
 * @param {string|Date} dateString 
 * @returns {string}
 */
function formatDubaiDateOnly(dateString) {
    return formatDubaiDate(dateString, {
        timeZone: DUBAI_TIMEZONE,
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Get relative time in Dubai timezone
 * @param {string|Date} dateString 
 * @returns {string}
 */
function formatTimeAgo(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'Invalid Date';
    
    // Convert to Dubai timezone for calculation
    const now = new Date();
    const dubaiOffset = getDubaiOffset();
    const localOffset = now.getTimezoneOffset() * 60000;
    const dubaiNow = new Date(now.getTime() + localOffset + dubaiOffset);
    const dubaiDate = new Date(date.getTime() + localOffset + dubaiOffset);
    
    const diffInSeconds = Math.floor((dubaiNow - dubaiDate) / 1000);
    
    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' days ago';
    if (diffInSeconds < 31536000) return Math.floor(diffInSeconds / 2592000) + ' months ago';
    return Math.floor(diffInSeconds / 31536000) + ' years ago';
}

/**
 * Get Dubai timezone offset in milliseconds
 * @returns {number}
 */
function getDubaiOffset() {
    const dubaiTime = new Date().toLocaleString('en-US', { timeZone: DUBAI_TIMEZONE });
    const localTime = new Date().toLocaleString('en-US');
    return new Date(dubaiTime).getTime() - new Date(localTime).getTime();
}

/**
 * Get current time in Dubai timezone
 * @returns {Date}
 */
function nowDubai() {
    const now = new Date();
    const dubaiOffset = getDubaiOffset();
    const localOffset = now.getTimezoneOffset() * 60000;
    return new Date(now.getTime() + localOffset + dubaiOffset);
}

// Make functions available globally
window.formatDubaiDate = formatDubaiDate;
window.formatDubaiDateTime = formatDubaiDateTime;
window.formatDubaiDateOnly = formatDubaiDateOnly;
window.formatTimeAgo = formatTimeAgo;
window.nowDubai = nowDubai; 