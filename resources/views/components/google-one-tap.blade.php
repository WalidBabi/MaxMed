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
        <p class="text-sm text-gray-600 mb-2">Sign in for a better experience</p>
        <div class="g_id_signin"
            data-type="standard"
            data-size="large"
            data-theme="outline"
            data-text="sign_in_with"
            data-shape="rectangular"
            data-logo_alignment="right"
            data-width="300">
        </div>
    </div>
@endguest
