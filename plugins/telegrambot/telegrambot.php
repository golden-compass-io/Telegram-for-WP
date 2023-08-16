<?php
/* Plugin name: telegram-bot 
* Description: Підключення телеграм-бота на сайт для отримання замовлень.
* Version: 1.0
* Author: Golden Compass
* Author URI: https://example.com
*/
require_once( plugin_dir_path( __FILE__ ) . 'telegram-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );


// Основна функція
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data)) {

        $message = "-----------------------\n<b>Application number: </b>" . $data['counter'] . "\n<i><b>Form:</b> " . $data['formname'] . "</i>\n\n";
        
        $atLeastOneChecked = false;

        foreach ($data as $name => $value) {
            $checked = in_array($name, get_option('my_plugin_settings_field', array()));

            if ($checked) {
                $atLeastOneChecked = true;
                $name = sanitize_text_field($name);
                $value = sanitize_text_field($value);

                $name_without_numbers = preg_replace('/[\d_]+/', ' ', $name);
                $value_with_b = preg_replace('/,\s*/', "\n   -", $value);

                $message .= "<b>$name_without_numbers</b>: $value_with_b\n";
               
            }
        }

        if ($atLeastOneChecked) {
            $bot_token = get_option('telegram_bot_token');
            $chat_id = get_option('telegram_chat_id');

            $text = $message;
            $parse_mode = 'HTML';

            $api_url = "https://api.telegram.org/bot$bot_token/sendMessage";
            $data = [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => $parse_mode
            ];

            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query($data)
                ]
            ];

            $context = stream_context_create($options);
            $result = file_get_contents($api_url, false, $context);
            
            
        }
    }
}



?>


