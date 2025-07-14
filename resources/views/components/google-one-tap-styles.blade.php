<style>
    /* Desktop Google One Tap Container (Top Right) */
    .desktop-google-signin {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1049;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 15px;
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        max-width: 350px;
        animation: slideIn 0.5s ease-out;
        display: block;
    }
    
    .desktop-google-signin p {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #4a5568;
    }
    
    /* Mobile Google Sign-in (Bottom) */
    .mobile-google-signin {
        position: fixed;
        bottom: 20px;
        left: 15px;
        right: 15px;
        z-index: 1048;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border: 1px solid #e0e0e0;
        display: none;
        animation: slideUp 0.5s ease-out;
    }
    
    .mobile-google-signin-content {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .mobile-google-signin-text {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #4a5568;
        font-weight: 500;
    }
    
    /* Mobile Google Sign-in (Bottom) - Alternative compact version */
    .mobile-google-signin-compact {
        position: fixed;
        bottom: 20px;
        left: 15px;
        right: 15px;
        z-index: 1048;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border: 1px solid #e0e0e0;
        display: none;
        animation: slideUp 0.5s ease-out;
    }
    
    .mobile-google-signin-compact-content {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    
    .mobile-google-signin-compact-text {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #4a5568;
        font-weight: 500;
    }
    
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .desktop-google-signin:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }
    
    .mobile-google-signin:hover,
    .mobile-google-signin-compact:hover {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    /* Make sure the Google button is properly sized */
    .desktop-google-signin iframe,
    .mobile-google-signin iframe,
    .mobile-google-signin-compact iframe {
        width: 100% !important;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .desktop-google-signin {
            display: none;
        }
        
        .mobile-google-signin {
            display: block;
        }
        
        /* Show compact version on very small screens */
        @media (max-width: 480px) {
            .mobile-google-signin {
                display: none;
            }
            
            .mobile-google-signin-compact {
                display: block;
            }
        }
    }
    
    @media (min-width: 769px) {
        .mobile-google-signin,
        .mobile-google-signin-compact {
            display: none;
        }
        
        .desktop-google-signin {
            display: block;
        }
    }
    
    /* Ensure proper spacing with cookie consent */
    @media (max-width: 768px) {
        .mobile-google-signin,
        .mobile-google-signin-compact {
            bottom: 120px; /* Space for cookie consent */
            transition: bottom 0.3s ease;
        }
        
        /* When cookie consent is hidden, move Google sign-in to bottom */
        body.cookie-consent-hidden .mobile-google-signin,
        body.cookie-consent-hidden .mobile-google-signin-compact {
            bottom: 20px;
        }
        
        /* When cookie consent is visible, keep Google sign-in above it */
        body.cookie-consent-visible .mobile-google-signin,
        body.cookie-consent-visible .mobile-google-signin-compact {
            bottom: 120px;
        }
    }
    
    /* Touch-friendly improvements */
    @media (max-width: 768px) {
        .mobile-google-signin-content,
        .mobile-google-signin-compact-content {
            min-height: 44px; /* Minimum touch target size */
        }
        
        .mobile-google-signin-text,
        .mobile-google-signin-compact-text {
            min-height: 44px;
            display: flex;
            align-items: center;
        }
    }
</style>
