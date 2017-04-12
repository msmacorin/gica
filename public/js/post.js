/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var map, marker, infoWindow = null;

function fillSelectPostType() {
    $('#post_type').append('<option>Selecione...</option>');
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

function createMarker(latlng, html) {
//    var latlng = new google.maps.LatLng($('#latitude').val(), $('#longitude').val());
    var contentString = html;
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        zIndex: Math.round(latlng.lat() * -100000) << 5
    });

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(contentString);
        infowindow.open(map, marker);
    });

    return marker;
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

    map = new google.maps.Map(document.getElementById('map'), myOptions);

    infowindow = new google.maps.InfoWindow({
        size: new google.maps.Size(150, 50)
    });

    google.maps.event.addListener(map, 'click', function () {
        infowindow.close();
    });
    
    google.maps.event.addListener(map, 'click', function (event) {
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        marker = createMarker(event.latLng, "<b>Localização</b><br>" + event.latLng);
    });
}

function autocompleteInit() {
    $('.address_autocomplete').each(function () {
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
                createMarker();
            }
        });

        google.maps.event.addDomListener(document.getElementById('address'), 'keydown', function (e) {
            $('#latitude').val('');
            $('#longitude').val('');
        });
    });
}


$(document).ready(function () {
    $('#page-top').removeClass('home');
    $('#page-top').addClass('post');

    if (!$('#latitude').val()) {
        $('#latitude').val('-15.794157');
        $('#longitude').val('-47.882529');
    }

    $('.date').mask('00/00/0000');
    autocompleteInit();
    fillSelectPostType();
    initMap();
})