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


    let counter = parseInt(localStorage.getItem('counter'), 10) || 1; // За замовчуванням 1, якщо в локальному сховищі немає збереженого значення
    
    function postData(form, index){
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = form.querySelector('.form__title').textContent;
            const price = form.querySelector('.order-total').textContent;
            const products = form.querySelectorAll('.product-name');

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

            if (products) {
                products.forEach((product, i) => {
                    if (!data[`products_${index}`]) {
                        data[`products_${index}`] = '';
                    }
                    data[`products_${index}`] += `,${product.textContent}`;
                    
                });
            }
            data['counter'] = counter;

            await sendData(data);

            form.reset();
            closeModal();
            console.log(data);

            counter ++;
            localStorage.setItem('counter', counter); // Збереження counter у локальному сховищі
            
        });
        
    }

    async function sendData(data) {
        try {
            const response = await fetch('/plugins/telegramnotifier/telegramnotifier.php', {
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
