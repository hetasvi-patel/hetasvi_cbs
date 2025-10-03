let table;
function initializeDataTable(title,canExcel) {
    let excelBtn = [];
    if(canExcel) {
       excelBtn = {
                    extend: "excelHtml5",
                    title: title,
                    exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                };
    }
    table = $("#searchMaster").DataTable({
        colReorder: true,
        scrollX: true,
        layout: {
            topStart: {
                buttons: [
                "colvis",
                excelBtn,
                {
                    extend: "pdfHtml5",
                    title: title,
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                },
                {
                    extend: "print",
                     exportOptions: {
                        columns: ":visible",
                        autoFilter: true,
                    }
                }
            ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 }
        ]
    });
}
jQuery(document).ready(function ($) {
    
    $(".date-filter").datepicker({
        dateFormat: "dd/mm/yy",
        showButtonPanel: true,
        closeText: "Close",
        beforeShow: function (input) {
            setTimeout(function () {
            var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
            if (buttonPane.find(".ui-datepicker-clear").length === 0) {
                $("<button>", {
                    text: "Clear",
                    class: "ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all",
                    click: function () {
                        $(input).val("").datepicker("hide");
                        const colIndex = $(input).attr("data-index"); 
                        table.column(colIndex).search("").draw();
                    }
                }).appendTo(buttonPane);
            }
            }, 1);
        },
        onSelect: function (dateText) {
            let colIndex = $(this).attr("data-index");
            table.column(colIndex).search(dateText).draw(); // adjust index if needed
        }
    });
    $("#search-filters input, #search-filters select").on("keyup change", function () {
        let colIndex = $(this).attr("data-index");
        table.column(colIndex).search(this.value).draw();
    });
});