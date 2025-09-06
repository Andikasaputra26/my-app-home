<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #eef2f7;
        }
        .chat-container {
            max-width: 800px;
            margin: auto;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            background-color: white;
            display: flex;
            flex-direction: column;
            height: 90vh;
            overflow: hidden;
        }
        .chat-header {
            background-color: #0d6efd;
            color: white;
            padding: 1.5rem;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }
        .chat-body {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .message {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            word-wrap: break-word;
        }
        .user-message {
            background-color: #e2f0ff;
            color: #333;
            align-self: flex-end;
            text-align: right;
            border-bottom-right-radius: 0;
        }
        .ai-message {
            background-color: #f1f3f4;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }
        .message-content {
            white-space: pre-wrap;
            line-height: 1.6;
            margin-bottom: 0;
        }
        .chat-footer {
            padding: 1rem;
            background-color: #f8f9fa;
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }
        #loading-indicator {
            display: none;
            padding: 1rem;
            text-align: left;
        }
        #voice-status {
            display: none;
            color: #0d6efd;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        #voice-status.active {
            display: block;
        }
        .voice-btn {
            background: none;
            border: none;
            padding: 0.5rem;
            color: #6c757d;
            transition: color 0.3s;
        }
        .voice-btn:hover {
            color: #0d6efd;
        }
        .voice-btn.recording {
            color: #dc3545; /* Merah saat merekam */
        }
    </style>
</head>
<body>
<div class="py-5">
    <div class="chat-container">
        <div class="chat-header text-center">
            <h4 class="mb-0 fw-bold">AI Chatbot</h4>
            <p class="mb-0 text-white-50">Tanya apa saja kepada saya!</p>
        </div>

        <div class="chat-body" id="chat-body">
            <div class="message ai-message">
                <p class="message-content">Halo! Saya adalah AI. Ada yang bisa saya bantu?</p>
            </div>
        </div>

        <div id="loading-indicator" class="text-muted">
            <div class="spinner-grow spinner-grow-sm text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
            Gemini sedang mengetik...
        </div>

        <div id="voice-status" class="text-muted">
            <div class="spinner-grow spinner-grow-sm text-primary" role="status"></div>
            Mendengarkan...
        </div>

        <div class="chat-footer">
            <form id="gemini-form" class="d-flex align-items-center">
                @csrf
                <button type="button" id="voice-btn" class="voice-btn me-2">
                    <i class="bi bi-mic-fill fs-4"></i>
                </button>
                <textarea name="prompt" id="prompt" class="form-control me-2" rows="1" placeholder="Ketik pesan Anda..." required></textarea>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // JavaScript untuk fitur Voice-to-Text
    const voiceBtn = document.getElementById('voice-btn');
    const promptInput = document.getElementById('prompt');
    const voiceStatus = document.getElementById('voice-status');

    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false; // Berhenti setelah selesai berbicara
        recognition.interimResults = false;
        recognition.lang = 'id-ID'; // Set bahasa ke Bahasa Indonesia

        voiceBtn.addEventListener('click', () => {
            if (voiceBtn.classList.contains('recording')) {
                recognition.stop();
                voiceBtn.classList.remove('recording');
            } else {
                recognition.start();
                voiceBtn.classList.add('recording');
            }
        });

        recognition.onstart = () => {
            voiceStatus.classList.add('active');
            promptInput.placeholder = "Mendengarkan...";
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            promptInput.value = transcript;
        };

        recognition.onend = () => {
            voiceStatus.classList.remove('active');
            voiceBtn.classList.remove('recording');
            promptInput.placeholder = "Ketik pesan Anda...";
            // Secara otomatis kirim pesan setelah selesai merekam
            if (promptInput.value.trim() !== '') {
                $('#gemini-form').submit();
            }
        };

        recognition.onerror = (event) => {
            voiceStatus.classList.remove('active');
            voiceBtn.classList.remove('recording');
            promptInput.placeholder = "Ketik pesan Anda...";
            console.error('Speech recognition error:', event.error);
            alert("Gagal mendengarkan. Pastikan mikrofon Anda diaktifkan dan browser mendukung fitur ini.");
        };
    } else {
        voiceBtn.style.display = 'none'; 
        console.warn('Browser tidak mendukung Web Speech API.');
    }

    $('#gemini-form').on('submit', function(e) {
        e.preventDefault();
        const promptText = $('#prompt').val();
        if (promptText.trim() === '') return;

        const userMessageHtml = `<div class="message user-message"><p class="message-content">${promptText}</p></div>`;
        $('#chat-body').append(userMessageHtml);
        $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);

        $('#loading-indicator').show();
        $('#prompt').val('');

        $.ajax({
            url: "{{ route('ai.gemini.generate') }}",
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                prompt: promptText
            },
            success: function(response) {
                $('#loading-indicator').hide();
                const aiMessageHtml = `<div class="message ai-message"><p class="message-content">${response.result}</p></div>`;
                $('#chat-body').append(aiMessageHtml);
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
            },
            error: function(xhr) {
                $('#loading-indicator').hide();
                const errorMessageHtml = `<div class="message ai-message text-danger"><p class="message-content">Maaf, terjadi kesalahan. Silakan coba lagi.</p></div>`;
                $('#chat-body').append(errorMessageHtml);
                alert("Terjadi kesalahan. Coba lagi.");
            }
        });
    });
</script>
</body>
</html>