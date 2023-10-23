document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach((form, index) => {
        postData(form, index);
    })
    
    function postData(form, index){
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            let name = '';
            let price = '';
            try{name =  form.querySelector('.form__title').textContent;}catch(e){}
            try{price = form.querySelector('.order-total').textContent;}catch(e){}
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

            await sendData(data);

            form.reset();
            
        });
        
    }

    async function sendData(data) {
        try {
            await fetch('/plugins/notificationsfortelegram/notificationsfortelegram.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

        } catch (error) {
            console.error(error);
        }
    }
});
