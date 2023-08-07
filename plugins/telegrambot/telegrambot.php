<?php

/* Plugin name: telegram-bot 
* Description: Підключення телеграм-бота на сайт для отримання замовлень.
* Version: 1.0
* Author: Ваше ім'я або назва компанії
* Author URI: https://example.com
*/
require_once( plugin_dir_path( __FILE__ ) . 'admin.php' );


// // Основна функція
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из тела запроса, которые были отправлены в JSON-формате
    $data = json_decode(file_get_contents('php://input'), true);

    // Проверяем, что данные существуют и содержат необходимые поля
    if (!empty($data)) {
        // Извлекаем данные из массива $data
        $message = $data['formname'] . "\n";
        

        foreach ($data as $name => $value) {
            $checked = in_array($name, get_option('my_plugin_settings_fields', array()));

            // Если чекбокс был отмечен, добавляем данные в сообщение для отправки в Telegram Bot
            if ($checked) {
                $name = sanitize_text_field($name);
                $value = sanitize_text_field($value);
                $message .= "$name: $value\n";
            }
        }

        // Добавляем информацию о продуктах из массива $data

        // Получаем значения токена и ID чата из настроек плагина
        $bot_token = get_option('telegram_bot_token');
        $chat_id = get_option('telegram_chat_id');

        // URL для отправки сообщения в телеграм
        $api_url = "https://api.telegram.org/bot$bot_token/sendMessage";

        // Отправка сообщения в телеграм
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=$chat_id&text=$message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Возвращаем успешный ответ обратно в JavaScript
        echo json_encode(['success' => true]);
    }
}




?>

