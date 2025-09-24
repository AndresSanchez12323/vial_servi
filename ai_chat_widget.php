<?php
/**
 * AI Chat Widget for VialServi
 * Include this file to add AI assistant to any page
 */
?>

<!-- AI Chat Widget Styles -->
<style>
.ai-chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    max-height: 600px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    transition: all 0.3s ease;
    transform: translateY(100%);
    opacity: 0;
}

.ai-chat-widget.open {
    transform: translateY(0);
    opacity: 1;
}

.ai-chat-header {
    background: linear-gradient(135deg, #2d0f2a, #440f33);
    color: white;
    padding: 15px 20px;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ai-chat-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.ai-chat-header .close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.ai-chat-header .close-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.ai-chat-body {
    height: 400px;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.ai-message, .user-message {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.ai-message {
    background: #f0f0f0;
    color: #333;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
}

.user-message {
    background: #2d0f2a;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
}

.ai-chat-input {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    border-radius: 0 0 15px 15px;
}

.ai-chat-input input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
}

.ai-chat-input button {
    background: #2d0f2a;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s;
}

.ai-chat-input button:hover {
    background: #440f33;
}

.ai-chat-input button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.ai-chat-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #2d0f2a, #440f33);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 999;
}

.ai-chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

.ai-chat-toggle.hidden {
    display: none;
}

.typing-indicator {
    display: none;
    align-self: flex-start;
    padding: 12px 16px;
    background: #f0f0f0;
    border-radius: 18px;
    border-bottom-left-radius: 5px;
    max-width: 80px;
}

.typing-dots {
    display: flex;
    gap: 3px;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    background: #999;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.ai-badge {
    font-size: 12px;
    background: linear-gradient(135deg, #2d0f2a, #440f33);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    margin-bottom: 5px;
    display: inline-block;
}

@media (max-width: 768px) {
    .ai-chat-widget {
        width: calc(100vw - 40px);
        bottom: 10px;
        right: 20px;
        left: 20px;
    }
    
    .ai-chat-toggle {
        bottom: 10px;
        right: 20px;
    }
}
</style>

<!-- AI Chat Widget HTML -->
<button class="ai-chat-toggle" id="aiChatToggle" title="Asistente AI VialServi">
    🤖
</button>

<div class="ai-chat-widget" id="aiChatWidget">
    <div class="ai-chat-header">
        <h3>🤖 Asistente VialServi AI</h3>
        <button class="close-btn" id="aiChatClose">×</button>
    </div>
    <div class="ai-chat-body" id="aiChatBody">
        <div class="ai-message">
            <div class="ai-badge">VialServi AI</div>
            ¡Hola! Soy tu asistente virtual de VialServi. Estoy aquí para ayudarte con información sobre servicios, vehículos, programación de citas y cualquier consulta que tengas. ¿En qué puedo ayudarte hoy?
        </div>
    </div>
    <div class="typing-indicator" id="typingIndicator">
        <div class="typing-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="ai-chat-input">
        <input type="text" id="aiChatInput" placeholder="Escribe tu pregunta..." maxlength="500">
        <button id="aiChatSend">Enviar</button>
    </div>
</div>

<!-- AI Chat Widget JavaScript -->
<script>
class VialServiAI {
    constructor() {
        this.widget = document.getElementById('aiChatWidget');
        this.toggle = document.getElementById('aiChatToggle');
        this.closeBtn = document.getElementById('aiChatClose');
        this.chatBody = document.getElementById('aiChatBody');
        this.chatInput = document.getElementById('aiChatInput');
        this.sendBtn = document.getElementById('aiChatSend');
        this.typingIndicator = document.getElementById('typingIndicator');
        
        this.isOpen = false;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        // Event listeners
        this.toggle.addEventListener('click', () => this.toggleChat());
        this.closeBtn.addEventListener('click', () => this.closeChat());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Close chat when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.widget.contains(e.target) && !this.toggle.contains(e.target)) {
                this.closeChat();
            }
        });
    }
    
    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }
    
    openChat() {
        this.isOpen = true;
        this.widget.classList.add('open');
        this.toggle.classList.add('hidden');
        this.chatInput.focus();
    }
    
    closeChat() {
        this.isOpen = false;
        this.widget.classList.remove('open');
        this.toggle.classList.remove('hidden');
    }
    
    async sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message || this.isLoading) return;
        
        // Add user message to chat
        this.addMessage(message, 'user');
        this.chatInput.value = '';
        
        // Show typing indicator
        this.showTyping();
        this.isLoading = true;
        this.sendBtn.disabled = true;
        
        try {
            const response = await fetch('ai_chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    context: this.getContext()
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.addMessage(data.response, 'ai');
            } else {
                this.addMessage(data.error || 'Lo siento, ocurrió un error. Por favor, intenta de nuevo.', 'ai');
            }
        } catch (error) {
            console.error('Error:', error);
            this.addMessage('Lo siento, no pude conectarme al servidor. Por favor, verifica tu conexión e intenta de nuevo.', 'ai');
        } finally {
            this.hideTyping();
            this.isLoading = false;
            this.sendBtn.disabled = false;
            this.chatInput.focus();
        }
    }
    
    addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = type === 'user' ? 'user-message' : 'ai-message';
        
        if (type === 'ai') {
            messageDiv.innerHTML = `<div class="ai-badge">VialServi AI</div>${this.formatMessage(content)}`;
        } else {
            messageDiv.textContent = content;
        }
        
        this.chatBody.appendChild(messageDiv);
        this.scrollToBottom();
    }
    
    formatMessage(content) {
        // Basic formatting for AI responses
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }
    
    showTyping() {
        this.typingIndicator.style.display = 'block';
        this.scrollToBottom();
    }
    
    hideTyping() {
        this.typingIndicator.style.display = 'none';
    }
    
    scrollToBottom() {
        setTimeout(() => {
            this.chatBody.scrollTop = this.chatBody.scrollHeight;
        }, 100);
    }
    
    getContext() {
        // Get current page context
        const pageTitle = document.title;
        const currentUrl = window.location.pathname;
        return `Página actual: ${pageTitle} (${currentUrl})`;
    }
}

// Initialize AI Chat when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.vialServiAI = new VialServiAI();
});
</script>