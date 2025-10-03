
/* ADDED BY BHUMITA ON 24/07/2025 */
function isVisible(el) {
    return !!(el && el.offsetParent !== null);
}
function checkFormValidation(form) {
  let i=0;
  let firstelement;
  if (!form.checkValidity()) {
      form.querySelectorAll(":invalid").forEach(function (input) {
        if (!isVisible(input)) {
            return;
        }

          if(i==0) {
              firstelement=input;
          }
      input.classList.add("is-invalid");
      if(!input.nextElementSibling)
          input.insertAdjacentHTML('afterend', '<div class="invalid-feedback"></div>');
      
       if(input.nextElementSibling && input.nextElementSibling.classList.contains("invalid-feedback")) 
          input.nextElementSibling.textContent =input.validationMessage;
      i++;
      });
      if(firstelement) firstelement.focus(); 
      return false;
  } else {
      form.querySelectorAll(".is-invalid").forEach(function (input) {
        input.classList.remove("is-invalid");
        if(input.nextElementSibling && input.nextElementSibling.classList.contains("invalid-feedback")) 
          input.nextElementSibling.textContent = "";
      });
  }
}
/* \ADDED BY BHUMITA ON 24/07/2025 */

/* ADDED BY BHUMITA ON 22/07/2025 */
function ucFirst(str) {
  if (typeof str !== 'string' || str.length === 0) {
    return str; // Handle non-string input or empty strings
  }
  return str.charAt(0).toUpperCase() + str.slice(1);
}
function fetchCountry(form, state_id) {
    if (state_id == "") {
        return;
    }
    $.ajax({
        url: "classes/cls_city_master.php",
        type: "POST",
        data: { state_id: state_id, action: "fetchCountry" },
        success: function (response) {
            let data = JSON.parse(response);
            $('#'+form+' #hid_country_id').val(data.country_id);
            $('#'+form+' #country_id').val(data.country_name);
        },
        error: function () {
            console.log("Error");
        }
    });
}
function fetchCountryAndState(cityId) {
    $.ajax({
        url: "classes/cls_customer_master.php",
        type: "POST",
        data: { action: "fetchCountryAndState", city_id: cityId },
        success: function (response) {
            try {
                const data = JSON.parse(response);
                if (data && data.country_id && data.state_id) {
                    $('#hid_country_id').val(data.country_id);
                    $('#country_id').val(data.country_name);
                    $('#hid_state_id').val(data.state_id);
                    $('#state_id').val(data.state_name);
                } else {
                    console.error("Country or state data missing in response.");
                    $('#country_id, #state_id').val('');
                    $('#country_name, #state_name').val('');
                }
            } catch (e) {
                console.error("Invalid JSON:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    }); 
}
function AddPopupData(form, field_name) {
       if (!form.checkValidity()) {
            let i = 0;
            let firstelement;
            form.querySelectorAll(":invalid").forEach(function (input) {
                if (i === 0) firstelement = input;
                input.classList.add("is-invalid");
                if(input.nextElementSibling && input.nextElementSibling.classList.contains("invalid-feedback")) 
                      input.nextElementSibling.textContent =input.validationMessage;
                else {
                    input.insertAdjacentHTML('afterend', '<div class="invalid-feedback"></div>');
                    input.nextElementSibling.textContent = input.validationMessage;
                }
               
                i++;
            });
            if (firstelement) firstelement.focus();
            return false;
        }
        else {
            form.querySelectorAll(".is-invalid").forEach(function (input) {
            input.classList.remove("is-invalid");
            input.nextElementSibling.textContent = "";
            });
        }
        setTimeout(function(){
            const invalidInputs = form.querySelectorAll(".is-invalid");
            if(invalidInputs.length>0)
            {} else{
                const formData = $(form).serialize();
                const field_value = $(form).find("#"+field_name+"_name").val();
                $.ajax({
                    url: "classes/cls_"+field_name+"_master.php",
                    method: "POST",
                    data: formData,
                    success: function (field_id) {
                        if (parseInt(field_id) > 0 && field_value!= "") {
                            $("#masterForm select[name='"+field_name+"_id']").each(function () {
                                const $dropdown = $(this);
                                if (!$dropdown.find('option[value="'+field_id +'"]').length) {
                                    $dropdown.append(
                                        $("<option>", {
                                            value: field_id,
                                            text: field_value,
                                            selected: true
                                        })
                                    );
                                } else {
                                    $dropdown.val(field_id);
                                }
                                if(field_name == "state") {
                                  fetchCountry("stateForm",field_id);
                                }
                                if(field_name == "city") {
                                  fetchCountryAndState(field_id);
                                }
                            });
                            document.getElementById(field_name+"Form").reset();
                             $("#add"+ucFirst(field_name)+"Modal").modal("hide");
                              Swal.fire({
                                title: "Success!",
                                text: ucFirst(field_name)+" added successfully!",
                                icon: "success",
                                confirmButtonText: "OK"
                              }).then(() => {
                                // Focus input after the alert is closed
                                $("#masterForm #"+field_name+"_id").focus();
                              });
                             
                        } else {
                            Swal.fire("Error",field_id , "error");
                            //console.log("Unexpected response: " + field_id);
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire("", "Error! ", "error");
                    }
                });
                return false;
            }
        },200); 
}
/* \ADDED BY BHUMITA ON 22/07/2025 */
function clearForm(form) {
    form.reset();
    $(':input', form).each(function() {
        var type = this.type;
        if (type == 'checkbox' || type == 'radio') {
            this.checked=false;
            $(this).removeAttr("checked");
        }
    });
}
function reset_data() {
  if($('#masterForm').length>0) {
    document.getElementById("masterForm").reset();
    document.getElementById("transactionmode")="I";
  }
}
function formatDate(date) {
      var year = date.getFullYear();
      var month = String(date.getMonth() + 1).padStart(2, '0');
      var day = String(date.getDate()).padStart(2, '0');
      return year + '-' + month + '-' + day;
}
function formatDateToDDMMYYYY(dateStr) {
    if (!dateStr) return ''; 
    let year,month,day;
    if(dateStr.includes('-'))
       [year, month, day] = dateStr.split('-');
    else if(dateStr.includes('/'))
       [year, month, day] = dateStr.split('/');
    return day+"/"+month+"/"+year;
}
function showError(input,errorContainer,message) {
  if(!errorContainer) {
     const nextSibling=input.next("div");
      if(nextSibling && nextSibling.hasClass("invalid-feedback")) {
          errorContainer=nextSibling;
      }
  }
  if(errorContainer)
    errorContainer.text(message);

  input.addClass('is-invalid');
  input.focus();
}
async function customAlert(message) {
  
  let modalElement = document.getElementById("customAlertModal");
  if(modalElement) {
    return new Promise((resolve) => {
      document.getElementById("customAlertMessage").innerText = message;
      let modal = new bootstrap.Modal(document.getElementById("customAlertModal"), {
        backdrop: 'static', keyboard: false // Prevent closing without clicking OK
      });
      modalElement.addEventListener("shown.bs.modal", function () {
        document.getElementById("customAlertOk").focus(); // Focus on OK button when modal opens
      });
      document.getElementById("customAlertOk").onclick = function() {
        modal.hide();
        resolve(); // Continue execution after clicking OK
      };
      document.getElementById("customAlertCancel").onclick = function () {
        modal.hide();
        resolve(false); // Resolve promise with false (Cancel clicked)
      };
      modal.show(); 
    });
  }
}
jQuery(document).ready(function($){
    if($('.delete').length>0) {
      $('.delete').click(function(){
        let message='';
        Swal.fire({
          title: "Are you sure you want to delete this?",
          text: "You won't be able to revert it!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).closest("form").submit();
          }
        });
        /*(async function() {
            await customAlert("Are you sure you want to delete this?");
          //console.log($(this).closest("form"));
             $(this).closest("form").submit();
          })();*/
      });
    }
    if($('.update').length>0) {
      $('.update').click(function(){
        $(this).closest("form").submit();
      });
    }
});
/* ADDED BY BHUMITA ON 22/07/2025 */
function validateField(input, pattern, errorMessage) {
  if (!isVisible(input)) {
      return;
  }
  const value = input.value.trim();
  if(value === "") {
    return true; // Skip validation for empty fields
  }
  if (value !== "" && !pattern.test(value)) {
      input.classList.add("is-invalid");
      if(input.nextElementSibling){
          input.nextElementSibling.textContent = errorMessage;
      }
      return false;
  } else {
      input.classList.remove("is-invalid");
      if(input.nextElementSibling){
          input.nextElementSibling.textContent = "";
      }
      return true;
  }
}
function number_format(number, decimals, dec_point, thousands_sep) {
    number = parseFloat(number).toFixed(decimals);
    const parts = number.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
    return parts.join(dec_point);
}
function getFloat(id) {
    const v = document.getElementById(id);
    return v && v.value ? parseFloat(v.value) || 0 : 0;
}
/* \ADDED BY BHUMITA ON 22/07/2025 */
// Function to validate a single input
  function validateInput(input) {
    if (!isVisible(input)) {
        return;
    }
    if (!input.checkValidity()) {
       input.classList.add('is-invalid');
        if(input.nextElementSibling && input.nextElementSibling.classList.contains("invalid-feedback")) {
            input.nextElementSibling.textContent = input.validationMessage;
        } else if(!input.nextElementSibling) {
          input.insertAdjacentHTML('afterend', '<div class="invalid-feedback"></div>');
          input.nextElementSibling.textContent = input.validationMessage;
          console.log("else",input.nextElementSibling.textContent);
        }
    } else {
      input.classList.remove('is-invalid');
      if(input.nextElementSibling && input.nextElementSibling.classList.contains("invalid-feedback"))
        input.nextElementSibling.textContent = ''; // Clear error message
    }
  }
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(function (form) {
      const inputs = form.querySelectorAll('input, textarea, select');

      let formSubmitted = false; // Track if form was submitted

      // Validate all fields when the form is submitted
      form.addEventListener('submit', function (event) {
        //console.log("submit");
        formSubmitted = true; // Enable real-time validation after first submit
        let formIsValid = form.checkValidity();
        inputs.forEach(validateInput); // Validate all fields

        if (!formIsValid) {
          event.preventDefault();
          event.stopPropagation();
        }
      });

      // Enable real-time validation only after the first submit
      inputs.forEach((input) => {
          input.addEventListener('input', function () {
            if (formSubmitted) validateInput(input);
          });
          const skipIds = ["btn_search", "btn_reset", "btn_frm","detailbtn_cancel"];
          input.addEventListener('blur', function (event) {
              if(!event.relatedTarget) return;
              if (event.relatedTarget && skipIds.includes(event.relatedTarget.id)) {
                  return;
              }
              // If focus moved to another input/select/textarea → validate
              if (["INPUT", "SELECT", "TEXTAREA"].includes(event.relatedTarget.tagName)) {
                validateInput(input);
              }
          });
      });
    });
  })();