@guest
    <div id="g_id_onload"
        data-client_id="{{ config('services.google.client_id') }}"
        data-callback="handleCredentialResponse"
        data-auto_prompt="true"
        data-cancel_on_tap_outside="false"
        data-context="signin"
        data-ux_mode="popup"
        data-itp_support="true">
    </div>
    <div id="google-one-tap-container" class="google-one-tap-container">
        <div class="google-one-tap-inner">
            <div class="google-one-tap-header">
                <h4>Sign in with Google</h4>
                <button type="button" class="google-one-tap-close" onclick="hideGoogleOneTapContainer()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="google-one-tap-content">
                <p>Sign in for a faster, easier experience</p>
                <div class="g_id_signin"
                    data-type="standard"
                    data-size="large"
                    data-theme="outline"
                    data-text="sign_in_with"
                    data-shape="rectangular"
                    data-logo_alignment="left"
                    data-width="300">
                </div>
                <div class="google-one-tap-footer">
                    <small>By continuing, you agree to our <a href="{{ route('privacy.policy') }}">Terms of Service</a> and <a href="{{ route('privacy.policy') }}">Privacy Policy</a>.</small>
                </div>
            </div>
        </div>
    </div>
@endguest

<style>
    /* Google One Tap Container */
    .google-one-tap-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.1);
        max-width: 380px;
        overflow: hidden;
        transform: translateY(0);
        opacity: 1;
        display: none;
        animation: slideIn 0.3s ease-out forwards;
    }
    
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .google-one-tap-inner {
        position: relative;
        padding: 0;
    }

    .google-one-tap-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: linear-gradient(135deg, #4285f4, #34a853);
        color: white;
    }

    .google-one-tap-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
    }

    .google-one-tap-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        padding: 0 8px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .google-one-tap-close:hover {
        opacity: 1;
    }

    .google-one-tap-content {
        padding: 20px;
    }

    .google-one-tap-content p {
        margin: 0 0 16px;
        color: #5f6368;
        font-size: 14px;
        line-height: 1.5;
    }

    .google-one-tap-footer {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid #f1f3f4;
        text-align: center;
    }

    .google-one-tap-footer small {
        color: #5f6368;
        font-size: 12px;
        line-height: 1.4;
    }

    .google-one-tap-footer a {
        color: #1a73e8;
        text-decoration: none;
    }

    .google-one-tap-footer a:hover {
        text-decoration: underline;
    }

    /* Mobile-specific styles for Google One Tap */
    @media (max-width: 768px) {
        .google-one-tap-container {
            top: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
            width: calc(100% - 20px);
            border-radius: 8px;
        }
        
        .google-one-tap-header {
            padding: 12px 16px;
        }
        
        .google-one-tap-header h4 {
            font-size: 14px;
        }
        
        .google-one-tap-content {
            padding: 16px;
        }
        
        .google-one-tap-content p {
            font-size: 13px;
            margin-bottom: 12px;
        }
        
        .google-one-tap-footer {
            margin-top: 12px;
            padding-top: 8px;
        }
        
        .google-one-tap-footer small {
            font-size: 11px;
        }
        
        .google-one-tap-close {
            font-size: 20px;
            padding: 0 4px;
        }
    }
</style>
