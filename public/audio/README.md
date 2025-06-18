# Notification Audio Setup

## Add Your Custom MP3 Notification Sound

To use your own MP3 notification sound:

1. **Add your MP3 file** to this directory: `public/audio/notification.mp3`
2. **File requirements:**
   - Format: MP3
   - Duration: 1-3 seconds recommended
   - Volume: Moderate (the system will play at 70% volume)
   - Quality: Any quality is fine

## How it works

- **Primary**: The system will try to play your MP3 file first
- **Fallback**: If the MP3 fails to load, it automatically falls back to a generated beep sound
- **Volume**: Audio is set to 70% volume by default
- **Real-time**: Plays immediately when new feedback is submitted (0-3 seconds delay)

## Testing

After adding your MP3 file:
1. Login as admin
2. Have someone submit feedback on an order
3. You should hear your custom sound + see the notification

## Supported browsers

- ✅ Chrome
- ✅ Firefox  
- ✅ Safari
- ✅ Edge
- ✅ All modern browsers with MP3 support 