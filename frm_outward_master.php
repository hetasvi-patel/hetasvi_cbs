<?php
    include("classes/cls_outward_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="I";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
   /* MODIFIED BY MANSI MODIFIED BY HETANSHREE EDIT MODE DISABLED OUTWARD_SEQUENCE,OUTWARD_DATE SELECT INWARD BUTTON */
    checkFrmPermission($transactionmode,$currentmenu_label,"srh_outward_master.php");
    if ($transactionmode == "U") {
    $_bll->fillModel();
    $label = "Update";
      $outward_id = isset($_REQUEST['outward_id']) ? $_REQUEST['outward_id'] : null;
        if ($outward_id) {
            // UPDATED: determine if ANY outward_detail for this outward is invoiced (by outward_detail_id)
            $stmt = $_dbh->prepare("
                SELECT COUNT(*)
                FROM tbl_rent_invoice_detail rid
                INNER JOIN tbl_outward_detail od ON rid.outward_detail_id = od.outward_detail_id
                WHERE od.outward_id = ?
            ");
            $stmt->execute([$outward_id]);
            $is_used_in_rent_invoice = $stmt->fetchColumn() > 0;
        }
    } else {
        $label = "Add";
        $is_used_in_rent_invoice = false;
    }
    /* ADDED BY BHUMITA ON 18/08/2025 */
    //outward no and outward sequence
    $outward_id = isset($_REQUEST['outward_id']) ? $_REQUEST['outward_id'] : null;
    $sequence_data = getNextSequenceAndNo(
        $tbl_outward_master,
        'outward_sequence',
        'outward_no',
        'outward_date',
        COMPANY_YEAR_ID,
        $outward_id,
        'outward_id'
    );
    $next_outward_sequence = $sequence_data['next_sequence'];
    $outward_no_formatted = $sequence_data['formatted_no'];
    $finYear = $sequence_data['fin_year'];
    /* \ADDED BY BHUMITA ON 18/08/2025 */
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $label; ?> Data
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_outward_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
                <?php
                    echo $_bll->getForm($transactionmode,false,"col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1","col-12 col-sm-7 col-md-5 col-lg-5 col-xl-4 col-xxl-3"); // ADDED CLASSES ARGUMENTS BY BHUMITA ON 19/08/2025
                ?>
            <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                  <input type="hidden" id="deleted_records" name="deleted_records" />
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_outward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                   <input type="button" class="btn btn-default" id="btn_frm" name="btn_frm" value="Cancel" onclick="window.location='frm_outward_master.php'">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div> <!-- /.box -->
          </div>
      </section> <!-- /.content -->
    </div> <!-- /.container -->

<!--  BELOW CODE ADDED BY BHUMITA ON 18/08/2025 -->
<!-- Modal -->
   <div class="detail-modal">
  <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
    <div class="modal-dialog outward modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <form id="popupForm" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="modal-body">
                <div class="box-body container-fluid">
                <div id="pendingInwardSection">
                    <h5 class="modal-title" id="pendingInwardLabel">Pending Inward</h5>
                    <table class="table table-bordered table-striped table-sm align-middle" style="width:100%;">
                    <thead class="table-light boxheader">
                        <tr>
                        <th>Select</th>
                        <th>Inward No.</th>
                        <th>Lot No.</th>
                        <th>Inward Date</th>
                        <th>Broker</th>
                        <th>Item</th>
                        <th>marko</th>
                        <th>Inward Qty</th>
                        <th>Unit</th>
                        <th>Inward Wt</th>
                        <th>Stock Qty</th>
                        <th>Stock Wt.(Kg)</th>
                        <th>Out Qty</th>
                        <th>Out Wt.(Kg)</th>
                        <th>Loading Charges</th>
                        <th>Location</th>
                        </tr>
                    </thead>
                    <tbody id="pendingInwardTableBody">
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" id="saveSelectedInward" class="btn btn-success">Ok</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div><!-- /Modal -->
<!--  \BELOW CODE ADDED BY BHUMITA ON 18/08/2025 -->
  </div> <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div><!-- ./wrapper -->
<?php
    include("include/footer_includes.php");
?>
<script src="dist/js/transaction_functions.js"></script>


<script>
// Initialize jsonData if not exists
if (!window.jsonData) {
    window.jsonData = [];
}
if (!window.deleteData) {
    window.deleteData = [];
}
document.addEventListener("DOMContentLoaded", function () {    
    /* ADDED BY HETANSHREE EDIT MODE DISABLED OUTWARD_SEQUENCE,OUTWARD_DATE SELECT INWARD BUTTON */ 
    var transactionmode = "<?php echo $transactionmode; ?>";
    var isUsedInRentInvoice = <?php echo $is_used_in_rent_invoice ? 'true' : 'false'; ?>;
    /*MODIFIED BY MANSI*/
    if (transactionmode === "U" && isUsedInRentInvoice) {
        var outwardSeq = document.getElementById('outward_sequence');
        if (outwardSeq) {
            outwardSeq.readOnly = true;          
            outwardSeq.classList.add('bg-light');
        }
        var outwardDate = document.getElementById('outward_date');
        if (outwardDate) {
            outwardDate.readOnly = true;
            outwardDate.classList.add('bg-light');
            outwardDate.addEventListener('mousedown', e => e.preventDefault());
        }
        var btnInward = document.getElementById('btn_inward');
        if (btnInward) btnInward.setAttribute('disabled', 'disabled');
    }
   /* ADDED BY HETANSHREE EDIT MODE DISABLED OUTWARD_SEQUENCE,OUTWARD_DATE SELECT INWARD BUTTON */ 

    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    /* ADDED BY BHUMITA ON 04/08/2025 */
    const financialYear = "<?php echo $finYear; ?>";   
    let companyYearDetails = {
        start_date: '<?php echo isset($sequence_data["start_date"]) ? $sequence_data["start_date"] : date("Y-m-d"); ?>',
        end_date: '<?php echo isset($sequence_data["end_date"]) ? $sequence_data["end_date"] : date("Y-m-d"); ?>'
    };
    let editedInwardData = {};
    const customerSelect = document.getElementById('customer');
    /* \ADDED BY BHUMITA ON 04/08/2025 */
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="outward_id";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_outward_master.php"; ?>",
            type: "POST",
             data: { column_name: input.name,
         column_value:column_value, 
        id_name:id_column,
        id_value:id_value,
        table_name:"<?php echo $tbl_outward_master; ?>",
        action:"checkDuplicate",
        company_year_id: "<?php echo COMPANY_YEAR_ID; ?>" // <-- Add this on 29/09/2025 by HETASVI
        },
            success: function(response) {
                response = parseInt(response);
                if (response == 1) {
                    input.classList.add("is-invalid");
                    input.focus();
                    let message="";
                    if(input.validationMessage)
                        message=input.validationMessage;
                    else
                        message="Duplicate Value";
                    if(input.nextElementSibling) 
                      input.nextElementSibling.textContent = message;
                      return false;
                } else {
                   input.classList.remove("is-invalid");
                    if(input.nextElementSibling) 
                        input.nextElementSibling.textContent = "";
                }
            },
            error: function() {
                console.log("Error");
            }
        }); // ajax completed
    }
    /* ADDED BY BHUMITA ON 18/08/2025 */
     duplicateInputs.forEach((input) => {
            input.addEventListener('blur', function (event) {
                setTimeout(function() {
                    checkDuplicate(input);
                    if(input.name === "outward_sequence") {
                        let otherInput = document.getElementById("outward_no");
                        if(otherInput) checkDuplicate(otherInput);
                    }
                }, 100);
            });
        });
    // Outward sequence and inward_no logic   
    setSequence("outward_sequence","outward_no",financialYear); 
    
    //Outward_date
    setDefaultDates('outward_date',companyYearDetails);
    $('#outward_date').on('blur', function(){
        validateDate('outward_date',companyYearDetails);
    });

    // Delivery to auto-fill logic 
    const deliveryToInput = document.getElementById('delivery_to');
    // const transactionmode = "<?php echo $transactionmode; ?>"; // already defined above
    function updateDeliveryTo() {
        const selected = customerSelect.options[customerSelect.selectedIndex];
        deliveryToInput.value = selected ? selected.text : '';
    }
    if (customerSelect && deliveryToInput) {
        customerSelect.addEventListener('change', updateDeliveryTo);
        if (transactionmode !== "U") {
            updateDeliveryTo();
        }
    }
    
    // Calculate total qty, total wt and loading expense logic
    const totalQtyInput = document.getElementById("total_qty");
    const totalWeightInput = document.getElementById("total_wt");
    const totalLoadingExpenseInput = document.getElementById("loading_expense");
    let initialTotalsSet = false; 

    // MODIFIED BY MANSI - TOTALS ALWAYS FROM LATEST MERGED 
    function updateTotalsFromPendingGrid() {
        const latestMap = {};
        document.querySelectorAll('#searchDetail tbody tr').forEach(row => {
            if (row.classList.contains('norecords')) return;
            const inwardDetailId = row.getAttribute('data-inward-detail-id');
            if (!inwardDetailId) return;
            const outQty = parseFloat(row.querySelector('td[data-label="Out Qty."]')?.textContent) || 0;
            const outWt = parseFloat(row.querySelector('td[data-label="Out. Wt. (Kg.)"]')?.textContent) || 0;
            const loadingCharges = parseFloat(row.querySelector('td[data-label="Loading Charges"]')?.textContent) || 0;
            latestMap[String(inwardDetailId)] = {
                out_qty: outQty,
                out_wt: outWt,
                loading_charges: loadingCharges
            };
        });
        // overrides grid values
        if (window.jsonData && Array.isArray(window.jsonData)) {
            window.jsonData.forEach(r => {
                const key = String(r.inward_detail_id || '');
                if (!key) return;
                if (r.detailtransactionmode === 'D') {
                    delete latestMap[key];
                    return;
                }
                latestMap[key] = {
                    out_qty: parseFloat(r.out_qty) || 0,
                    out_wt: parseFloat(r.out_wt) || 0,
                    loading_charges: parseFloat(r.loading_charges) || 0
                };
            });
        }
        let totalQty = 0, totalWt = 0, totalLoadingExpense = 0;
        Object.values(latestMap).forEach(v => {
            totalQty += v.out_qty;
            totalWt += v.out_wt;
            totalLoadingExpense += v.out_qty * v.loading_charges;
        });
            
        const totalQtyInput = document.getElementById("total_qty");
        const totalWeightInput = document.getElementById("total_wt");
        const totalLoadingExpenseInput = document.getElementById("loading_expense");
        if (totalQtyInput) {
            totalQtyInput.value = totalQty ? totalQty : "";
            const hid = document.getElementById('hid_total_qty'); if (hid) hid.value = totalQty ? totalQty : "";
        }
        if (totalWeightInput) {
            totalWeightInput.value = totalWt ? totalWt.toFixed(3) : "";
            const hid = document.getElementById('hid_total_wt'); if (hid) hid.value = totalWt ? totalWt.toFixed(3) : "";
        }
        if (totalLoadingExpenseInput) {
            totalLoadingExpenseInput.value = totalLoadingExpense ? totalLoadingExpense.toFixed(2) : "";
            const hid = document.getElementById('hid_loading_expense'); if (hid) hid.value = totalLoadingExpense ? totalLoadingExpense.toFixed(2) : "";
        }
    }
    // MODIFIED BY MANSI - AUTOFILL OUT_QTY /OUT_WT
    function getExistingDetailsMap() {
    const map = {};
    // from grid
    document.querySelectorAll('#searchDetail tbody tr').forEach(row => {
        if (row.classList.contains('norecords')) return;
        const inwardDetailId = row.getAttribute('data-inward-detail-id');
        if (!inwardDetailId) return;
        const outwardDetailId = row.getAttribute('data-id') || ""; // blank in new mode
        const outQty = parseFloat(row.querySelector('td[data-label="Out Qty."]')?.textContent) || 0;
        const outWt = parseFloat(row.querySelector('td[data-label="Out. Wt. (Kg.)"]')?.textContent) || 0;
        const loadingCharges = parseFloat(row.querySelector('td[data-label="Loading Charges"]')?.textContent) || 0;
        map[String(inwardDetailId)] = {
            outward_detail_id: outwardDetailId || undefined,
            out_qty: outQty,
            out_wt: outWt,
            loading_charges: loadingCharges
        };
    });
    // overlay jsonData (latest unsaved edits)
    if (window.jsonData && Array.isArray(window.jsonData)) {
        window.jsonData.forEach(r => {
            const key = String(r.inward_detail_id || '');
            if (!key) return;
            if (r.detailtransactionmode === 'D') {
                delete map[key];
                return;
            }
            map[key] = {
                outward_detail_id: r.outward_detail_id || map[key]?.outward_detail_id,
                out_qty: parseFloat(r.out_qty) || 0,
                out_wt: parseFloat(r.out_wt) || 0,
                loading_charges: parseFloat(r.loading_charges) || 0
            };
        });
    }
    return map;
}

    document.addEventListener('change', function (e) {
        if (e.target.classList && e.target.classList.contains('inwardCheckbox')) {
            initialTotalsSet = true;
            updateTotalsFromPendingGrid();
        }
    });
    document.addEventListener('blur', function (e) {
        if (e.target.classList && (e.target.classList.contains('out-qty') || e.target.classList.contains('out-wt'))) {
            initialTotalsSet = true;
            updateTotalsFromPendingGrid();
        }
    }, true);

    // Pending inward grid logic
    function saveEditedInwardRow(tr) {
        const inwardId = tr.getAttribute('data-inward-id');
        const inwardDetailId = tr.getAttribute('data-inward-detail-id');
        const uniqueKey = inwardId + '_' + inwardDetailId;
        const outQty = parseFloat(tr.querySelector('.out-qty-cell')?.textContent) || 0;
        const outWt = parseFloat(tr.querySelector('.out-wt-cell')?.textContent) || 0;
        const loadingCharge = parseFloat(tr.querySelector('.loading-charge-cell')?.textContent) || 0;
        const checked = tr.querySelector('.select-inward-checkbox')?.checked || false;

        editedInwardData[uniqueKey] = {
            out_qty: outQty,
            out_wt: outWt,
            loading_charge: loadingCharge,
            checked: checked
        };
    }
    function setErrorCells(outQtyCell) {
        if(outQtyCell) {
            outQtyCell.focus();
            const range = document.createRange();
            range.selectNodeContents(outQtyCell);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
    //MODOFIED Ensure appendTableRow sets data-inward-detail-id so future updates hit the same row
    function appendTableRow(data) {
        const tableBody = document.querySelector('#searchDetail tbody');
        if (!tableBody) return;
        const tr = document.createElement('tr');
        tr.setAttribute('data-inward-detail-id', data.inward_detail_id || '');
        if (data.outward_detail_id) tr.setAttribute('data-id', data.outward_detail_id);
        tr.setAttribute('data-invoiced', data.is_invoiced ? '1' : '0'); // safe default if provided
        tr.innerHTML = `
            <input type="hidden" name="inward_detail_id[]" value="${data.inward_detail_id || 0}">
            <td data-label="Action" class="actions">
                <button class="btn btn-danger btn-sm delete-btn" data-id="${data.outward_detail_id || ''}" ${data.is_invoiced ? 'disabled title="This outward detail is already invoiced and cannot be deleted."' : ''}>Delete</button>
            </td>
            <td data-label="Inward No.">${data.inward_no}</td>
            <td data-label="Lot No.">${data.lot_no}</td>
            <td data-label="Inward Date">${data.inward_date}</td>
            <td data-label="Item">${data.item}</td>
            <td data-label="marko">${data.marko}</td>
            <td data-label="Out Qty.">${data.out_qty}</td>
            <td data-label="Unit">${data.unit}</td>
            <td data-label="Out. Wt. (Kg.)">${data.out_wt}</td>
            <td data-label="Loading Charges">${data.loading_charges}</td>
            <td data-label="Location">${data.location}</td>
        `;
        tableBody.appendChild(tr);
    }

    //MODIFIED updates by inward_detail_id for both modes
    function updateExistingGridRow(record) {
        const row = document.querySelector(`#searchDetail tbody tr[data-inward-detail-id="${record.inward_detail_id}"]`);
        if (!row) return false;
        const setText = (selector, val) => {
            const cell = row.querySelector(selector);
            if (cell) cell.textContent = val;
        };
        setText('td[data-label="Out Qty."]', record.out_qty);
        setText('td[data-label="Out. Wt. (Kg.)"]', record.out_wt);
        setText('td[data-label="Loading Charges"]', record.loading_charges);
        if (record.outward_detail_id) row.setAttribute('data-id', record.outward_detail_id);
        // carry over invoiced flag if provided
        if (typeof record.is_invoiced !== 'undefined') {
            row.setAttribute('data-invoiced', record.is_invoiced ? '1' : '0');
            const btn = row.querySelector('.delete-btn');
            if (btn && record.is_invoiced) {
                btn.setAttribute('disabled','disabled');
                btn.setAttribute('title','This outward detail is already invoiced and cannot be deleted.');
            }
        }
        return true;
    }
    
    /* MANSI - DELETE HANDLER */
    var searchDetail = document.getElementById("searchDetail");
    if (!searchDetail) return;
    /* MODIFIY FUNCTION- HETANSHREE LAST RECORD DELETE MESSAGE */
    function doDeleteRow(tr) {
    var searchDetail = document.getElementById("searchDetail");
    var dataRows = searchDetail.querySelectorAll("tbody tr:not(.norecords)");
        if (dataRows.length <= 1) {
            // Show message and do not delete
            if (window.Swal && Swal.fire) {
                Swal.fire({
                    title: 'Cannot delete',
                    text: 'Enter outward detail.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Enter outward detail.');
            }
            return false;
        }
        var inwardDetailId = tr.getAttribute("data-inward-detail-id");
        var outwardDetailId = tr.getAttribute("data-id");
        if (inwardDetailId) {
            if (!window.deleteData.some(function (r) {
                return String(r.inward_detail_id) === String(inwardDetailId);
            })) {
                window.deleteData.push({
                    outward_detail_id: outwardDetailId || undefined,
                    inward_detail_id: inwardDetailId,
                    detailtransactionmode: "D"
                });
            }
        }
        window.jsonData = (window.jsonData || []).filter(function (r) {
            return String(r.inward_detail_id) !== String(inwardDetailId);
        });
        tr.remove();
        if (searchDetail.querySelectorAll("tbody tr:not(.norecords)").length === 0) {
            var tbody = searchDetail.querySelector("tbody");
            var noTr = document.createElement("tr");
            noTr.id = "norecords";
            noTr.className = "norecords";
            noTr.innerHTML = '<td colspan="11">No records available.</td>';
            tbody.appendChild(noTr);
        }
        if (typeof updateTotalsFromPendingGrid === "function") {
            updateTotalsFromPendingGrid();
        }
    }
      searchDetail.addEventListener("click", function (e) {
        var btn = e.target.closest(".delete-btn");
        if (!btn) return;
        e.preventDefault();
        var tr = btn.closest("tr");
        if (!tr) return;

        // NEW GUARD: block deletion if invoiced
        var invoiced = tr.getAttribute('data-invoiced') === '1' || btn.disabled;
        if (invoiced) {
          if (window.Swal && Swal.fire) {
            Swal.fire({
              title: 'Not allowed',
              text: 'This outward detail is already invoiced and cannot be deleted.',
              icon: 'info',
              confirmButtonText: 'OK'
            });
          } else {
            alert('This outward detail is already invoiced and cannot be deleted.');
          }
          return;
        }

        if (window.Swal && Swal.fire) {
          Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: "You won't be able to revert it!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            focusCancel: true
          }).then(function (result) {
            if (result.isConfirmed) {
              doDeleteRow(tr);
            }
          });
        } else {
          if (confirm('Are you sure you want to delete this record?')) {
            doDeleteRow(tr);
          }
        }
      });
        
    /* ADDED BY MANSI OUT_QTY */
    function showCustomPopup(message, cell, popupId) {
        if (document.getElementById(popupId)) return;
        const overlay = Object.assign(document.createElement('div'), {
            id: popupId,
            style: `
                position:fixed;top:0;left:0;width:100vw;height:100vh;
                background:rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;z-index:9999;
            `.replace(/\s+/g, '')
        });
        const popup = document.createElement('div');
        popup.style.cssText = `
            background:#fff;padding:2rem 2.5rem 1.5rem 2.5rem;
            border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.15);text-align:center;
        `;
        popup.innerHTML = `
            <div style="font-size:1.3rem;color:red;margin-bottom:1rem;">${message}</div>
            <button id="${popupId}CloseBtn" style="padding:.5rem 2rem;font-size:1.1rem;background:#0d6efd;color:#fff;border:none;border-radius:5px;cursor:pointer;">OK</button>
        `;
        overlay.appendChild(popup);
        document.body.appendChild(overlay);
        document.getElementById(`${popupId}CloseBtn`).onclick = function() {
            document.body.removeChild(overlay);
            if (cell) {
                cell.focus();
                const range = document.createRange();
                range.selectNodeContents(cell);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
        };
    }
    function showCustomMessagePopup(msg, cellToRefocus) {
        showCustomPopup(msg, cellToRefocus, 'customOutwardQtyPopup');
    }
    function showStockNotAvailablePopup(cellToRefocus) {
        showCustomPopup('Stock Qty not available', cellToRefocus, 'customStockPopup');
    }

    /* MODIFIED BY MANSI*/
    document.getElementById('saveSelectedInward').addEventListener('click', function() {
        const tbody = document.getElementById('pendingInwardTableBody');
        const checkedRows = tbody.querySelectorAll('input[type="checkbox"]:checked');

        let foundError = false;
        let firstErrorCell = null;
        checkedRows.forEach(cb => {
            const tr = cb.closest('tr');
            const outQtyCell = tr.querySelector('.out-qty-cell');
            const stockQtyCell = tr.querySelector('[data-label="Stock Qty"]');

            const outQty = parseFloat((outQtyCell?.textContent || '').trim()) || 0;
            const stockQty = parseFloat((stockQtyCell?.textContent || '').trim()) || 0;

            if (!outQty || outQty <= 0) {
                if (!foundError) {
                    showCustomMessagePopup('Please enter Outward Qty', outQtyCell);
                    foundError = true;
                    firstErrorCell = outQtyCell;
                }
                return;
            }
            if (outQty > stockQty) {
                if (!foundError) {
                    showStockNotAvailablePopup(outQtyCell);
                    foundError = true;
                    firstErrorCell = outQtyCell;
                }
                return;
            }
        });

        if (foundError) return;
        let recs = [];
        checkedRows.forEach(cb => {
            const tr = cb.closest('tr');
            const inwardDetailId = tr.getAttribute('data-inward-detail-id') ?? 0;
            const outwardDetailId = tr.getAttribute('data-outward-detail-id') || undefined;

            const record = {
                outward_detail_id: outwardDetailId,
                inward_detail_id: inwardDetailId,
                inward_no: tr.querySelector('[data-label="Inward No."]')?.textContent.trim() || "",
                lot_no: tr.querySelector('[data-label="Lot No."]')?.textContent.trim() || "",
                inward_date: tr.querySelector('[data-label="Inward Date"]')?.textContent.trim() || "",
                item: tr.querySelector('[data-label="Item"]')?.textContent.trim() || "",
                marko: (tr.querySelector('[data-label="Marko"]') || tr.querySelector('[data-label="marko"]'))?.textContent.trim() || "",
                stock_qty: tr.querySelector('[data-label="Stock Qty"]')?.textContent.trim() || "",
                out_qty: tr.querySelector('.out-qty-cell')?.textContent.trim() || "0",
                unit: tr.querySelector('[data-label="Unit"]')?.textContent.trim() || "",
                out_wt: tr.querySelector('.out-wt-cell')?.textContent.trim() || "0",
                loading_charges: tr.querySelector('.loading-charge-cell')?.textContent.trim() || "0",
                location: tr.querySelector('[data-label="Location"]')?.textContent.trim() || "",
                detailtransactionmode: outwardDetailId ? 'U' : 'I'
            };
            recs.push(record);
        });
        recs.forEach(rec => {
            const idx = window.jsonData.findIndex(r => String(r.inward_detail_id) === String(rec.inward_detail_id));
            if (idx >= 0) window.jsonData[idx] = { ...window.jsonData[idx], ...rec };
            else window.jsonData.push(rec);
        });

        const noRecordsRow = document.getElementById('norecords');
        if (noRecordsRow) noRecordsRow.remove();

        recs.forEach(rec => {
            const exists = document.querySelector(`#searchDetail tbody tr[data-inward-detail-id="${rec.inward_detail_id}"]`);
            if (exists) updateExistingGridRow(rec);
            else appendTableRow(rec);
        });

        document.getElementById('detail_records').value = JSON.stringify(window.jsonData);
        bootstrap.Modal.getInstance(document.getElementById('modalDialog')).hide();

        document.getElementById('customer').disabled = true;
        updateTotalsFromPendingGrid();
    });
    // ON MODAL OPEN: use merged map so new-add unsaved rows are also prefilled/checked
    document.getElementById('btn_inward').addEventListener('click', function() {
    validateInput(document.getElementById('customer'));
    if (document.getElementById('customer') && document.getElementById('customer').value !== '') {
        var myModal = new bootstrap.Modal(document.getElementById('modalDialog'));
        myModal.show();
    }
    const existingDetails = getExistingDetailsMap();
    const customerId = document.getElementById('customer').value;
    const outwardId = document.getElementById('outward_id')?.value ?? '';/*MANSI - MODIFIED BY EDIT MODE NOT EXISTING RECORD*/
    let url = 'pending_inward.php?customer=' + encodeURIComponent(customerId);
    if (outwardId) url += '&outward_id=' + encodeURIComponent(outwardId);
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('pendingInwardTableBody');
            tbody.innerHTML = '';
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-inward-id', row.inward_id ?? 0);
                tr.setAttribute('data-inward-detail-id', row.inward_detail_id ?? 0);

                const exist = existingDetails[String(row.inward_detail_id)] || null;
                const defaultOutQty = exist ? exist.out_qty : (row.out_qty ?? 0);
                const defaultOutWt = exist ? exist.out_wt : (row.out_wt ?? 0);
                const defaultLoading = exist ? exist.loading_charges : (row.loading_charge ?? 0);
                const shouldCheck = !!exist;

                if (exist && exist.outward_detail_id) {
                    tr.setAttribute('data-outward-detail-id', exist.outward_detail_id);
                }
                // MANSI: sky blue if selected, else pink if full stock and not selected
                if (shouldCheck) {
                    tr.classList.add('selected-inward-row');
                } else if (Number(row.stock_qty) === Number(row.inward_qty)) {
                    tr.classList.add('full-inward-qty');
                }
                tr.innerHTML = `
                    <td><input type="checkbox" class="select-inward-checkbox" ${shouldCheck ? 'checked' : ''}></td>
                    <td data-label="Inward No.">${row.inward_no ?? 'N/A'}</td>
                    <td data-label="Lot No.">${row.lot_no ?? 'N/A'}</td>
                    <td data-label="Inward Date">${row.inward_date ? formatDateToDDMMYYYY(row.inward_date) : 'N/A'}</td>
                    <td data-label="Broker">${row.broker ?? 'N/A'}</td>
                    <td data-label="Item">${row.item ?? 'N/A'}</td>
                    <td data-label="marko">${row.marko ?? 'N/A'}</td>
                    <td data-label="Inward Qty">${row.inward_qty ?? 'N/A'}</td>
                    <td data-label="Unit">${row.packing_unit ?? 'N/A'}</td>
                    <td data-label="Inward Wt">${row.inward_wt ?? 'N/A'}</td>
                    <td data-label="Stock Qty" class="stock-qty-cell">${row.stock_qty ?? 'N/A'}</td>
                    <td data-label="Stock Wt" class="stock-wt-cell">${row.stock_wt ?? 'N/A'}</td>
                    <td data-label="Out Qty" class="out-qty-cell" contenteditable="${shouldCheck ? 'true' : 'false'}">${defaultOutQty}</td>
                    <td data-label="Out Wt" class="out-wt-cell">${defaultOutWt}</td>
                    <td data-label="Loading Charges" class="loading-charge-cell" contenteditable="${shouldCheck ? 'true' : 'false'}">${defaultLoading}</td>
                    <td data-label="Location">${row.location ?? 'N/A'}</td>
                `;
                tbody.appendChild(tr);
            });
        });
});

    document.getElementById('pendingInwardTableBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('select-inward-checkbox')) {
            const tr = e.target.closest('tr');
            const outQtyCell = tr.querySelector('.out-qty-cell');
            const loadingChargeCell = tr.querySelector('.loading-charge-cell');
            if (e.target.checked) {
                outQtyCell.setAttribute('contenteditable', 'true');
                loadingChargeCell.setAttribute('contenteditable', 'true');
                outQtyCell.focus();
                document.getSelection().selectAllChildren(outQtyCell);
            } else {
                outQtyCell.removeAttribute('contenteditable');
                loadingChargeCell.removeAttribute('contenteditable');
            }
            saveEditedInwardRow(tr);
        }
    });
    document.getElementById('pendingInwardTableBody').addEventListener('input', function(e) {
        const tr = e.target.closest('tr');
        if (e.target.classList.contains('out-qty-cell')) {
            const outQty = parseFloat(e.target.textContent) || 0;
            const stockQty = parseFloat(tr.querySelector('.stock-qty-cell').textContent) || 0;
            const stockWt = parseFloat(tr.querySelector('.stock-wt-cell').textContent) || 0;
            const outWtCell = tr.querySelector('.out-wt-cell');
            const perUnitKg = stockQty > 0 ? (stockWt / stockQty) : 0;
            const outWt = (outQty * perUnitKg).toFixed(3);
            outWtCell.textContent = outWt;
            saveEditedInwardRow(tr);
        }
        else if (e.target.classList.contains('loading-charge-cell')) {
            saveEditedInwardRow(tr);
        }
    });
    document.getElementById('pendingInwardTableBody').addEventListener('blur', function(e) {
        if (e.target.classList && (e.target.classList.contains('out-qty-cell') || e.target.classList.contains('loading-charge-cell'))) {
            const tr = e.target.closest('tr');
            saveEditedInwardRow(tr);
        }
    }, true);

    // Load outward order by logic
    const orderBySelect = document.getElementById('outward_order_by');
    const selectedValue = orderBySelect.value;
    function loadContactPersons(customerId, selected = '') {
        if (customerId) {
            orderBySelect.innerHTML = '<option value="">Loading...</option>';
        } else {
            orderBySelect.innerHTML = '<option value="">Select</option>';
            return;
        }
        fetch('frm_outward_master.php?action=getContactPersons&customer_id=' + encodeURIComponent(customerId))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                orderBySelect.innerHTML = html;
                if (selected) {
                    orderBySelect.value = selected;
                    if (!orderBySelect.value) {
                        orderBySelect.value = '';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching contact persons:', error);
                orderBySelect.innerHTML = '<option value="">Error loading contact persons</option>';
            });
    }
    if (customerSelect && orderBySelect) {
        orderBySelect.innerHTML = '<option value="">Select</option>';
        customerSelect.addEventListener('change', function () {
            loadContactPersons(this.value, '');
        });
        if (customerSelect.value) {
            loadContactPersons(customerSelect.value, selectedValue);
        }
    }
    /* \ADDED BY BHUMITA ON 04/08/2025 */
    document.getElementById("btn_add").addEventListener("click", function (event) {
        const form = document.getElementById("masterForm"); 
        duplicateInputs.forEach((input) => {
            checkDuplicate(input);
        });
        checkFormValidation(form);
        setTimeout(function(){
            const invalidInputs = document.querySelectorAll(".is-invalid");
            if(invalidInputs.length > 0) {
            } else {
                updateTotalsFromPendingGrid(); // ADDED BY BHUMITA ON 18/08/2025

                /* ADDED BY HETANSHREE DETAIL RECORD VALIDATION */
                var transactionmode = document.getElementById("transactionmode").value;
                if (transactionmode === "I") {
                    if (!window.jsonData || window.jsonData.length === 0) {
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                title: 'Cannot delete',
                                text: 'Enter outward detail.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('Enter outward detail.');
                        }
                        return;
                    }
                }
                const jsonDataString = JSON.stringify(jsonData);
                document.getElementById("detail_records").value = jsonDataString;

                const deletedDataString = JSON.stringify(deleteData);
                document.getElementById("deleted_records").value = deletedDataString;
                $("#masterForm").submit();
            }
        },200);
        document.getElementById('customer').disabled = false;
    });
});
</script>
<?php
    frmAlert("frm_outward_master.php");
?>
<?php
    include("include/footer_close.php");
?>