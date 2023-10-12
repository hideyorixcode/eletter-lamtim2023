var table;
var array_data = [];

$('#hideyori_datatable').on('page.dt', function () {
    $("#check-all").prop('checked', false);
});

$("#check-all").click(function () {
    if ($(this).is(':checked')) {
        $(".data-check").prop('checked', $(this).prop('checked'));
        $(".data-check:checked").each(function () {
            var index = array_data.indexOf(this.value);
            if (index === -1) {
                array_data.push(this.value);
            }
        });
        var rows = $('#hideyori_datatable').find('tbody tr');
        rows.addClass('table-secondary');
    } else {
        $(".data-check").prop('checked', false);
        $(".data-check").each(function () {
            var index = array_data.indexOf(this.value);
            console.log(array_data);
            if (index !== -1) {
                array_data.splice(index, 1);
            }
        });
        var rows = $('#hideyori_datatable').find('tbody tr');
        rows.removeClass('table-secondary');
    }
});


$('#hideyori_datatable').on('click', '.data-check', function () {
    if ($(this).is(':checked')) {
        $(this).closest('tr').addClass('table-secondary');
    } else {
        $(this).closest('tr').removeClass('table-secondary');
    }
});


function reloadTable() {
    table.ajax.reload();
}
