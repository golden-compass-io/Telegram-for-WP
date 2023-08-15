<?php

    // Додаємо пункт меню 'Telegram Bot'
    add_action('admin_menu', 'register_telegram_bot_menu');
    function register_telegram_bot_menu(){
        add_menu_page('Telegram Bot', 'Telegram Bot', 'manage_options', 'telegram_bot_settings', 'telegram_bot_plugin_settings_callback', plugins_url( 'assets/images/bot.png', __FILE__ ));
        add_submenu_page('telegram_bot_settings', 'Telegram Bot Settings', 'Telegram Bot Settings', 'manage_options', 'telegram_bot_general_settings', 'telegram_bot_settings_general_page');
        add_submenu_page('telegram_bot_settings', 'Setting Forms and Fields', 'Setting Forms and Fields', 'manage_options', 'telegram_bot_field_settings', 'telegram_bot_field_settings_page');
    }
    

    // функція зворотного визову для сторінки налаштувань плагіна:
    function telegram_bot_plugin_settings_callback() {
        ?>
        <div class="wrap">
            <h1 class="readmy">Usage</h1>
            <h2 class="readmy__title">Create a bot</h2>
            <ol>
                <li>Go to your Telegram, enter @BotFather in the search, and send the <b>/newbot</b>  command to this bot</li>
                <li>Choose a name for your bot.</li>
                <li>Choose a username for your bot. It must end in `bot'. Like, for example,<b>TetrisBot or tetris_bot</b>.</li>
                <li>Copy your token, go to wp-admin in the plugin, and paste the token into the <b>Telegram Bot Token</b> field</li>
                <li>Go to your Telegram, create a group where data from your site will come, and add your bot to the group</li>
                <li>Enter <b>@MyChatInfoBot</b> in the telegram search add it to your group, after that, you will get your Chat ID</li>
                <li>Copy your Chat ID, go to wp-admin in the plugin, and paste in the <b>Telegram Chat ID</b> field</li>
                <li>Click the save changes button</li>
            </ol>

            <h2 class="readmy__title">To go to the next point you need to do some actions</h2>
            <ol>
                <li>Add class <b>form__title</b> to the tag from which the name of your form will be taken</li>
                <li>Paste this code in themes -> your theme folder -> functions.php
                <pre><code>function enqueue_telegrambot_script() {
    $plugin_script_url = plugins_url( 'telegrambot/assets/js/script.js' );

    wp_register_script( 'telegrambot-script', $plugin_script_url, array(), '1.0', true );
    wp_enqueue_script( 'telegrambot-script' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_telegrambot_script' ); </code></pre>
                </li>
            </ol>

            <h2 class="readmy__title">Setting Forms and Fields tab</h2>
                <ol>
                    <li>Go to wp-admin -> plugin -> the Setting Forms and Fields tab</li>
                    <li>Select in which forms which fields you want to receive in Telegram</li>
                    <div><span style="color: #D50000; margin-left:-16px">!!!</span> The name of the fields is extracted from the name attribute in the input, if they are not there, you will not see anything <span  style="color: #D50000;">!!!</span></div>
                </ol> 
            
        </div>
        <?php
    }


    // -----------------Для токена та ID ----------
    // Додайте поля для введення токена та ID чату на сторінці налаштувань:
    function telegram_bot_plugin_init_settings_general() {
        add_settings_section(
            'telegram_bot_settings_general_section', // Ідентифікатор секції
            'Еnter the data',
            '', // Функція зворотного виклику для виводу опису секції (можна залишити порожньою)
            'telegram_bot_plugin_settings_general' // Ідентифікатор сторінки
        );
    
        // Додавання полів
        add_settings_field(
            'telegram_bot_token', // Ідентифікатор поля
            'Telegram Bot Token', // Назва поля
            'telegram_bot_token_callback', // Функція зворотного виклику для виводу поля
            'telegram_bot_plugin_settings_general', // Ідентифікатор сторінки
            'telegram_bot_settings_general_section' // Ідентифікатор секції
        );
    
        add_settings_field(
            'telegram_chat_id', // Ідентифікатор поля
            'Telegram Chat ID', // Назва поля
            'telegram_chat_id_callback', // Функція зворотного виклику для виводу поля
            'telegram_bot_plugin_settings_general', // Ідентифікатор сторінки
            'telegram_bot_settings_general_section' // Ідентифікатор секції
        );
    
        register_setting(
            'telegram_bot_plugin_settings_general', // Ідентифікатор групи налаштувань
            'telegram_bot_token' // Ідентифікатор поля
        );
        register_setting(
            'telegram_bot_plugin_settings_general', // Ідентифікатор групи налаштувань
            'telegram_chat_id' // Ідентифікатор поля
        );
    }
    add_action('admin_init', 'telegram_bot_plugin_init_settings_general');
    

    function telegram_bot_token_callback() {
        $value = get_option('telegram_bot_token', '');
        echo '<input type="text" name="telegram_bot_token" value="' . esc_attr($value) . '" />';
    }

    function telegram_chat_id_callback() {
        $value = get_option('telegram_chat_id', '');
        echo '<input type="text" name="telegram_chat_id" value="' . esc_attr($value) . '" />';
    }

    // -----------------Для полей ----------

    function my_plugin_field_settings_callback() {

        $theme_folder = get_template_directory();
        $selected_fields = get_option('my_plugin_settings_field', array());
        $php_files = scandir($theme_folder);

        foreach ($php_files as $php_file) {
            $file_path = $theme_folder . '/' . $php_file;
        
            if (pathinfo($php_file, PATHINFO_EXTENSION) === 'php') {
                $form_html = file_get_contents($file_path);
        
                $dom = new DOMDocument();
                @$dom->loadHTML($form_html);
        
                $xpath = new DOMXPath($dom);
                $forms = $xpath->query("//form");
                $formsLength = $forms->length;

                $i = 0;
                foreach ($forms as $form) {
                    $title = $xpath->query(".//*[contains(@class, 'form__title')]", $form);
                    $price = $xpath->query(".//*[contains(@class, 'order-total')]", $form);
                    $product_name = $xpath->query(".//*[contains(@class, 'product-name')]", $form);

                    if ($title->length > 0) {
                        $form_name = $title->item(0)->textContent;
                    }
                   
                    $is_form_checked = in_array($form_name, $selected_fields) ? 'checked' : '';
                    $is_form_checked_attribute = $is_form_checked === 'checked' ? 'checked' : '';

                    ?>
                    <div class="mainWrapper">
                        <input class="parentCheckbox" style="margin-bottom: 5px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($form_name); ?>" <?php echo $is_form_checked_attribute; ?> />
                        <label style="font-size: 15px; margin-bottom: 10px; display: inline-block;"><strong><?php echo 'Form: ' . esc_html($form_name); ?></strong></label><br>

                        <?php

                        if ($price->length > 0) {
                            $price_name = 'total_price';
                            $dynamic_price_name = $price_name . "_$i"; // Додайте динамічний індекс
                            $is_price_checked = in_array($dynamic_price_name, $selected_fields) ? 'checked' : '';
                            if($is_form_checked == ''){
                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_price_name); ?>" <?php echo $is_price_checked; ?>/> total price<br>
                                <?php
                            }else{
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_price_name); ?>" <?php echo $is_price_checked; ?>/> total price<br>
                                <?php
                            } 
                        }

                        if ($product_name->length > 0) {
                            $product= 'product_name';
                            $dynamic_product_name = $product . "_$i"; // Додайте динамічний індекс
                            $is_field_checked = in_array($dynamic_product_name, $selected_fields) ? 'checked' : '';
                            if($is_form_checked == ''){

                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_product_name); ?>" <?php echo $is_field_checked; ?>/> product name<br>
                                <?php
                            }else{
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_product_name); ?>" <?php echo $is_field_checked; ?>/> product name<br>
                                <?php
                            } 
                        }

                        $inputs = $xpath->query(".//input[@name]", $form);
                        foreach ($inputs as $input) {
                            $input_name = $input->getAttribute('name');
                            $dynamic_input_name = $input_name . "_$i"; // Додайте динамічний індекс
                            $is_input_checked = in_array($dynamic_input_name, $selected_fields) ? 'checked' : '';

                            if ($is_form_checked == '') {
                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_input_name); ?>" <?php echo $is_input_checked; ?> /> <?php echo esc_html($input_name); ?><br>
                                <?php
                            } else {
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_input_name); ?>" <?php echo $is_input_checked; ?> /> <?php echo esc_html($input_name); ?><br>
                                <?php
                            }
                        }

                        $textareas = $xpath->query(".//textarea[@name]", $form);
                        foreach ($textareas as $textarea) {
                            $textarea_name = $textarea->getAttribute('name');
                            $dynamic_textarea_name = $textarea_name . "_$i"; 
                            $is_textarea_checked = in_array($dynamic_textarea_name, $selected_fields) ? 'checked' : '';
                            if($is_form_checked == ''){
                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_textarea_name); ?>" <?php echo $is_textarea_checked; ?> /> <?php echo esc_html($textarea_name); ?><br>
                                <?php
                            }else{
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_field[]" value="<?php echo esc_attr($dynamic_textarea_name); ?>" <?php echo $is_textarea_checked; ?> /> <?php echo esc_html($textarea_name); ?><br>
                                <?php
                            }
                        }
                        $checkbox = get_option('my_plugin_settings_form', array());
                        $name_without_number = preg_replace('/_\d+$/', '', $checkbox);
                    
                        print_r($checked);
                        
                    ?>
                    </div>
                    <br><br>
                    <?php
                    $i++;
                }

            }
        }
    }



    // // Добавляем настройки поля в раздел Field Settings
    function telegram_bot_plugin_init_settings_field() {
        add_settings_section(
            'telegram_bot_plugin_field_section',
            'Select which forms and fields will be sent to telegram:',
            'my_plugin_field_settings_callback',
            'telegram_bot_plugin_settings_field'
        );  
          

        register_setting('telegram_bot_plugin_settings_field', 'my_plugin_settings_field');
        // register_setting('telegram_bot_plugin_settings_field', 'my_plugin_settings_field');
    }
    add_action('admin_init', 'telegram_bot_plugin_init_settings_field');
    




    // Сторінка налаштувань 'Telegram Bot'
    function telegram_bot_settings_general_page() {
        ?>
        <div class="wrap">
            <h1 style="margin-bottom:40px;">Telegram Bot Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('telegram_bot_plugin_settings_general');
                do_settings_sections('telegram_bot_plugin_settings_general');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }


    // Сторінка налаштувань 'Setting Forms and Fields'
    function telegram_bot_field_settings_page() {
        ?>
        <div class="wrap">
            <h1 style="margin-bottom:40px;">Setting Forms and Fields</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('telegram_bot_plugin_settings_field');
                do_settings_sections('telegram_bot_plugin_settings_field');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

// ...
?>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const tabs = document.querySelectorAll('.nav-tab-wrapper a');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e){
                e.preventDefault();

                // Видаляємо активний клас з усіх вкладок
                tabs.forEach(tab => tab.classList.remove('nav-tab-active'));

                // Додаємо активний клас до обраної вкладки
                tab.classList.add('nav-tab-active');
                
                // Отримуємо ідентифікатор сторінки
                const pageId = tab.getAttribute('href').substring(1);
                
                // Перенаправляємо на відповідну сторінку
                window.location.href = `admin.php?page=${pageId}`;
            });
        });
    });
</script>
<?php
?>