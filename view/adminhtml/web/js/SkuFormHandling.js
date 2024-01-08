let exportButton = document.getElementById('exportCsvBtn');
exportButton.addEventListener("click", exportCsv);

let fileInput = document.getElementById('fileInput');
let loadFileBtn = document.getElementById('loadFileBtn');
let errorMessageContainer = document.getElementById('errorMessageContainer');

loadFileBtn.addEventListener("click", loadCsv);

function exportCsv() {
    var csvData = getTableDataAsCsv();
    var link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvData);
    link.download = 'products_sku.csv';
    link.click();
}

function getTableDataAsCsv() {
    const rows = document.getElementsByTagName("tbody")[0].children;
    let csv = [];
    for (var i = 0; i < rows.length; i++) {
        let rowData = [];
        let cells = rows[i].querySelectorAll('td');
        for (let j = 0; j < cells.length - 1; j++) {
            rowData.push(cells[j].textContent.trim());
        }
        csv.push(rowData.join(','));
    }
    return csv.join('\n');
}

function loadCsv() {
    var fileInput = document.getElementById('fileInput');
    var errorMessageContainer = document.getElementById('errorMessageContainer');

    var file = fileInput.files[0];

    if (file) {
        var reader = new FileReader();

        reader.onload = function (e) {
            try {
                var csvContent = e.target.result;
                parseCsvAndUpdateFields(csvContent);
                errorMessageContainer.style.display = 'none';
            } catch (error) {
                errorMessageContainer.textContent = "There is an error in the imported file.";
                errorMessageContainer.style.display = 'block';
            }
        };

        reader.readAsText(file);
    } else {
        errorMessageContainer.textContent = "Please select a file first.";
        errorMessageContainer.style.display = 'block';
    }
}
function parseCsvAndUpdateFields(csvContent) {
    var rows = csvContent.split('\n');

    for (var i = 0; i < rows.length; i++) {
        var values = rows[i].split(',');
        var productId = values[0].trim();
        var newSku = values[values.length - 1].trim();

        var inputField = document.querySelector('input[name="sku[' + productId + ']"]');
        if (inputField) {
            inputField.value = newSku;
        }
    }
}