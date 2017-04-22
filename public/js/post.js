/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var map, marker, infoWindow = null;

function fillSelectPostType() {
    $('#post_type').append('<option value="">Selecione...</option>');
    $.ajax({
        url: '/post-types/types',
        type: 'GET',
        success: function (data) {
            var opts = $.parseJSON(data);
            $.each(opts, function (i, d) {
                $('#post_type').append('<option value="' + d.id + '">' + d.name + '</option>');
            });
        }
    });
}

function createMarker() {
    if (marker) {
        marker.setMap(null);
        marker = null;
    }

    var latlng = new google.maps.LatLng($('#latitude').val(), $('#longitude').val());
    map.setCenter(latlng);
    var contentString = '<b>Localização</b><br>' + $('#address').val() + '<br>' + latlng;
    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        zIndex: Math.round(latlng.lat() * -100000) << 5
    });

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(contentString);
        infowindow.open(map, marker);
    });
}

function getAddress() {
    var url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=';
    url += $('#latitude').val() + ',' + $('#longitude').val();
    url += '&sensor=true';
    $.ajax({
        url: url,
        dataType: 'json',
        success: function (data) {
            if (data.results && data.results.length > 0) {
                var address = data.results[0].address_components[1].long_name; // rua
                address += ' - ' + data.results[0].address_components[2].long_name; // bairro
                address += ', ' + data.results[0].address_components[3].long_name; // cidade
                address += ' - ' + data.results[0].address_components[5].short_name; // estado
                address += ', ' + data.results[0].address_components[6].long_name; // pais

                $('#address').val(address);
                createMarker();
            }
        }
    });
}

function initMap() {
    // create the map
    var myOptions = {
        zoom: 14,
        center: new google.maps.LatLng($('#latitude').val(), $('#longitude').val()),
        scrollwheel: false,        
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    map = new google.maps.Map(document.getElementById('map'), myOptions);
    infowindow = new google.maps.InfoWindow({
        size: new google.maps.Size(150, 50)
    });
    google.maps.event.addListener(map, 'click', function () {
        infowindow.close();
    });
    google.maps.event.addListener(map, 'click', function (event) {
        $('#latitude').val(event.latLng.lat());
        $('#longitude').val(event.latLng.lng());
        getAddress();
    });
}

function autocompleteInit() {
    var autocomplete;
    autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('address'), {
        types: ['geocode'],
        componentRestrictions: {'country': 'br'}
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            $('#latitude').val(place.geometry.location.lat());
            $('#longitude').val(place.geometry.location.lng());
            marker = createMarker();
        }
    });

    google.maps.event.addDomListener(document.getElementById('address'), 'keydown', function (e) {
        $('#latitude').val('');
        $('#longitude').val('');
    });
}

$(document).on('click', '#btnSend', function () {
    if ($('#postForm').parsley().validate()) {
        if (!$('#latitude').val()) {
            bootbox.alert('Por favor, selecione um local para a ocorrência!');
            return false;
        }
        
        $('#postForm').submit();
    }
});

$(document).ready(function () {
    $('#page-top').removeClass('home');
    $('#page-top').addClass('post');

    if (!$('#latitude').val()) {
        $('#latitude').val('-15.794157');
        $('#longitude').val('-47.882529');
    }

    autocompleteInit();
    fillSelectPostType();
    initMap();
    $('.date').mask('00/00/0000');
});