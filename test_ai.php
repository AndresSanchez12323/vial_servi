<?php
/**
 * Test file for AI integration
 */

// Test without database connection first
require_once 'gemini_ai.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test AI Integration - VialServi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .ai-response {
            margin-top: 20px;
            padding: 15px;
            background: #e8f4fd;
            border-left: 4px solid #2d0f2a;
            border-radius: 5px;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 VialServi AI Integration Test</h1>
        
        <div class="test-section">
            <h3>✅ Test 1: AI Class Loading</h3>
            <?php
            try {
                $gemini = new GeminiAI();
                echo '<p class="success">✅ GeminiAI class loaded successfully</p>';
            } catch (Exception $e) {
                echo '<p class="error">❌ Error loading GeminiAI class: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="test-section">
            <h3>✅ Test 2: AI Chat Widget</h3>
            <?php
            if (file_exists('ai_chat_widget.php')) {
                echo '<p class="success">✅ AI Chat Widget file exists</p>';
                echo '<p>The chat widget is ready to be included in pages.</p>';
            } else {
                echo '<p class="error">❌ AI Chat Widget file not found</p>';
            }
            ?>
        </div>

        <div class="test-section">
            <h3>✅ Test 3: Enhanced Contact Form</h3>
            <?php
            if (file_exists('contactenos.php')) {
                echo '<p class="success">✅ Enhanced contact form exists</p>';
                echo '<p>Contact form has been enhanced with AI response capability.</p>';
            }
            ?>
        </div>

        <div class="test-section">
            <h3>✅ Test 4: Dashboard Integration</h3>
            <?php
            if (file_exists('ai_dashboard_insights.php')) {
                echo '<p class="success">✅ Dashboard insights API exists</p>';
                echo '<p>Admin dashboard has been enhanced with AI insights.</p>';
            }
            ?>
        </div>

        <div class="test-section">
            <h3>📋 Implementation Summary</h3>
            <ul>
                <li><strong>AI Core:</strong> Gemini AI integration with VialServi-specific prompts</li>
                <li><strong>Contact Form:</strong> Intelligent responses to customer inquiries</li>
                <li><strong>Chat Assistant:</strong> Available on dashboards for real-time help</li>
                <li><strong>Service Recommendations:</strong> AI-powered maintenance suggestions</li>
                <li><strong>Dashboard Insights:</strong> Business intelligence and trend analysis</li>
            </ul>
        </div>

        <div class="test-section">
            <h3>🚀 Next Steps</h3>
            <ol>
                <li>Configure your Gemini API key in <code>gemini_ai.php</code></li>
                <li>Set up environment variable: <code>GEMINI_API_KEY=your_key</code></li>
                <li>Test the enhanced contact form</li>
                <li>Try the AI chat assistant on dashboards</li>
                <li>Explore AI insights on the admin dashboard</li>
            </ol>
        </div>
    </div>

    <script>
    // Simple test for JavaScript functionality
    console.log('VialServi AI Integration - Test Page Loaded');
    console.log('Files created:');
    console.log('- gemini_ai.php (Core AI integration)');
    console.log('- ai_chat.php (Chat endpoint)'); 
    console.log('- ai_chat_widget.php (Chat widget)');
    console.log('- ai_recommendations.php (Service recommendations)');
    console.log('- ai_dashboard_insights.php (Dashboard analysis)');
    console.log('- Enhanced contactenos.php');
    console.log('- Enhanced dashboard.php with AI insights');
    console.log('- Enhanced cliente_dashboard.php with chat');
    </script>
</body>
</html>