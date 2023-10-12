var arrayList = [];

function checkBulk() {
    if ($(event.target).is(':checked')) {
        arrayList.push($(event.target).val());
        console.log(arrayList);
    } else {
        var index = arrayList.indexOf($(event.target).val());
        if (index !== -1) {
            arrayList.splice(index, 1);
        }
    }
}

function checkall() {
    $("#check-all").click(function () {
        if ($(this).is(':checked')) {
            $(".data-check").prop('checked', $(this).prop('checked'));
            $(".data-check:checked").each(function () {
                var index = arrayList.indexOf(this.value);
                if (index === -1) {
                    arrayList.push(this.value);
                }
            });
        } else {
            $(".data-check").prop('checked', false);
            $(".data-check").each(function () {
                var index = arrayList.indexOf(this.value);
                console.log(arrayList);
                if (index !== -1) {
                    arrayList.splice(index, 1);
                }
            });

        }
    });
}

function initCountpage() {
    $('#page_count').change(function () {
        getViewData(1);
    });
}
