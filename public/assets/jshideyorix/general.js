function tampilPesan(tipe, desc, judul) {
    iziToast[tipe]({
        title: judul,
        message: desc,
        position: 'topRight'
    });
}

$("input").change(function () {
    $(this).closest('.form-group').find('input.form-control').removeClass('is-invalid');
    $(this).closest('.form-group').find('div.invalid-feedback').text('');
});
$("select").change(function () {
    $(this).closest('.form-group').find('select.form-control').removeClass('is-invalid');
    $(this).closest('.form-group').find('div.invalid-feedback').text('');
});

function check_int(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    return (charCode >= 46 && charCode <= 57 || charCode == 8 || charCode == 32 || charCode == 40 || charCode == 41 || charCode == 43);
}

function name_to_url(name) {
    name = name.toLowerCase(); // lowercase
    name = name.replace(/[-]/g, ''); // remove everything that is not [a-z] or -
    name = name.replace(/^\s+|\s+$/g, ''); // remove leading and trailing whitespaces
    name = name.replace(/\s+/g, '-'); // convert (continuous) whitespaces to one -
    return name;
}
