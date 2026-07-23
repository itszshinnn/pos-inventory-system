<div id="ai-chat-btn" onclick="toggleAiChat()" style="position: fixed; bottom: 20px; right: 20px; background: #0d6efd; color: #fff; width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 1001; font-size: 24px;">
  💬
</div>

<div id="ai-chat-card" style="display: none; border: 1px solid #e0e0e0; background: #ffffff; border-radius: 12px; width: 360px; height: 480px; position: fixed; bottom: 85px; right: 20px; z-index: 1000; box-shadow: 0 8px 24px rgba(0,0,0,0.18); flex-direction: column; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
  
  <div style="background: #0d6efd; color: white; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h4 style="margin: 0; font-size: 15px; font-weight: 600;">POS Assistant</h4>
      <span style="font-size: 11px; opacity: 0.85;">Online • Smart POS Assistant</span>
    </div>
    <button onclick="toggleAiChat()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: white; line-height: 1;">✖</button>
  </div>
  
  <div id="ai-chat-logs" style="flex: 1; padding: 12px; overflow-y: auto; background: #f8f9fa; display: flex; flex-direction: column; gap: 10px;">
    <div style="align-self: flex-start; background: #e9ecef; color: #212529; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
      Hello! Ask me about system records, sales, stock, or general questions.
    </div>
  </div>

  <div style="padding: 10px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 6px;">
    <input type="text" id="ai-user-input" placeholder="Ask a question..." style="flex: 1; padding: 8px 12px; font-size: 13px; border: 1px solid #ced4da; border-radius: 20px; outline: none;">
    <button onclick="sendAdminQuery()" style="padding: 8px 14px; cursor: pointer; background: #0d6efd; color: white; border: none; border-radius: 20px; font-size: 13px; font-weight: 500;">Send</button>
  </div>
</div>

<script>
// Storage keys
const CHAT_STORAGE_KEY = 'ai_chat_logs_history';
const CHAT_OPEN_KEY = 'ai_chat_is_open';
const CHAT_DRAFT_KEY = 'ai_chat_draft_text';

document.addEventListener('DOMContentLoaded', () => {
    restoreChatState();
    
    const input = document.getElementById('ai-user-input');
    input.addEventListener('input', () => {
        sessionStorage.setItem(CHAT_DRAFT_KEY, input.value);
    });
});

function saveChatState() {
    const logs = document.getElementById('ai-chat-logs');
    const chatCard = document.getElementById('ai-chat-card');
    const input = document.getElementById('ai-user-input');
    
    sessionStorage.setItem(CHAT_STORAGE_KEY, logs.innerHTML);
    
    const isOpen = chatCard.style.display === 'flex';
    sessionStorage.setItem(CHAT_OPEN_KEY, isOpen ? 'true' : 'false');

    sessionStorage.setItem(CHAT_DRAFT_KEY, input.value);
}

function restoreChatState() {
    const logs = document.getElementById('ai-chat-logs');
    const chatCard = document.getElementById('ai-chat-card');
    const input = document.getElementById('ai-user-input');
    
    const savedLogs = sessionStorage.getItem(CHAT_STORAGE_KEY);
    if (savedLogs) {
        logs.innerHTML = savedLogs;
        logs.scrollTop = logs.scrollHeight;
    }

    const savedDraft = sessionStorage.getItem(CHAT_DRAFT_KEY);
    if (savedDraft !== null) {
        input.value = savedDraft;
    }

    const savedOpenState = sessionStorage.getItem(CHAT_OPEN_KEY);
    if (savedOpenState === 'true') {
        chatCard.style.display = 'flex';
    } else {
        chatCard.style.display = 'none';
    }
}

function toggleAiChat() {
    const chatCard = document.getElementById('ai-chat-card');
    if (chatCard.style.display === 'none' || chatCard.style.display === '') {
        chatCard.style.display = 'flex';
    } else {
        chatCard.style.display = 'none';
    }
    saveChatState();
}

function parseMarkdown(text) {
    let cleanText = text.trim();
    cleanText = cleanText.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
    return cleanText;
}

async function sendAdminQuery() {
    const input = document.getElementById('ai-user-input');
    const logs = document.getElementById('ai-chat-logs');
    const question = input.value.trim();

    if (!question) return;

    logs.innerHTML += `
        <div style="align-self: flex-end; background: #0d6efd; color: white; padding: 8px 12px; border-radius: 12px 12px 2px 12px; max-width: 80%; font-size: 13px; line-height: 1.4;">
            ${escapeHtml(question)}
        </div>`;
    
    input.value = '';
    sessionStorage.removeItem(CHAT_DRAFT_KEY);
    
    logs.scrollTop = logs.scrollHeight;
    saveChatState();

    const loadingId = 'loading-' + Date.now();
    logs.innerHTML += `
        <div id="${loadingId}" style="align-self: flex-start; background: #e9ecef; color: #6c757d; padding: 8px 12px; border-radius: 12px 12px 12px 2px; font-size: 12px;">
            Thinking...
        </div>`;
    logs.scrollTop = logs.scrollHeight;

    try {
        const response = await fetch('../Inventory_backend/api_ai_chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question: question })
        });

        const data = await response.json();
        const loadingElem = document.getElementById(loadingId);
        if (loadingElem) loadingElem.remove();

        if (data.success) {
            logs.innerHTML += `
                <div style="align-self: flex-start; background: #e9ecef; color: #212529; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
                    ${parseMarkdown(data.answer)}
                </div>`;
        } else {
            logs.innerHTML += `
                <div style="align-self: flex-start; background: #f8d7da; color: #721c24; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
                    ${escapeHtml(data.error)}
                </div>`;
        }
    } catch (err) {
        const loadingElem = document.getElementById(loadingId);
        if (loadingElem) loadingElem.remove();
        logs.innerHTML += `
            <div style="align-self: flex-start; background: #f8d7da; color: #721c24; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
                Error: Unable to reach backend server.
            </div>`;
    }

    logs.scrollTop = logs.scrollHeight;
    saveChatState();
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.getElementById('ai-user-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        sendAdminQuery();
    }
});
</script>