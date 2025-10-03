<?php
include("classes/cls_item_preservation_charges_list_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");
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

      <section class="content">

        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
            <?php
                if (isset($_bll))
                    $_bll->pageSearch();
            ?>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
  include("include/footer.php");
  ?>
</div>
<!-- ./wrapper -->

<?php
include("include/footer_includes.php");
?>
<script>
jQuery(document).ready(function ($) {
    var companyTitle = 'Inter Continental Enterprise (Cold Storage) Pvt. Ltd.';
    function getCurrentDateForFilename() {
        var d = new Date();
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }
    var fileDate = getCurrentDateForFilename();
    var currentDate = new Date().toLocaleDateString('en-GB');
    var summaryTitle = 'ITEM PRESERVATION CHARGE REPORT';

    var table = $("#searchMaster").DataTable({
        colReorder: true,
        pageLength: 50,
        language: {
            emptyTable: "No records available."
        },
        layout: {
            topStart: {
                buttons: [
                    "colvis",
                    {
                        extend: "csvHtml5",
                        filename: fileDate + "_item_preservation_charges",
                        exportOptions: { columns: ":visible", header: true },
                        customize: function (csv) {
                            var prepend = '"' + companyTitle + '"\n"' + summaryTitle + '"\n\n';
                            return prepend + csv;
                        }
                    },
                    {
                        extend: "excelHtml5",
                        title: companyTitle + ' ' + summaryTitle,
                        filename: fileDate + "_item_preservation_charges",
                        exportOptions: { columns: ":visible", autoFilter: true }
                    },
                    {
                        extend: "pdfHtml5",
                        title: "",
                        filename: fileDate + "_item_preservation_charges",
                        customize: function (doc) {
                            doc.content.splice(0, 0, {
                                margin: [0, 0, 0, 0],
                                columns: [
                                    { text: currentDate, alignment: 'left', fontSize: 10 },
                                    { text: companyTitle, alignment: 'center', fontSize: 14, bold: true },
                                    { text: 'Page 1 of 1', alignment: 'right', fontSize: 10 }
                                ]
                            });
                            doc.content.splice(1, 0, {
                                canvas: [
                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                ],
                                margin: [0, 0, 0, 2]
                            });
                            doc.content.splice(2, 0, {
                                text: summaryTitle,
                                style: 'header',
                                alignment: 'center',
                                margin: [0, 5, 0, 0],
                                bold: true,
                                fontSize: 12
                            });
                            doc.content.splice(3, 0, {
                                canvas: [
                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                ],
                                margin: [0, 0, 0, 8]
                            });
                            var tableNode = null;
                            for (var i = 0; i < doc.content.length; i++) {
                                if (doc.content[i].table !== undefined) {
                                    tableNode = doc.content[i];
                                    break;
                                }
                            }
                            if (tableNode) {
                                var colLen = tableNode.table.body[0].length;
                                tableNode.table.widths = Array(colLen).fill('*');
                            }
                        }
                    },
                    {
                        extend: "print",
                        title: "",
                        customize: function (win) {
                            $(win.document.body).find('h1').remove();
                            $(win.document.body).prepend(
                                '<div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">' +
                                    '<div style="font-size:12px;text-align:left;width:33%;">' + currentDate + '</div>' +
                                    '<div style="font-size:16px;font-weight:bold;text-align:center;width:34%;">' + companyTitle + '</div>' +
                                    '<div style="font-size:12px;text-align:right;width:33%;">Page 1 of 1</div>' +
                                '</div>' +
                                '<hr style="border:0;border-top:2px solid #000;margin:0 0 2px 0;">' +
                                '<div style="text-align:center;font-weight:bold;font-size:15px;margin:8px 0 0 0;">' + summaryTitle + '</div>' +
                                '<hr style="border:0;border-top:5px solid #000;margin:2px 0 6px 0;">'
                            );
                        }
                    }
                ]
            }
        },
        columnDefs: [
            { "orderable": false, "targets": 0 },
            { "className": "dt-head-right dt-body-right", "targets": "_all" }
        ]
    });

    window.reloadGrid = function() {
        let filter_type = $('input[name="filter_type"]:checked').val();

        $.ajax({
            url: 'classes/cls_item_preservation_charges_list_master.php',
            type: 'POST',
            data: {
                action: 'pageSearch',
                filter_type: filter_type
            },
            success: function(response) {
                let newTbody = $(response).find('#searchMaster tbody');
                let newThead = $(response).find('#searchMaster thead');
                let oldTable = $('#searchMaster');

                let colCountOld = oldTable.find('thead th').length;
                let colCountNew = newThead.find('th').length;
                if (colCountOld !== colCountNew) {
                    if ($.fn.DataTable.isDataTable("#searchMaster")) {
                        $("#searchMaster").DataTable().destroy();
                    }
                    oldTable.replaceWith($(response).find('#searchMaster'));
                    table = $("#searchMaster").DataTable({
                        colReorder: true,
                        language: { emptyTable: "No records available." },
                        columnDefs: [
                            { "orderable": false, "targets": 0 },
                            { "className": "dt-head-left dt-body-left", "targets": "_all" }
                        ],
                        layout: {
                            topStart: {
                                buttons: [
                                    "colvis",
                                    {
                                        extend: "csvHtml5",
                                        filename: fileDate + "_item_preservation_charges",
                                        exportOptions: { columns: ":visible", header: true },
                                        customize: function (csv) {
                                            var prepend = '"' + companyTitle + '"\n"' + summaryTitle + '"\n\n';
                                            return prepend + csv;
                                        }
                                    },
                                    {
                                        extend: "excelHtml5",
                                        title: companyTitle + ' ' + summaryTitle,
                                        filename: fileDate + "_item_preservation_charges",
                                        exportOptions: { columns: ":visible", autoFilter: true }
                                    },
                                    {
                                        extend: "pdfHtml5",
                                        title: "",
                                        filename: fileDate + "_item_preservation_charges",
                                        customize: function (doc) {
                                            doc.content.splice(0, 0, {
                                                margin: [0, 0, 0, 0],
                                                columns: [
                                                    { text: currentDate, alignment: 'left', fontSize: 10 },
                                                    { text: companyTitle, alignment: 'center', fontSize: 14, bold: true },
                                                    { text: 'Page 1 of 1', alignment: 'right', fontSize: 10 }
                                                ]
                                            });
                                            doc.content.splice(1, 0, {
                                                canvas: [
                                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                                ],
                                                margin: [0, 0, 0, 2]
                                            });
                                            doc.content.splice(2, 0, {
                                                text: summaryTitle,
                                                style: 'header',
                                                alignment: 'center',
                                                margin: [0, 5, 0, 0],
                                                bold: true,
                                                fontSize: 12
                                            });
                                            doc.content.splice(3, 0, {
                                                canvas: [
                                                    { type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#000' }
                                                ],
                                                margin: [0, 0, 0, 8]
                                            });
                                            var tableNode = null;
                                            for (var i = 0; i < doc.content.length; i++) {
                                                if (doc.content[i].table !== undefined) {
                                                    tableNode = doc.content[i];
                                                    break;
                                                }
                                            }
                                            if (tableNode) {
                                                var colLen = tableNode.table.body[0].length;
                                                tableNode.table.widths = Array(colLen).fill('*');
                                            }
                                        }
                                    },
                                    {
                                        extend: "print",
                                        title: "",
                                        customize: function (win) {
                                            $(win.document.body).find('h1').remove();
                                            $(win.document.body).prepend(
                                                '<div style="width:100%;display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">' +
                                                    '<div style="font-size:12px;text-align:left;width:33%;">' + currentDate + '</div>' +
                                                    '<div style="font-size:16px;font-weight:bold;text-align:center;width:34%;">' + companyTitle + '</div>' +
                                                    '<div style="font-size:12px;text-align:right;width:33%;">Page 1 of 1</div>' +
                                                '</div>' +
                                                '<hr style="border:0;border-top:2px solid #000;margin:0 0 2px 0;">' +
                                                '<div style="text-align:center;font-weight:bold;font-size:15px;margin:8px 0 0 0;">' + summaryTitle + '</div>' +
                                                '<hr style="border:0;border-top:5px solid #000;margin:2px 0 6px 0;">'
                                            );
                                        }
                                    }
                                ]
                            }
                        }
                    });
                } else {
                    table.clear();
                    let rows = [];
                    newTbody.find('tr').each(function () {
                        let row = [];
                        $(this).find('td').each(function () {
                            row.push($(this).html());
                        });
                        if (row.length) rows.push(row);
                    });
                    table.rows.add(rows).draw();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error reloading grid: ", error);
            }
        });
    };
});
</script>

<?php
include("include/footer_close.php");
?>