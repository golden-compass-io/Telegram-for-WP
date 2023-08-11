<?php

    // Додаємо пункт меню 'Telegram Bot'
    add_action('admin_menu', 'register_telegram_bot_menu');
    function register_telegram_bot_menu(){
        add_menu_page('Telegram Bot', 'Telegram Bot', 'manage_options', 'telegram_bot_settings', 'telegram_bot_plugin_settings_callback', 'dashicons-megaphone');
        add_submenu_page('telegram_bot_settings', 'Telegram Bot Settings', 'Telegram Bot Settings', 'manage_options', 'telegram_bot_general_settings', 'telegram_bot_settings_general_page');
        add_submenu_page('telegram_bot_settings', 'Setting Forms and Fields', 'Setting Forms and Fields', 'manage_options', 'telegram_bot_field_settings', 'telegram_bot_field_settings_page');
    }
    

    // функція зворотного визову для сторінки налаштувань плагіна:
    function telegram_bot_plugin_settings_callback() {
        ?>
        <div class="wrap">
            <h1>Readmy</h1>
            
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
        $selected_fields = get_option('my_plugin_settings_forms', array());
        $php_files = scandir($theme_folder);

        foreach ($php_files as $php_file) {
            $file_path = $theme_folder . '/' . $php_file;
        
            if (pathinfo($php_file, PATHINFO_EXTENSION) === 'php') {
                $form_html = file_get_contents($file_path);
        
                $dom = new DOMDocument();
                @$dom->loadHTML($form_html);
        
                $xpath = new DOMXPath($dom);
                $forms = $xpath->query("//form[contains(@class, 'checkout')]");
        
                foreach ($forms as $form) {
                    $title = $xpath->query(".//*[contains(@class, 'form__title')]", $form);
                    
                    if ($title->length > 0) {
                        $form_name = $title->item(0)->textContent;
                    }
        
                    $is_form_checked = in_array($form_name, $selected_fields) ? 'checked' : '';
                    $is_form_checked_attribute = $is_form_checked === 'checked' ? 'checked' : '';

                    ?>
                    <div class="mainWrapper">
                        <input class="parentCheckbox" style="margin-bottom: 5px;" type="checkbox" name="my_plugin_settings_forms[]" value="<?php echo esc_attr($form_name); ?>" <?php echo $is_form_checked; ?> />
                        <label style="font-size: 15px; margin-bottom: 10px; display: inline-block;"><strong><?php echo 'Form: ' . esc_html($form_name); ?></strong></label><br>
                        <?php
        
                        $inputs = $xpath->query(".//input[@name]", $form);
                        foreach ($inputs as $input) {
                            $input_name = $input->getAttribute('name');
                            $is_input_checked = in_array($input_name, $selected_fields) ? 'checked' : '';
                            if($is_form_checked == ''){
                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_forms[]" value="<?php echo esc_attr($input_name); ?>" <?php echo $is_input_checked; ?> /> <?php echo esc_html($input_name); ?><br>
                                <?php
                            }else{
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_forms[]" value="<?php echo esc_attr($input_name); ?>" <?php echo $is_input_checked; ?> /> <?php echo esc_html($input_name); ?><br>
                                <?php
                            }
                            
                        }
                        $textareas = $xpath->query(".//textarea[@name]", $form);
                        foreach ($textareas as $textarea) {
                            $textarea_name = $textarea->getAttribute('name');
                            $is_textarea_checked = in_array($textarea_name, $selected_fields) ? 'checked' : '';
                            if($is_form_checked == ''){
                                ?>
                                <input disabled class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_forms[]" value="<?php echo esc_attr($textarea_name); ?>" <?php echo $is_textarea_checked; ?> /> <?php echo esc_html($textarea_name); ?><br>
                                <?php
                            }else{
                                ?>
                                <input class="childCheckbox" style="margin-left: 20px;" type="checkbox" name="my_plugin_settings_forms[]" value="<?php echo esc_attr($textarea_name); ?>" <?php echo $is_textarea_checked; ?> /> <?php echo esc_html($textarea_name); ?><br>
                                <?php
                            }
                        }
                    ?>
                    </div>
                    <br><br>
                    <?php
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
          
        register_setting('telegram_bot_plugin_settings_field', 'my_plugin_settings_fields');
        register_setting('telegram_bot_plugin_settings_field', 'my_plugin_settings_forms');
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