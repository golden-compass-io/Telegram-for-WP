<?php
/* Plugin name: telegram notifier 
* Description: The "telegram notifier" plugin is a powerful tool that helps you quickly process requests and respond to orders. In this way, you ensure a high level of service and convenience for your audience.
* Version: 1.0
* Author: Golden Compass
* Author URI: https://goldencompass.io/
*/
require_once( plugin_dir_path( __FILE__ ) . 'telegram-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );


// Основна функція
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $servername = get_option('server_name');
    $username = get_option('user_name'); 
    $password = get_option('password'); 
    $databaseName = get_option('database_name');

    $db = mysqli_connect($servername, $username, $password, $databaseName) or die('Помилка');  

    $sql = "CREATE TABLE IF NOT EXISTS `request_counter` (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($db->query($sql) === TRUE) {
        echo "Таблица успешно создана";
    } else {
        echo "Ошибка при создании таблицы: ";
    }

    if (!empty($data)){
        $query = mysqli_query($db, "INSERT INTO `request_counter` (`id`) VALUES (NULL);");

        if ($query) {
            $count_id = mysqli_insert_id($db);
        } else {
            echo "";
        }

        $message = "-----------------------\n<b>Request number: </b>" . $count_id . "\n<i><b>Form:</b> " . $data['formname'] . "</i>\n\n";
        
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


