/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function changeStatus(status, id) {
    $.ajax({
        url: '/admin/posts/?id=' + id + '&status=' + status,
        type: 'POST',
        beforeSend: function (request) {
            request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="_token"]').attr('content'));
        },
        success: function () {
            $('#' + id).remove();
        }
    });
}

function search(type) {
    var url = '/admin/posts/?status=' + type;
    $.ajax({
        url: url,
        success: function (data) {
            $('#tablePost').html(data);
        }
    });
}

$('#post_status').on('change', function () {
    search(this.value);
})

$(document).ready(function () {
})
