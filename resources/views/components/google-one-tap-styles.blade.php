<style>
    /* Google One Tap Container */
    .google-one-tap-container {
        position: fixed;
        top: 80px; /* Positioned below the navbar */
        right: 20px;
        z-index: 1049; /* Just below navbar */
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 15px;
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        max-width: 350px;
        animation: slideIn 0.5s ease-out;
    }
    
    .google-one-tap-container p {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #4a5568;
    }
    
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .google-one-tap-container:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }
    
    /* Make sure the Google button is properly sized */
    #google-one-tap-container iframe {
        width: 100% !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .google-one-tap-container {
            left: 15px;
            right: 15px;
            max-width: none;
            top: 70px;
        }
    }
</style>
