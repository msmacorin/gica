/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function changeStatus(status, id) {
    var data = {'status': status, 'id': id};
    $.ajax({
        url: prefix_url + '/admin/update-posts',
        type: 'POST',
        data: data,
        beforeSend: function (request) {
            request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="_token"]').attr('content'));
        },
        success: function () {
            $('#' + id).remove();
        }
    });
}

function search(type) {
    var url = prefix_url + '/admin/get-posts/?status=' + type;
    $.ajax({
        url: url,
        success: function (data) {
            $('#tablePost').html(data);
        }
    });
}

$('#post_status').on('change', function () {
    search(this.value);
});

$(document).ready(function () {
    $('#page-top').addClass('admin');
});