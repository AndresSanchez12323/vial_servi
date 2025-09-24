<?php
/**
 * Gemini AI Integration for VialServi
 * Configuration and utility functions for Google Gemini API
 */

class GeminiAI {
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    
    public function __construct($apiKey = null) {
        // Use environment variable or provided key
        $this->apiKey = $apiKey ?: getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
    }
    
    /**
     * Generate content using Gemini API
     */
    public function generateContent($prompt, $context = '', $temperature = 0.7) {
        $url = $this->baseUrl . '/models/gemini-pro:generateContent?key=' . $this->apiKey;
        
        // Prepare the full prompt with VialServi context
        $fullPrompt = $this->buildVialServiPrompt($prompt, $context);
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $fullPrompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ]
        ];
        
        $response = $this->makeApiCall($url, $data);
        
        if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            return $response['candidates'][0]['content']['parts'][0]['text'];
        }
        
        return 'Lo siento, no pude generar una respuesta en este momento. Por favor, contacta directamente a nuestro equipo de soporte.';
    }
    
    /**
     * Build VialServi-specific prompts
     */
    private function buildVialServiPrompt($prompt, $context) {
        $vialServiContext = "
Eres un asistente inteligente de VialServi, una empresa especializada en servicios viales y mantenimiento de vehículos. 
Tu misión es brindar respuestas útiles, precisas y profesionales sobre:

- Servicios de mantenimiento vial
- Gestión de vehículos y flotas
- Programación de servicios técnicos
- Información sobre empleados y técnicos
- Consultas de clientes sobre sus vehículos
- Reportes y estadísticas de servicios
- Soporte técnico general

Características de tus respuestas:
- Profesional y cortés
- Específica para el contexto vial/automotriz
- Concisa pero completa
- En español
- Orientada a la solución

Contexto adicional: $context

Pregunta del usuario: $prompt

Responde de manera profesional y útil:";

        return $vialServiContext;
    }
    
    /**
     * Make API call to Gemini
     */
    private function makeApiCall($url, $data) {
        $options = [
            'http' => [
                'header' => [
                    'Content-Type: application/json',
                    'User-Agent: VialServi-AI/1.0'
                ],
                'method' => 'POST',
                'content' => json_encode($data),
                'timeout' => 30
            ]
        ];
        
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            error_log('Gemini API call failed');
            return null;
        }
        
        return json_decode($result, true);
    }
    
    /**
     * Generate service recommendations based on vehicle data
     */
    public function getServiceRecommendations($vehicleData, $serviceHistory = []) {
        $context = "Datos del vehículo: " . json_encode($vehicleData) . 
                  "\nHistorial de servicios: " . json_encode($serviceHistory);
        
        $prompt = "Basándote en los datos del vehículo y su historial de servicios, recomienda qué servicios de mantenimiento serían apropiados y cuándo realizarlos. Incluye razones específicas para cada recomendación.";
        
        return $this->generateContent($prompt, $context, 0.3); // Lower temperature for more consistent recommendations
    }
    
    /**
     * Generate intelligent response for contact form inquiries
     */
    public function generateContactResponse($message, $clientData = []) {
        $context = "Mensaje del cliente: $message\n" . 
                  "Datos del cliente: " . json_encode($clientData);
        
        $prompt = "Un cliente ha enviado un mensaje de contacto. Genera una respuesta profesional que aborde su consulta de manera específica y útil. Si necesita agendar un servicio, menciona los pasos a seguir.";
        
        return $this->generateContent($prompt, $context, 0.4);
    }
    
    /**
     * Generate dashboard insights
     */
    public function generateDashboardInsights($dashboardData) {
        $context = "Datos del dashboard: " . json_encode($dashboardData);
        
        $prompt = "Analiza los datos del dashboard y proporciona insights útiles sobre tendencias, oportunidades de mejora, y recomendaciones estratégicas para el negocio de servicios viales.";
        
        return $this->generateContent($prompt, $context, 0.5);
    }
    
    /**
     * Help system for users
     */
    public function getHelp($query, $userRole = 'cliente') {
        $context = "Rol del usuario: $userRole";
        
        $prompt = "El usuario necesita ayuda con: $query. Proporciona una respuesta clara y específica sobre cómo usar el sistema VialServi para resolver su consulta.";
        
        return $this->generateContent($prompt, $context, 0.3);
    }
}

/**
 * Initialize Gemini AI instance
 */
function getGeminiAI() {
    static $gemini = null;
    if ($gemini === null) {
        $gemini = new GeminiAI();
    }
    return $gemini;
}
?>