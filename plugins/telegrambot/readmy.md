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
        1. Add class checkout to all your forms in which you want to take data !!! You need to add the class exactly to the form tag: <form class="checkout"></form>
        2. Add class form__title to the tag from which the name of your form will be taken
        3. Your forms should be in your theme's index.php file
        4. Create a javascript file in your theme and paste this code there, after connecting the file to functions.php of your theme
```
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.checkout');
            
            forms.forEach(item => {
                postData(item);
            })
            
            function postData(form){
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const name = form.querySelector('.form__title').textContent;

                    let formName = {'formname' : name};
                    const formData = new FormData(form);
            
                    const object = {};
                    formData.forEach(function(value, key){
                        object[key] = value;
                    });
                    fetch('/plugins/telegrambot/telegrambot.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({...object, ...formName})
            
                    }).then(data => {
                        console.log(data);
                    }).catch((data) => {
                        console.log(data);
                    }).finally(() => {
                        form.reset();
                    });
                    
                    
                });
            }
        });
```
        
# 3) Go to wp-admin -> plugin -> the Setting Fields tab, and select which data you want to receive in Telegram. The name of the fields is extracted from the name attribute in the input, if they are not there, you will not see anything