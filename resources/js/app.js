



let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

elems.forEach(function(html) {
    let switchery = new Switchery(html,  { size: 'small' });
});

//Block usuario
$(document).ready(function(){
    $('#users').DataTable();
    $('.js-switch').change(function () {
        let block = $(this).prop('checked') === true ? 1 : 0;
        let userId = $(this).data('id');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('users.block') }}",
            data: {'block': block, 'user_id': userId},
            success: function (data) {
                toastr.options.closeButton = true;
                toastr.options.closeMethod = 'fadeOut';
                toastr.options.closeDuration = 100;
                toastr.info(data.message);
            }
        });
    });    
});


