var DatatableResponsive = function() {

    // Lightbox
    var _componentFancybox = function() {
        if (!$().fancybox) {
            console.warn('Warning - fancybox.min.js is not loaded.');
            return;
        }

        // Image lightbox
        $('[data-popup="lightbox"]').fancybox({
            padding: 3
        });
    };

    var _componentDatatableResponsive = function() {
        if (!$().DataTable) {
            console.warn('Warning - datatables.min.js is not loaded.');
            return;
        }

        // Setting datatable defaults
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                orderable: false,
                width: 100,
                //targets: [ 0 ]
            }],
            dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            }
        });


        // Basic responsive configuration
        $('.datatable-responsive').DataTable();

    };


    // Select2 for length menu styling
    var _componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        }

        // Initialize
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true,
            width: 'auto'
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentFancybox();
            _componentDatatableResponsive();
            _componentSelect2();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    DatatableResponsive.init();
});

function onEditProfile() {
    $("#profile-modal input[id^='my']").val('');
    $("#profile-modal").modal('show');
}

$("#save-profile").on("click", () => {

    if ($("#my-password").val().trim() == "") {
        alert("Please input new password.")
        return;
    }

    if ($("#my-password").val() != $("#my-confirm-password").val()) {
        alert("New password does not match");
        return;
    }

    $.post( baseUrl + "/editProfile", {
        "_token": Laravel.csrfToken,
        "oldPassword": $("#my-current-password").val(),
        "newPassword": $("#my-password").val()
    }, function (data, status) {
        if (status == "success") {

            if(data.message == "OK") {
                $("#profile-modal").modal('hide');
            } else {
                alert(data.message);
            }
        }
    });
});
