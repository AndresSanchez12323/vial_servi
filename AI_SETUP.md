# VialServi AI Integration Setup Guide

## 🤖 Gemini AI Integration

This guide explains how to set up and configure the Gemini AI integration in VialServi.

### Features Implemented

1. **AI-Powered Contact Form** - Provides intelligent responses to customer inquiries
2. **AI Chat Assistant** - Available on client and employee dashboards
3. **Service Recommendations** - AI-powered suggestions based on vehicle history
4. **Dashboard Insights** - Intelligent analysis of business data and trends

### Setup Instructions

#### 1. Get Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key for Gemini Pro
3. Copy the API key

#### 2. Configure API Key

**Option A: Environment Variable (Recommended)**
```bash
export GEMINI_API_KEY="your_api_key_here"
```

**Option B: Direct Configuration**
Edit `gemini_ai.php` and replace `YOUR_GEMINI_API_KEY_HERE` with your actual API key:
```php
$this->apiKey = $apiKey ?: getenv('GEMINI_API_KEY') ?: 'your_actual_api_key_here';
```

#### 3. Test the Integration

1. **Contact Form**: Visit `contactenos.php` and submit a message
2. **Chat Assistant**: Look for the 🤖 button on dashboard pages
3. **Dashboard Insights**: Click "Insights AI del Dashboard" on the admin dashboard

### File Structure

```
vial_servi/
├── gemini_ai.php              # Core AI integration class
├── ai_chat.php               # Chat assistant endpoint
├── ai_chat_widget.php        # Chat widget component
├── ai_recommendations.php    # Service recommendations API
├── ai_dashboard_insights.php # Dashboard analysis API
├── contactenos.php          # Enhanced contact form
├── cliente_dashboard.php    # Client dashboard with AI chat
├── dashboard.php           # Admin dashboard with AI insights
└── AI_SETUP.md            # This setup guide
```

### Usage Examples

#### Contact Form AI Response
- Users submit inquiries through the contact form
- AI provides immediate, contextual responses
- Responses are saved with the inquiry for staff review

#### Chat Assistant
- Available on client and employee dashboards
- Context-aware responses based on user role and data
- Helps with service information, scheduling, and troubleshooting

#### Service Recommendations
- Analyzes vehicle history and provides maintenance suggestions
- Accessible via API: `ai_recommendations.php?placa=ABC123`
- Returns personalized recommendations and maintenance tips

#### Dashboard Insights
- Provides business intelligence and trend analysis
- Accessible to administrators only
- Generates actionable recommendations for business improvement

### Security Notes

- API keys should never be committed to version control
- Use environment variables or secure configuration files
- Validate all user inputs before sending to AI
- Implement rate limiting if needed for production

### Troubleshooting

**Common Issues:**

1. **"API key not configured"**
   - Ensure GEMINI_API_KEY environment variable is set
   - Or update the API key directly in `gemini_ai.php`

2. **"No response from AI"**
   - Check internet connectivity
   - Verify API key is valid and has quota remaining
   - Check error logs in PHP error log

3. **"Permission denied"**
   - Ensure user is logged in with appropriate permissions
   - Check session management and user roles

### Cost Considerations

- Gemini Pro API has usage-based pricing
- Monitor API usage in Google Cloud Console
- Consider implementing caching for frequently requested insights
- Set usage limits to prevent unexpected costs

### Support

For technical support or questions about the AI integration:
1. Check the PHP error logs
2. Verify API key configuration
3. Test with simple requests first
4. Contact the development team if issues persist

---

🚀 **VialServi AI is now ready to provide intelligent assistance to your users!**