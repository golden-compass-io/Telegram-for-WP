# 1) Create a bot:
    1. Go to your Telegram, enter @BotFather in the search, and send the /newbot command to this bot
    2. Choose a name for your bot.
    3. Choose a username for your bot. It must end in `bot'. Like, for example, TetrisBot or tetris_bot.
    4. Copy your token, go to wp-admin in the plugin, and paste the token into the Telegram Bot Token field
    5. Go to your Telegram, create a group where data from your site will come, and add your bot to the group
    6. Enter @MyChatInfoBot in the telegram search add it to your group, after that, you will get your Chat ID
    7. Copy your Chat ID, go to wp-admin in the plugin, and paste in the Telegram Chat ID field
    8. Click the save changes button
# 2) To go to the next point you need to do some actions:
        1. Add class form__title to the tag from which the name of your form will be taken
        2. Paste this code in themes -> your theme folder -> functions.php
```    
    <?php
        function enqueue_notificationsfortelegram_script() {
            $plugin_script_url = plugins_url( 'notificationsfortelegram/assets/js/script.js' );
            
            wp_register_script( 'notificationsfortelegram-script', $plugin_script_url, array(), '1.0', true );
            wp_enqueue_script( 'notificationsfortelegram-script' );
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_notificationsfortelegram_script' );
    ?>
```      
# 3) 1. Go to Notifications for telegram -> the Database Settings tab. 2. In the field Server Name, The default is localhost. If it doesn’t fit, go to phpMyAdmin, click on the house icon, and in the right corner there will be a block with the name "database server", in it, there is an item with the name "server", take the value from there 3. In the field User name, enter the database user name 4. In the field Password, enter password, which you set for this user. 5. In the field Database name, enter: database name

# 4) Go to wp-admin -> plugin -> the Setting Forms and Fields tab, and select in which forms which fields you want to receive in Telegram. The name of the fields is extracted from the name attribute in the input, if they are not there, you will not see anything