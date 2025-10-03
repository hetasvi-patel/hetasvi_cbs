let jsonData = [];
let editIndex = -1;
let deleteData = [];
let detailIdLabel="";
const tableHead = document.getElementById("tableHead");
const tableBody = document.getElementById("tableBody");
const form = document.getElementById("popupForm");
const modalDialog = document.getElementById("modalDialog");
const modal = new bootstrap.Modal(modalDialog);

function getSearchArray() {
    document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
        let rowData = {};
        if(!row.classList.contains("norecords")) {
            rowData[row.dataset.label]=row.dataset.id;
            detailIdLabel=row.dataset.label;
            editIndex++;
            row.querySelectorAll("td[data-label]").forEach(td => {
                if(!td.classList.contains("actions")){
                    if(td.dataset.value!="")
                        rowData[td.dataset.label] = td.dataset.value;
                    else
                        rowData[td.dataset.label] = td.innerText;
                }
            });
            rowData["detailtransactionmode"]="U";
            jsonData[editIndex]=rowData;
        }
        window.jsonData = jsonData;
    });
}
function openModal(index = -1) {
    if (index >= 0) {
        editIndex = index;
        const data = jsonData[index];
        
        for (let key in data) {
            const inputFields = form.elements.namedItem(key);
            if (!inputFields) continue;
            let inputs;
            //console.log("Inputs for key:", key, JSON.stringify(inputFields));
            if (
                inputFields instanceof RadioNodeList ||
                inputFields instanceof HTMLCollection ||
                inputFields instanceof NodeList ||
                (Array.isArray(inputFields) && inputFields.length)
            ) {
                inputs = Array.from(inputFields);
            } else {
                inputs = [inputFields];
            }
            
            if(inputs.length > 1) {
                inputs.forEach(inputField => {
                    if (!inputField) return;
                    if (inputField.type == "checkbox" || inputField.type == "radio") {
                        if (inputField.value!="" && inputField.value == data[key]) {
                            inputField.checked = true;
                            jQuery(inputField).attr("checked", "checked");
                        } else {
                            inputField.checked = false;
                            jQuery(inputField).removeAttr("checked");
                        }
                    } 
                    else if(inputField.type === "hidden" && inputField.classList.contains("chk")) {
                    }
                    else  {
                        inputField.value = data[key];
                    }
                });
            } else {
                inputs[0].value = data[key];
            }
        }
    } else {
        editIndex = -1;
        clearForm(form);
    }
    modal.show();
    setTimeout(() => {
        const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close), select, textarea");
        if (firstInput) firstInput.focus();
    }, 10);
}
function saveData() { 
    const formData = new FormData(form);
    const newEntry = {};
    const allEntries= {};
    // Convert form data to object (excluding hidden fields)
    for (const [key, value] of formData.entries()) {
        if (!getHiddenFields().includes(key) && getDisplayFields().includes(key)) {
            newEntry[key] = value;
        } 
        if (editIndex >= 0) {
            if(jsonData[editIndex].hasOwnProperty(key)) {
                jsonData[editIndex][key] = value;
            } 
        }
        allEntries[key]=value;
    }
    
    if($("#norecords").length>0) {
        $("#norecords").remove();
    }
    
    if (editIndex >= 0) {
        //jsonData[editIndex] = allEntries;
        updateTableRow(editIndex, newEntry);
        modal.hide();
        Swal.fire({
            icon: "success",
            title: "Updated Successfully",
            text: "The record has been updated successfully!",
            showConfirmButton: true,
            showClass: {
                popup: "" // Disable the popup animation
            },
            hideClass: {
                popup: "" // Disable the popup hide animation
            }
        }).then((result) => {
                setFocustAfterClose();
        });
    } else {
        allEntries["detailtransactionmode"]="I";
        jsonData.push(allEntries);
        appendTableRow(newEntry, jsonData.length - 1);
        modal.hide();
        Swal.fire({
            icon: "success",
            title: "Added Successfully",
            text: "The record has been added successfully!",
            showConfirmButton: true,
            showClass: {
                popup: "" // Disable the popup animation
            },
            hideClass: {
                popup: "" // Disable the popup hide animation
            }
        }).then((result) => {
                if (result.isConfirmed) {
                modal.show();
                setTimeout(() => {
                    const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close)");
                    if (firstInput) firstInput.focus();
                }, 100);
                }
        });
    }
    clearForm(form);
}
function getCheckboxRadioData() {
    let checkboxRadioData = Array.from(form.elements)
        .filter(input => (input.type === "checkbox" || input.type === "radio")  && input.classList.contains("chk"))
        .map(input => input.name);
    return checkboxRadioData;
}
function getMultipleCheckboxRadioData() {
    let selectData = Array.from(form.elements);
    if (selectData instanceof RadioNodeList || selectData instanceof HTMLCollection || selectData instanceof NodeList ||(Array.isArray(selectData) && selectData.length)) {
        selectData=selectData.filter(input => (input.type === "checkbox" || input.type === "radio"))
            .map(input => input.name);
    }
    return selectData;
}
function getSelectFields() {
    let selectData = Array.from(form.elements)
        .filter(input => input.type === "select-one")
        .map(input => input.name);
    return selectData;
}
function getDateFields() {
    let dateData = Array.from(form.elements)
        .filter(input => input.type === "date")
        .map(input => input.name);
    return dateData;
}
function getTextareaFields() {
    let textareaData = Array.from(form.elements)
        .filter(input => input.type === "textarea")
        .map(input => input.name);
    return textareaData;
}
function getNumberFields() {
    let numberData = Array.from(form.elements)
        .filter(input => input.type === "number")
        .map(input => input.name);
    return numberData;
}
function getHiddenFields() {   
    let hiddenFields = Array.from(form.elements)
        .filter(input => input.type === "hidden" && input.classList.contains("exclude-field"))
        .map(input => input.name);

    // Add a static entry
    hiddenFields.push("detailtransactionmode");

    return hiddenFields;
}
function getDisplayFields() {
    let displayFields=[];
    let formElements = Array.from(form.elements);
    formElements.forEach(input => {
        if (input.length && (input.type=="checkbox" || input.type=="radio")) { // Handle RadioNodeList
            for (let element of input) {
                if (element.classList && element.classList.contains("display")) {
                    displayFields.push(input.name);
                    break;
                }
            }
        } else if (input.classList && input.classList.contains("display")) { 
            displayFields.push(input.name);
        }
    });
    return displayFields;
}
function setRowHTML(row,rowData,col) {
    const cell = document.createElement("td");
    cell.setAttribute("data-label", col);
    if(getCheckboxRadioData().includes(col)) {
        if(rowData[col]==1)
            cell.innerHTML='<img src="dist/images/right_icon.png" style="height:15px; width:auto;">';
        else
            cell.innerHTML="";
    } else if(getMultipleCheckboxRadioData().includes(col)) {
       const inputs = form.querySelectorAll("[name='" + col + "']");
        const checkedInputs = Array.from(inputs).filter(input => input.checked);

        // If multiple checkboxes can be checked, join their labels/text
        if (checkedInputs.length > 1) {
            cell.textContent = checkedInputs
                .map(input => input.nextElementSibling ? input.nextElementSibling.textContent : input.value)
                .join(", ");
        }
        // If only one radio/checkbox is expected
        else if (checkedInputs.length === 1) {
            const input = checkedInputs[0];
            cell.textContent = input.nextElementSibling ? input.nextElementSibling.textContent : input.value;
        }
        else {
            cell.textContent = "";
        }
    } else if(getSelectFields().includes(col)) {
        const select = form.querySelector("[name='"+col+"']");
        const selectedOption = Array.from(select.options).find(opt => opt.value === rowData[col]);
        cell.textContent = selectedOption ? selectedOption.text : rowData[col];
    } else if(getDateFields().includes(col)) {
        if(rowData[col]!="")
            cell.textContent = formatDateToDDMMYYYY(rowData[col]);
        else
            cell.textContent="";
    } else if(getTextareaFields().includes(col)) {
        if(rowData[col]!="")
            cell.innerHTML = rowData[col].replace(/\n/g, "<br>");
        else
            cell.textContent="";
    } else if(getNumberFields().includes(col)) {
       const value = Number(rowData[col]);
       const formatted = isNaN(value) ? '' : value.toFixed(2);
       cell.textContent=formatted;
    }       
    else {
        cell.textContent = rowData[col] || "";
    }
    row.appendChild(cell);
}
function appendTableRow(rowData, index) {
    const row = document.createElement("tr");
    var id=0;
    if(detailIdLabel!=""){
        id=rowData[detailIdLabel];
    } 
    row.setAttribute("data-id", id);
      
   addActions(row,index,id);
    Object.keys(rowData).forEach(col => {
        if (!getHiddenFields().includes(col) && getDisplayFields().includes(col))  {
            setRowHTML(row,rowData,col);
        }
    });
         
    tableBody.appendChild(row);
}
function updateTableRow(index, rowData) {
    const row = tableBody.children[index];
    var id=0;
    if(detailIdLabel!=""){
        id=rowData[detailIdLabel];
    } 
    row.innerHTML = "";
    addActions(row,index,id);

    Object.keys(rowData).forEach(col => {
        setRowHTML(row,rowData,col);
    });
}
function addActions(row,index,id) {
    const actionCell = document.createElement("td");
    actionCell.classList.add("actions");
    const editButton = document.createElement("button");
    editButton.textContent = "Edit";
    editButton.classList.add("btn", "btn-info", "btn-sm","me-2", "edit-btn");
    editButton.setAttribute("data-index", index);
    editButton.setAttribute("data-id", id);

    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Delete";
    deleteButton.classList.add("btn", "btn-danger", "btn-sm","delete-btn");
    deleteButton.setAttribute("data-index", index);
    deleteButton.setAttribute("data-id", id);
    
    actionCell.appendChild(editButton);
    actionCell.appendChild(deleteButton);
    row.appendChild(actionCell);
}
function setFocustAfterClose() {
    if(document.getElementById("detailBtn"))
        document.getElementById("detailBtn").focus();
}
modalDialog.addEventListener("hidden.bs.modal", function () {
    clearForm(form);
    setFocustAfterClose();
});
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("edit-btn")) {
        event.preventDefault(); // Stops the required field validation trigger
        const index = event.target.getAttribute("data-index");
        openModal(index);
    }
    if (event.target.classList.contains("delete-btn")) {
        event.preventDefault(); // Stops the required field validation trigger
        const index = event.target.getAttribute("data-index");
        const id = event.target.getAttribute("data-id");
        deleteRow(index,id);
    }
});
function deleteRow(index,id) {
    Swal.fire({
        title: "Are you sure you want to delete this record?",
        text: "You won't be able to revert it!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
        if(id>0) {
            jsonData[index]["detailtransactionmode"]="D";
            deleteData.push(jsonData[index]);
        }
        // Remove the item from the jsonData array
        jsonData.splice(index, 1);
        tableBody.innerHTML = "";
        const numberOfColumns = document.querySelector("table th") ? document.querySelector("table th").parentElement.children.length : 0;
        // Check if there are any rows left
        if (jsonData.length === 0) {
            // If no rows, add a row saying "No records"
            const noRecordsRow = document.createElement("tr");
            for(var i=1; i< numberOfColumns; i++) {
                const noRecordsCell = document.createElement("td");
                if(i==1) {
                    noRecordsCell.colSpan = numberOfColumns;
                    noRecordsCell.textContent = "No records available";
                }
                noRecordsRow.appendChild(noRecordsCell);
            }
            noRecordsRow.setAttribute("id","norecords");
            noRecordsRow.classList.add("norecords"); 
            tableBody.appendChild(noRecordsRow);
        } else {
            // If there are rows left, re-populate the table
            jsonData.forEach((data, idx) => appendTableRow(data, idx));
        }
        }
    });
}
// Expose functions globally
window.openModal = openModal;
window.saveData = saveData;
