<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LM Studio Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #1e1e1e;
            color: #d4d4d4;
        }

        /* Список чатов */
        .sidebar {
            width: 250px;
            background-color: #252526;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }

        .chat-tab {
            padding: 10px;
            margin-bottom: 5px;
            cursor: pointer;
            border-radius: 4px;
            background-color: #333;
        }

        .chat-tab:hover {
            background-color: #444;
        }

        .chat-tab.active {
            background-color: #0078d4;
        }

        /* Центральная область диалога */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            overflow-y: auto;
        }

        .chat-messages {
            flex: 1;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .user-message {
            background-color: #3a3d41;
            align-self: flex-end;
        }

        .ai-message {
            background-color: #252526;
            align-self: flex-start;
        }

        /* Форма ввода */
        .input-area {
            display: flex;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #444;
        }

        .message-input {
            flex: 1;
            background-color: #333;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            resize: none;
            height: 50px;
        }

        .send-button {
            background-color: #0078d4;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            cursor: pointer;
        }

        .send-button:hover {
            background-color: #005a9e;
        }
    </style>
</head>
<body>

<!-- Список чатов -->
<div class="sidebar">
    <div class="chat-tab active">Chat 1</div>
    <div class="chat-tab">Chat 2</div>
    <div class="chat-tab">Chat 3</div>
    <div class="chat-tab">New Chat</div>
</div>

<!-- Центральная часть -->
<div class="main-content">
    <div class="chat-messages">
        <div class="message ai-message">Привет! Как я могу помочь?</div>
        <div class="message user-message">Расскажи о себе</div>
        <div class="message ai-message">Я — ИИ-ассистент, созданный для помощи в ответах на вопросы и генерации текста.</div>
    </div>

    <!-- Форма ввода -->
    <div class="input-area">
        <textarea class="message-input" placeholder="Введите сообщение..."></textarea>
        <button class="send-button">Отправить</button>
    </div>
</div>

</body>
</html>
