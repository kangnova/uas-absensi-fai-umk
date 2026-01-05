    <!-- CHATBOT WIDGET -->
    <div id="chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button id="chat-toggle-btn" class="btn btn-primary rounded-circle shadow-lg p-3" style="width: 60px; height: 60px;">
            💬
        </button>
    </div>

    <!-- CHAT BOX -->
    <div id="chat-box" class="card shadow-lg d-none" style="position: fixed; bottom: 90px; right: 20px; width: 350px; height: 500px; z-index: 1000; border-radius: 15px; display: flex; flex-direction: column;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
            <span class="fw-bold">🤖 Asisten UAS FAI</span>
            <button id="close-chat" class="btn btn-sm btn-link text-white text-decoration-none">✖</button>
        </div>
        <div class="card-body p-0" style="flex: 1; overflow-y: auto; background: #f8f9fa;">
            <div id="chat-messages" class="p-3">
                <div class="d-flex flex-column align-items-start mb-2">
                    <div class="bg-white p-2 rounded shadow-sm text-dark" style="max-width: 85%;">
                        Halo! 👋 Saya <strong>Asisten Panitia UAS</strong>.
                        <br>Silakan ketik pertanyaan Anda, contoh:
                        <ul class="mb-0 ps-3 small mt-1">
                            <li>📅 <strong>"Jadwal hari ini"</strong></li>
                            <li>🗓️ <strong>"Jadwal tanggal 7 Januari"</strong></li>
                            <li>👮 <strong>"Siapa bertugas hari ini?"</strong></li>
                            <li>🕗 <strong>"Petugas Sesi 1"</strong></li>
                            <li>🔍 <strong>"Cari Pak Nova"</strong></li>
                            <li>📚 <strong>"Pengawas Matkul Filsafat"</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white p-2" style="border-radius: 0 0 15px 15px;">
            <form id="chat-form" class="d-flex gap-2">
                <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Ketik pesan..." autocomplete="off">
                <button type="submit" class="btn btn-sm btn-primary">➤</button>
            </form>
        </div>
    </div>

    <!-- Check jQuery availability, if not loaded, load it -->
    <script>
    if (typeof jQuery == 'undefined') {
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
    }
    </script>
    
    <script>
        $(document).ready(function() {
            const chatToggleBtn = $('#chat-toggle-btn');
            const chatBox = $('#chat-box');
            const closeChat = $('#close-chat');
            const chatForm = $('#chat-form');
            const chatInput = $('#chat-input');
            const chatMessages = $('#chat-messages');

            // Toggle Chat
            chatToggleBtn.click(function() {
                chatBox.toggleClass('d-none');
                if (!chatBox.hasClass('d-none')) {
                    chatInput.focus();
                }
            });

            closeChat.click(function() {
                chatBox.addClass('d-none');
            });

            // Send Message
            chatForm.submit(function(e) {
                e.preventDefault();
                const msg = chatInput.val().trim();
                if (!msg) return;

                // User Bubble
                appendMessage(msg, 'user');
                chatInput.val('');
                
                // Show Typing
                const typingId = appendTyping();

                // AJAX
                $.ajax({
                    url: 'chat_api.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ message: msg }),
                    success: function(res) {
                        removeTyping(typingId);
                        appendMessage(res.response, 'bot');
                    },
                    error: function() {
                        removeTyping(typingId);
                        appendMessage("Maaf, gagal terhubung ke server.", 'bot');
                    }
                });
            });

            function appendMessage(text, sender) {
                let align = sender === 'user' ? 'align-items-end' : 'align-items-start';
                let bg = sender === 'user' ? 'bg-primary text-white' : 'bg-white text-dark shadow-sm';
                
                let html = `
                    <div class="d-flex flex-column ${align} mb-2">
                        <div class="${bg} p-2 rounded" style="max-width: 80%; word-wrap: break-word;">
                            ${text}
                        </div>
                    </div>
                `;
                chatMessages.append(html);
                scrollToBottom();
            }

            function appendTyping() {
                const id = 'typing-' + Date.now();
                let html = `
                    <div id="${id}" class="d-flex flex-column align-items-start mb-2">
                        <div class="bg-white p-2 rounded shadow-sm text-secondary small">
                            <em>Sedang mengetik...</em>
                        </div>
                    </div>
                `;
                chatMessages.append(html);
                scrollToBottom();
                return id;
            }

            function removeTyping(id) {
                $('#' + id).remove();
            }

            function scrollToBottom() {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
        });
    </script>
