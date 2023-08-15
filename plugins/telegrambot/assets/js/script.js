document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.checkout'),
        modal = document.querySelector('.modal');

    function closeModal(){
        modal.classList.add('hide');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    forms.forEach(item => {
        postData(item);
    })
    
    function postData(form){
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const name = form.querySelector('.form__title').textContent;
            const price = form.querySelector('.order-total').textContent;
            const productName = form.querySelectorAll('.product-name');

              
            const formName = {'formname' : name};
            const formData = new FormData(form);
    
            const object = {};
            formData.forEach(function(value, key){
                object[key] = value;
            });


            // price && productName
            if(price && productName){
                const totalprice = {'totalprice' : price};

                const productList = {
                    "productName": []
                };
                productName.forEach((product, i) => {
                    productList["productName"] += `${product.textContent}, `;
                });
                
                
                fetch('/plugins/telegrambot/telegrambot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({...object, ...formName, ...productList, ...totalprice})
        
                }).then(data => {
                    console.log(data);
                })
                .catch(data => {
                    console.log(data);
                })
                .finally(() => {
                    form.reset();
                    closeModal();
                });
            // productName
            }else if(productName){
               const productList = {
                    "productName": []
                };
                productName.forEach((product, i) => {
                    productList["productname"] =+ product.textContent;
                });

                fetch('/plugins/telegrambot/telegrambot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({...object, ...formName, ...productList,})
        
                }).then(data => {
                    console.log(data);
                })
                .catch(data => {
                    console.log(data);
                })
                .finally(() => {
                    form.reset();
                    closeModal();
                });
            }else if(price){
                const totalprice = {'totalprice' : price};
                fetch('/plugins/telegrambot/telegrambot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({...object, ...formName, ...totalprice,})
        
                }).then(data => {
                    console.log(data);
                })
                .catch(data => {
                    console.log(data);
                })
                .finally(() => {
                    form.reset();
                    closeModal();
                });
            }else{
                fetch('/plugins/telegrambot/telegrambot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({...object, ...formName, ...productList, ...totalprice})
        
                }).then(data => {
                    console.log(data);
                })
                .catch(data => {
                    console.log(data);
                })
                .finally(() => {
                    form.reset();
                    closeModal();
                });
            }
          


            
            
        });
    }
});
