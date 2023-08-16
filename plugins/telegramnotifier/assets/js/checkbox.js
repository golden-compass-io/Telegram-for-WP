document.addEventListener('DOMContentLoaded', () => {
    const parentCheckbox = document.querySelectorAll('.parentCheckbox');
   
    
    parentCheckbox.forEach(parent => {
        parent.addEventListener('change', (e) => {
            const mainParent = e.target.closest('.mainWrapper');
            const childCheckboxes = mainParent.querySelectorAll('.childCheckbox');
            const isChecked = parent.checked;
            childCheckboxes.forEach(childCheckbox => {
                childCheckbox.checked = isChecked;
                childCheckbox.disabled = !isChecked;
            });
        });
    })
    
});
