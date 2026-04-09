const filePicker = document.getElementById('file-picker');
const form = document.getElementById('add-media');

filePicker.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        form.submit();
    }
});