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
    
            }).then()
            .catch()
            .finally(() => {
                form.reset();
            });
            
            
        });
    }
});
