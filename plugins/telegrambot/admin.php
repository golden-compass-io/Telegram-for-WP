<?php
    // Додаємо пункт меню 'Telegram Bot'
    add_action('admin_menu', 'register_telegram_bot_menu');
    function register_telegram_bot_menu(){
        add_menu_page('Telegram Bot', 'Telegram Bot', 'manage_options', 'telegram_bot_settings', 'telegram_bot_settings_page', 'dashicons-megaphone');
    }

    // функція зворотного визову для сторінки налаштувань плагіна:
    function telegram_bot_plugin_settings_callback() {
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('telegram_bot_plugin_settings');
                do_settings_sections('telegram_bot_plugin_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // -----------------Для токена та ID ----------
    // Додайте поля для введення токена та ID чату на сторінці налаштувань:
    function telegram_bot_plugin_init_settings_general() {
        add_settings_section(
            'telegram_bot_plugin_general_section',
            '',
            '',
            'telegram_bot_plugin_settings_general'
        );
    
        add_settings_field(
            'telegram_bot_token',
            'Telegram Bot Token',
            'telegram_bot_token_callback',
            'telegram_bot_plugin_settings_general',
            'telegram_bot_plugin_general_section'
        );
    
        add_settings_field(
            'telegram_chat_id',
            'Telegram Chat ID',
            'telegram_chat_id_callback',
            'telegram_bot_plugin_settings_general',
            'telegram_bot_plugin_general_section'
        );
    
        register_setting('telegram_bot_plugin_settings_general', 'telegram_bot_token');
        register_setting('telegram_bot_plugin_settings_general', 'telegram_chat_id');
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



    // ------для файлов----

    function display_php_files_list() {
        $theme_folder = get_template_directory();
        $selected_files = get_option('my_plugin_settings_files', array());

        if (empty($selected_files)) {
            $selected_files = array('index.php');
        }
    
        if ($theme_folder) {
            $php_files = array();
            $files = scandir($theme_folder);
    
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $file_path = $theme_folder . '/' . $file;
                    $form_html = file_get_contents($file_path);
    
                    $dom = new DOMDocument();
                    @$dom->loadHTML($form_html);
    
                    $xpath = new DOMXPath($dom);
                    $forms = $xpath->query("//form[contains(@class, 'checkout')]");
                    
                    // If form found in the current PHP file, add it to the list
                    if ($forms->length > 0) {
                        $php_files[] = $file;
                    }
                }
            }
    
            foreach ($php_files as $php_file) {
                ?>
                <input type="checkbox" name="my_plugin_settings_files[]" value="<?php echo esc_attr($php_file); ?>" <?php checked(in_array($php_file, $selected_files)); ?> /> <?php echo esc_html($php_file); ?><br>
                <?php
            }
        } else {
            echo 'Theme folder not found.';
        }
    }
    
    
    function telegram_bot_plugin_init_settings_file() {
        add_settings_section(
            'telegram_bot_plugin_file_section',
            'Select which pages your forms are on',
            'display_php_files_list',
            'telegram_bot_plugin_settings_file'
        );  
        
        register_setting('telegram_bot_plugin_settings_file', 'my_plugin_settings_files');
    }
    add_action('admin_init', 'telegram_bot_plugin_init_settings_file');
    

    
  


    // -----------------Для полей ----------

    // Функция для обработки атрибутов name и вывода их в административной панели
    function my_plugin_field_settings_callback() {
        // Получаем путь к папке активной темы
        $theme_folder = get_template_directory(); // Или get_stylesheet_directory(), в зависимости от вашего случая
        // Например, если вы хотите получить содержимое файла index.php
        $selected_php_files = get_option('my_plugin_settings_files', array());

        if (empty($selected_php_files)) {
            $selected_php_files = array('index.php');
        }

        foreach ($selected_php_files as $php_file) {
            $file_path = $theme_folder . '/' . $php_file;

            if (file_exists($file_path)) {
                $form_html = file_get_contents($file_path);

                $dom = new DOMDocument();
                @$dom->loadHTML($form_html);

                $xpath = new DOMXPath($dom);
                $forms = $xpath->query("//form[contains(@class, 'checkout')]");

        
                foreach ($forms as $form) {
                        // Получаем заголовок формы с классом .form__title
                    $title = $xpath->query(".//*[contains(@class, 'form__title')]", $form);
                    if ($title->length > 0) {
                        $form_name = $title->item(0)->textContent;
                    }

                    // Выполняйте дополнительные действия с каждой найденной формой
                    $inputs = $xpath->query(".//input[@name]", $form);
                    foreach ($inputs as $input) {
                        // Действия с каждым найденным input в форме
                        $name = $input->getAttribute('name');
                        $inputData[$form_name][] = $name; // Добавляем атрибуты <input> в массив, сгруппированные по заголовку формы
                    }
                
                    // Обрабатываем элементы textarea
                    $textareas = $xpath->query(".//textarea[@name]", $form);
                    foreach ($textareas as $textarea) {
                        // Действия с каждым найденным textarea в форме
                        $name = $textarea->getAttribute('name');
                        if (!isset($inputData[$form_name])) {
                            $inputData[$form_name] = array();
                        }
                        $inputData[$form_name][] = $name; // Добавляем атрибуты <textarea> в массив
                    }
                }
            } else {
            }
            // Обработка случая, если файл не найден.
        }


        // Выводим данные input, сгруппированные по заголовку формы
        foreach ($inputData as $form_name => $input_names) {
            echo '<h3 style="font-size: 14px;">' . 'Form title: ' . esc_html($form_name) . '</h3>'; 
            foreach ($input_names as $inputName) {
                // Выводим checkbox для каждого элемента в массиве $input_names
                ?>
                <input type="checkbox" name="my_plugin_settings_fields[]" value="<?php echo esc_attr($inputName); ?>" <?php checked(in_array($inputName, get_option('my_plugin_settings_fields', array()))); ?> /> <?php echo esc_html($inputName); ?><br>
                <?php
            }
        }

    }




    // // Добавляем настройки поля в раздел Field Settings
    function telegram_bot_plugin_init_settings_field() {
        add_settings_section(
            'telegram_bot_plugin_field_section',
            'Choose which fields to send to Telegram Bot:',
            'my_plugin_field_settings_callback',
            'telegram_bot_plugin_settings_field'
        );  
          
        register_setting('telegram_bot_plugin_settings_field', 'my_plugin_settings_fields');
    }
 add_action('admin_init', 'telegram_bot_plugin_init_settings_field');
    




    // Сторінка налаштувань 'Telegram Bot'
    function telegram_bot_settings_page() {
        ?>
        <div class="wrap">
            <h1>Telegram Bot Settings</h1>
            <h2 class="nav-tab-wrapper">
                <a href="#general-settings" class="nav-tab nav-tab-active">Bot Settings</a>
                <a href="#file-settings" class="nav-tab">Setting PHP Files</a>
                <a href="#field-settings" class="nav-tab">Setting Fields</a>
            </h2>
    
            <!-- Вміст 1 табу - General Settings -->
            <div id="general-settings" class="tab-content">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('telegram_bot_plugin_settings_general');
                    do_settings_sections('telegram_bot_plugin_settings_general');
                    ?>
                    <?php submit_button(); ?>
                </form>
            </div>
    
            <!-- Вміст 2 табу - Field Settings -->
            <div id="file-settings" class="tab-content" style="display:none;">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('telegram_bot_plugin_settings_file');
                    do_settings_sections('telegram_bot_plugin_settings_file');
                    ?>
                    <?php submit_button(); ?>
                </form>
            </div>

            <!-- Вміст 3 табу - Field Settings -->
            <div id="field-settings" class="tab-content" style="display:none;">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('telegram_bot_plugin_settings_field');
                    do_settings_sections('telegram_bot_plugin_settings_field');
                    ?>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function(){
                const tabs = document.querySelectorAll('.nav-tab-wrapper a');
                const tabContents = document.querySelectorAll('.tab-content');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e){
                        e.preventDefault();

                        // Видаляємо активний клас з усіх вкладок
                        tabs.forEach(tab => tab.classList.remove('nav-tab-active'));

                        // Приховуємо вміст всіх табів
                        tabContents.forEach(content => content.style.display = 'none');

                        // Показуємо вміст табу, який вибрав користувач
                        const targetTab = tab.getAttribute('href');
                        document.querySelector(targetTab).style.display = 'block';

                        // Додаємо активний клас до обраної вкладки
                        tab.classList.add('nav-tab-active');
                    });
                });
            });
        </script>
        <?php
    }

?>