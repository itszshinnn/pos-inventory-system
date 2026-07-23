<style>
    @keyframes aiFadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ai-card-active {
        display: flex !important;
        animation: aiFadeUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @media (max-width: 768px) {
        #ai-chat-card {
            width: calc(100vw - 24px) !important;
            max-width: 360px !important;
            right: 12px !important;
            bottom: 20px !important;
            height: 480px !important;
            max-height: 80vh !important;
            z-index: 2050 !important;
        }

        #ai-chat-btn {
            bottom: 20px !important;
            right: 12px !important;
        }
    }
</style>

<div id="ai-chat-btn" onclick="toggleAiChat()" style="position: fixed; bottom: 35px; right: 20px; background: #0d6efd; color: #fff; padding: 10px 18px; border-radius: 30px; display: flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 1001; font-weight: 600; font-size: 14px;">
    <img src="../Images/message.svg" alt="AI Chat" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">
    <span>AI Assistant</span>
</div>

<div id="ai-chat-card" style="display: none; border: 1px solid #e0e0e0; background: #ffffff; border-radius: 16px; width: 360px; height: 490px; max-height: 80vh; position: fixed; bottom: 95px; right: 20px; z-index: 1000; box-shadow: 0 10px 30px rgba(0,0,0,0.22); flex-direction: column; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <div style="background: #0d6efd; color: white; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 style="margin: 0; font-size: 15px; font-weight: 600;">AI Assistant</h4>
            <span style="font-size: 11px; opacity: 0.85;">Online • Smart POS Assistant</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button onclick="startNewChat()" style="background: rgba(255,255,255,0.2); border: none; cursor: pointer; color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px;" title="Start New Chat">
                + New Chat
            </button>
            <button onclick="toggleAiChat()" style="background: none; border: none; cursor: pointer; color: white; padding: 0; display: flex; align-items: center; justify-content: center;">
                <img src="../Images/close.svg" alt="Close" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
            </button>
        </div>
    </div>

    <div id="ai-chat-logs" style="flex: 1; min-height: 0; padding: 12px; overflow-y: auto; background: #f8f9fa; display: flex; flex-direction: column; gap: 10px;">
        <div style="align-self: flex-start; background: #e9ecef; color: #212529; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
            Hello! Ask me about system records, sales, stock, or general questions.
        </div>
    </div>

    <div class="ai-prompt-chips-bar" style="padding: 8px 10px 4px; background: #ffffff; border-top: 1px solid #eeeeee; display: flex; gap: 6px; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <button onclick="sendQuickPrompt('How much is today\'s total sales?')" style="white-space: nowrap; flex-shrink: 0; background: #eef4ff; color: #0d6efd; border: 1px solid #b6d4fe; padding: 5px 11px; border-radius: 14px; font-size: 11px; font-weight: 600; cursor: pointer;">📊 Today's sales</button>
        <button onclick="sendQuickPrompt('Which items are low or out of stock?')" style="white-space: nowrap; flex-shrink: 0; background: #fff8e6; color: #d97706; border: 1px solid #fde68a; padding: 5px 11px; border-radius: 14px; font-size: 11px; font-weight: 600; cursor: pointer;">⚠️ Low stock items</button>
        <button onclick="sendQuickPrompt('What is the status of purchase orders?')" style="white-space: nowrap; flex-shrink: 0; background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; padding: 5px 11px; border-radius: 14px; font-size: 11px; font-weight: 600; cursor: pointer;">📦 Purchase orders</button>
    </div>

    <div style="padding: 8px 12px 4px; background: #ffffff;">
        <div style="display: flex; align-items: center; background: #f8f9fa; border: 1.5px solid #ced4da; border-radius: 24px; padding: 3px 4px 3px 14px; transition: border-color 0.2s;" onfocusin="this.style.borderColor='#0d6efd'" onfocusout="this.style.borderColor='#ced4da'">
            <input type="text" id="ai-user-input" placeholder="Ask a question..." style="flex: 1; background: transparent; border: none; outline: none; font-size: 13px; color: #212529; padding: 4px 0;">
            <button onclick="sendAdminQuery()" style="width: 32px; height: 32px; cursor: pointer; background: #0d6efd; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0; transition: background 0.2s;" title="Send Message">
                <img src="../Images/send.svg" alt="Send" style="width: 15px; height: 15px; filter: brightness(0) invert(1); display: block;">
            </button>
        </div>
    </div>

    <div style="padding: 2px 12px 8px; background: #ffffff; text-align: center; font-size: 10px; color: #9a9a9a; font-weight: 500; letter-spacing: 0.3px;">
        Powered by Llama
    </div>
</div>

<script>
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
            chatCard.classList.add('ai-card-active');
        } else {
            chatCard.classList.remove('ai-card-active');
            chatCard.style.display = 'none';
        }
    }

    function toggleAiChat() {
        const chatCard = document.getElementById('ai-chat-card');
        if (chatCard.style.display === 'flex' || chatCard.classList.contains('ai-card-active')) {
            chatCard.classList.remove('ai-card-active');
            chatCard.style.display = 'none';
        } else {
            chatCard.style.display = 'flex';
            chatCard.classList.add('ai-card-active');
        }
        saveChatState();
    }

    function startNewChat() {
        const logs = document.getElementById('ai-chat-logs');
        const input = document.getElementById('ai-user-input');

        logs.innerHTML = `
            <div style="align-self: flex-start; background: #e9ecef; color: #212529; padding: 8px 12px; border-radius: 12px 12px 12px 2px; max-width: 80%; font-size: 13px; line-height: 1.4;">
                Hello! Ask me about system records, sales, stock, or general questions.
            </div>`;
        input.value = '';

        sessionStorage.removeItem(CHAT_STORAGE_KEY);
        sessionStorage.removeItem(CHAT_DRAFT_KEY);
        saveChatState();
    }

    function sendQuickPrompt(promptText) {
        const input = document.getElementById('ai-user-input');
        input.value = promptText;
        sendAdminQuery();
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
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question: question
                })
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

    document.getElementById('ai-user-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendAdminQuery();
        }
    });
</script>