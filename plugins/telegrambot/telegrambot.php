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
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Получаем данные из тела запроса, которые были отправлены в JSON-формате
//     $data = json_decode(file_get_contents('php://input'), true);

//     // Проверяем, что данные существуют и содержат необходимые поля
//     if (!empty($data)) {
//         // Извлекаем данные из массива $data
//         $message = "*Form:* " . $data['formname'] . "\n\n";

//         // Змінна-флаг для перевірки, чи був принаймні один чекбокс чекед
//         $atLeastOneChecked = false;

//         $i = 0; // Индекс для полей "product_names_$i"

//         foreach ($data as $name => $value) {
//             $checked = in_array($name, get_option('my_plugin_settings_forms', array()));

//             // Якщо чекбокс був відзначений, встановлюємо флаг в true
//             if ($checked && $name) {
//                 if ($name === "product_names_$i") {
//                     $i++;
//                     continue;
//                 }
//                 $atLeastOneChecked = true;
//                 $name = sanitize_text_field($name);
//                 $value = sanitize_text_field($value);
//                 $message .= "*$name*: $value\n";
//             }
//         }
//          // Додайте перевірку для "totalprice"
//         $totalprice_checked = in_array('totalprice', get_option('my_plugin_settings_forms', array()));
//         if ($totalprice_checked) {
//              $totalprice_value = $data['totalprice'];
//              $message .= "*Total price*: $totalprice_value\n";
//              $atLeastOneChecked = true;
//         }
 
//          // Додайте перевірку для "product_names"
//         $product_names_checked = in_array('product_names', get_option('my_plugin_settings_forms', array()));
//         if ($product_names_checked) {
//              foreach ($data as $name => $value) {
//                  if (strpos($name, 'product_names_') === 0) {
//                      $value = sanitize_text_field($value);
//                      $message .= "*Product Name*: $value\n";
//                      $atLeastOneChecked = true;
//                  }
//              }
//         }

//         if ($atLeastOneChecked) {
//             // Отримуємо значення токена і ID чата з налаштувань плагіна
//             $bot_token = get_option('telegram_bot_token');
//             $chat_id = get_option('telegram_chat_id');

//             // Відправляємо повідомлення в телеграм
//             $text = $message;
//             $parse_mode = 'Markdown';

//             $api_url = "https://api.telegram.org/bot$bot_token/sendMessage";
//             $data = [
//                 'chat_id' => $chat_id,
//                 'text' => $text,
//                 'parse_mode' => $parse_mode
//             ];

//             $options = [
//                 'http' => [
//                     'method' => 'POST',
//                     'header' => 'Content-Type: application/x-www-form-urlencoded',
//                     'content' => http_build_query($data)
//                 ]
//             ];

//             $context = stream_context_create($options);
//             $result = file_get_contents($api_url, false, $context);

//         }
//     }
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data)) {
        $message = "*Form:* " . $data['formname'] . "\n\n";
        $atLeastOneChecked = false;

        foreach ($data as $name => $value) {
            $checked = in_array($name, get_option('my_plugin_settings_forms', array()));

            if ($checked) {
                $atLeastOneChecked = true;
                $name = sanitize_text_field($name);
                $value = sanitize_text_field($value);
                
                
                if($name === 'productName'){
                    $value = $data['productName'];
                }
                
                $message .= "*$name*: $value\n";
            }
        }

        if ($atLeastOneChecked) {
            $bot_token = get_option('telegram_bot_token');
            $chat_id = get_option('telegram_chat_id');

            $text = $message;
            $parse_mode = 'Markdown';

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


