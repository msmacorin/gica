/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



//exemplo de form com o bootbox.
//http://plnkr.co/edit/qYsJFeyxH9MIPAOBKbvr?p=preview

bootbox.setLocale('br');
var autocomplete;

function autocompleteInit() {
    autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('location'), {
        types: ['cities'],
        componentRestrictions: {'country': 'br'}
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            $('#latitude').val(place.geometry.location.lat());
            $('#longitude').val(place.geometry.location.lng());
        }
    });

    google.maps.event.addDomListener(document.getElementById('location'), 'keydown', function (e) {
        $('#latitude').val('');
        $('#longitude').val('');
    });
}

function browserLocalizacao(position) {
    $('#latitude').val(position.coords.latitude);
    $('#longitude').val(position.coords.longitude);
}

function ipLocalizacao() {
    $.getJSON("http://ip-api.com/json/?callback=?", function (data) {
        $('#latitude').val(data['lat']);
        $('#longitude').val(data['lon']);
    }).fail(function () {
        //brasilia
        $('#latitude').val('-15.794157');
        $('#longitude').val('-47.882529');
    });
}

function geoLocalizacao() {
    navigator.geolocation.getCurrentPosition(browserLocalizacao, ipLocalizacao);
}

function search() {
    if (!$('#latitude').val()) {
        bootbox.alert({
            message: 'Selecione uma localidade.',
            size: 'small'
        });
        return false;
    }

    $.ajax({
        url: '/posts/nearby/?latitude=' + $('#latitude').val() + '&longitude=' + $('#longitude').val(),
        type: 'GET',
        success: function (data) {

        }
    })
}

function initMap() {
    // create the map
    var myOptions = {
        zoom: 14,
        center: new google.maps.LatLng($('#latitude').val(), $('#longitude').val()),
        mapTypeControl: true,
        mapTypeControlOptions: {
            mapTypeIds: [
                google.maps.MapTypeId.ROADMAP,
            ]
        },
        navigationControl: true,
    };

    map = new google.maps.Map(document.getElementById('map'),
            myOptions);
}

function loadView() {
    $('section.content').css({'min-height': $(window).height()});
    $('#search').css({'margin-top': $('#mainNav').height()});
    $('#map').css({'height': $(window).height()});
    $('.page-scroll').removeClass('active');
}

$('#btnSearch').click(function () {
    search();
});

$('#btnAdd').click(function () {
    var url = '/post';
    if ($('#latitude').val()) {
        url += '/' + $('#latitude').val() + ',' + $('#longitude').val();
    }
    window.location.href = url;
})

$(window).resize(function () {
    loadView();
});

$(document).ready(function () {
    $('#page-top').removeClass('post');
    $('#page-top').addClass('home');

    loadView();
    if (!$('#latitude').val()) {
        geoLocalizacao();
    }
    autocompleteInit();
    setTimeout(initMap, 1000);
});