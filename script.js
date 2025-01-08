function convertFile() {
    const fileInput = document.getElementById('fileInput');
    const inputFormat = document.getElementById('inputFormat').value;
    const outputFormat = document.getElementById('outputFormat').value;
    const downloadLink = document.getElementById('downloadLink');

    if (!fileInput.files.length) {
        alert('Please upload a file first!');
        return;
    }

    if (inputFormat === outputFormat) {
        alert('Input and output formats cannot be the same!');
        return;
    }

    const fileName = fileInput.files[0].name.replace(`.${inputFormat}`, `.${outputFormat}`);
    downloadLink.href = URL.createObjectURL(fileInput.files[0]);
    downloadLink.download = fileName;
    downloadLink.style.display = 'block';
    downloadLink.innerText = 'Download Converted File';
}
