function getDefaultDate(companyYearDetails) {
    const today = new Date();
    let defaultDate = new Date(today);
    const startDate = new Date(companyYearDetails.start_date);
    const endDate = new Date(companyYearDetails.end_date);
    defaultDate.setFullYear(startDate.getFullYear());
    if (defaultDate.getDate() !== today.getDate()) {
        defaultDate = new Date(defaultDate.getFullYear(), defaultDate.getMonth() + 1, 0);
    }
    if (defaultDate < startDate) {
        defaultDate = startDate;
    } else if (defaultDate > endDate) {
        defaultDate = endDate;
    }
    return defaultDate.toISOString().split('T')[0];
}
function isDate(value) {
  return Object.prototype.toString.call(value) === '[object Date]';
}
function getMaxDate(inputName,dateObj) {
    let maxAllowedDate=null;
    if(inputName=="billing_till_date") {
        maxAllowedDate=new Date( $('#invoice_date').val());
        maxAllowedDate=maxAllowedDate.toISOString().split('T')[0];
    } else {
        maxAllowedDate=new Date(dateObj.end_date);
        maxAllowedDate=maxAllowedDate.toISOString().split('T')[0];
    }
    return maxAllowedDate;
}
function getMinDate(inputName,dateObj){
    let minAllowedDate = null;
    if(inputName=="invoice_date") {
        minAllowedDate=new Date(dateObj.start_date);
    }
    else if(inputName!="billing_till_date"){
        minAllowedDate = getDefaultDate(dateObj);
        if(!isDate(minAllowedDate))
            minAllowedDate=new Date(minAllowedDate);
        if(minAllowedDate)
            minAllowedDate.setDate(minAllowedDate.getDate() - 30);
    }
    return minAllowedDate;
}
function setDefaultDates(inputName,companyYearDetails) {
    const dateInput = $('#'+inputName);
    //const billingStartsFromInput = $('#billing_starts_from');
    const formattedDate = getDefaultDate(companyYearDetails);
    if (dateInput.val() === '') {
        dateInput.val(formattedDate);
    }
   /* if (billingStartsFromInput && billingStartsFromInput.val() === '') {
        billingStartsFromInput.val(formattedDate);
    }*/
   if(inputName!="billing_till_date"){
    const minDate=getMinDate(inputName,companyYearDetails);
    if(minDate)
        document.getElementById(inputName).min = formatDate(minDate);
   }
    const maxDate=getMaxDate(inputName,companyYearDetails);
    if(maxDate)
        document.getElementById(inputName).max = formatDate(new Date(maxDate));

    validateDate(inputName,companyYearDetails);
}
function validateDate(inputName,companyYearDetails) {
    let autoFilledDate=null;
    const dateInput = $('#'+inputName);
    const nextSibling=dateInput.nextElementSibling;
    let errorContainer=null
    if(nextSibling && nextSibling.classList.contains("invalid-feedback")) {
        errorContainer =nextSibling;
        errorContainer.text('');
    }
        
    const dateVal = dateInput.val();
    dateInput.removeClass('is-invalid');
    
    if (!dateVal) {
        showError(dateInput,errorContainer,'Invalid Date');
        return false;
    }
    const selectedDate = new Date(dateVal);
    if (isNaN(selectedDate.getTime())) {
        showError(dateInput,errorContainer,'Invalid Date');
        return false;
    }
    
    autoFilledDate=getMaxDate(inputName,companyYearDetails);
    
    selectedDate.setHours(0, 0, 0, 0);
    const autoFilledDateObj = new Date(autoFilledDate);
    autoFilledDateObj.setHours(0, 0, 0, 0);

    if(inputName!="billing_till_date") {
        const minAllowedDate=getMinDate(inputName,companyYearDetails);
        let minDate=minAllowedDate.toISOString().split('T')[0];
        minDate=formatDateToDDMMYYYY(minDate);
        if (selectedDate < minAllowedDate) {
            showError(dateInput,errorContainer,`Date below (${minDate}) is not allowed`);
            return false;
        }
    }
    const maxDate=formatDateToDDMMYYYY(autoFilledDate);
    
    if (selectedDate > autoFilledDateObj) {
        showError(dateInput,errorContainer,`Date above (${maxDate}) is not allowed`);
        return false;
    }
   /* if($('#billing_starts_from')) {
      $('#billing_starts_from').val(dateVal);
    }*/
    return true;
}
function setSequence(seqInput,numberInput,financialYear,isInvoice=null) {
  const sequenceInput = document.getElementById(seqInput);
    const noInput = document.getElementById(numberInput);
    const noHidden = document.getElementById("hid_"+numberInput);
    if (sequenceInput && noInput) {
        sequenceInput.addEventListener("input", function () {
            let sequence = this.value.padStart(4, '0');
            if(isInvoice) {
                const invoiceType = document.querySelector('input[name="invoice_type"]:checked')?.value || null;
                if(invoiceType && parseInt(invoiceType)===2)
                    sequence='TS' + sequence;
                else if(invoiceType && parseInt(invoiceType)===3)
                    sequence='ES' + sequence;
            }
            noInput.value = sequence + '/' + financialYear;
            if (noHidden) {
                noHidden.value = noInput.value;
            }
        });
    }
}
// gross input calculation
if(document.getElementById("gross_wt") && document.getElementById("tare_wt") && document.getElementById("net_wt")) {
    const grossInput = document.getElementById('gross_wt');
    const tareInput = document.getElementById('tare_wt');
    const netInput = document.getElementById('net_wt');
    const netInputHid = document.getElementById('hid_net_wt');
    function updateNetWt() {
        const grossWeight = parseFloat(grossInput && grossInput.value) || 0;
        const tareWeight= parseFloat(tareInput && tareInput.value) || 0;
        let netWeight = grossWeight - tareWeight;
        if (netWeight < 0) netWeight = 0;
        if (netInput) netInput.value = netWeight.toFixed(2);
        if (netInputHid) netInputHid.value = netWeight.toFixed(2);
    }
    if (grossInput && tareInput && netInput) {
        grossInput.addEventListener('input', updateNetWt);
        tareInput.addEventListener('input', updateNetWt);
        updateNetWt();
    }
}
function formatNumber(number) {
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(number);
}
function getStorageDurationMultiplierById(id) {
    switch (Number(id)) {
        case 1: return 1 / 30;          // Daily
        case 2: return 1 / 4;           // Weekly (4 weeks per month)
        case 3: return 1 / 2;           // Fortnightly (2 per month)
        case 4: return 1;               // Monthly
        case 5: return 1 + (1 / 30);    // 1 Month 1 Day
        case 6: return 1 + (7 / 30);    // 1 Month 7 Days
        case 7: return 1 + (15 / 30);   // 1 Month 15 Days
        case 8: return 2;               // 2 Months
        case 9: return 1;               // Seasonal (not used here)
        case 10: return 0;              // No Billing
        default: return 1;
    }
}
/* ADDED BY BHUMITA ON 06/09/2025 */
function getRentPerStorageDuration(storageDurationId, rentPerMonth) {
    const multiplier = getStorageDurationMultiplierById(storageDurationId);
    const rentStorageDuration = (parseFloat(rentPerMonth) * multiplier).toFixed(2);
    return rentStorageDuration;
}
function getAmountMultiplier(rentPer,qty, avgWeight) {
    let multiplier = 1;
    if (Number(rentPer) === 2) { // Per Kg
        multiplier = (parseFloat(avgWeight) || 0) * (parseInt(qty) || 0);
    } else { // Per Qty
        multiplier = parseInt(qty) || 0;
    }
    return multiplier;
}
/* \ADDED BY BHUMITA ON 06/09/2025 */
/** Convenience checkers for special IDs */
function isSeasonalStorageDurationId(id) {
    return Number(id) === 9;
}
function isNoBillingStorageDurationId(id) {
    return Number(id) === 10;
}

