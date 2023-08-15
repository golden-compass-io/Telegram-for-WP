document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form'),
        modal = document.querySelector('.modal');

    function closeModal(){
        modal.classList.add('hide');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    forms.forEach((form, index) => {
        postData(form, index);
    })
    
    function postData(form, index){
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = form.querySelector('.form__title').textContent;
            const price = form.querySelector('.order-total').textContent;
            const product_name = form.querySelectorAll('.product-name');

            const formData = new FormData(form);

         

            const object = {};
            formData.forEach(function(value, key){
                object[key] = value;
            });

            const data = {};
            for (const key in object) {
                data[`${key}_${index}`] = object[key];
            }

            if (name) {
                data[`formname`] = name;
            }

            if (price) {
                data[`total_price_${index}`] = price;
            }

            if (product_name) {
                product_name.forEach((product, i) => {
                    if (!data[`product_name_${index}`]) {
                        data[`product_name_${index}`] = '';
                    }
                    data[`product_name_${index}`] += `${product.textContent}, `;
                    
                });
            }

            await sendData(data);

            form.reset();
            closeModal();
        });
        
    }

    async function sendData(data) {
        try {
            const response = await fetch('/plugins/telegrambot/telegrambot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            console.log(response);
        } catch (error) {
            console.error(error);
        }
    }
});
