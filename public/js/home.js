/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
bootbox.setLocale('br');
var autocomplete, map;
var markers = [];

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
// https://davidwalsh.name/javascript-debounce-function
function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate)
                func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow)
            func.apply(context, args);
    };
}

function autocompleteInit() {
    autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('location'), {
        types: ['(cities)'],
        componentRestrictions: {'country': 'br'}
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            changeLocation(place.geometry.location.lat(), place.geometry.location.lng(), false);
        }
    });

    google.maps.event.addDomListener(document.getElementById('location'), 'keydown', function (e) {
        $('#latitude').val('');
        $('#longitude').val('');
    });
}

function changeLocation(lat, lng, searchTrigger) {
    $('#latitude').val(lat);
    $('#longitude').val(lng);
    localStorage.setItem('gosola_latitude', lat);
    localStorage.setItem('gosola_longitude', lng);
    if (searchTrigger && map != undefined) {
        $('#btnSearch').trigger('click');
    }
}

function browserLocalizacao(position) {
    changeLocation(position.coords.latitude, position.coords.longitude, true);
}

function ipLocalizacao() {
    $.getJSON("http://ip-api.com/json/?callback=?", function (data) {
        changeLocation(data['lat'], data['lon'], true);
    });
}

function geoLocalizacao() {
    if (localStorage.getItem('gosola_latitude')) {
        $('#latitude').val(localStorage.getItem('gosola_latitude'));
        $('#longitude').val(localStorage.getItem('gosola_longitude'));
    } else {
        //brasilia
        $('#latitude').val('-15.794157');
        $('#longitude').val('-47.882529');
        navigator.geolocation.getCurrentPosition(browserLocalizacao, ipLocalizacao);
    }
}

function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = new Array(0);
}

function addMarker(latitude, longitude, icon, content) {
    var latlng = new google.maps.LatLng(latitude, longitude);
    var marker = new google.maps.Marker({
        map: map,
        position: latlng,
        icon: icon
    });

    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(content);
        infowindow.open(map, marker);
    });

    markers.push(marker);
}

function search() {
    if (!$('#latitude').val()) {
        bootbox.alert({
            message: 'Selecione uma localidade.',
            size: 'small'
        });
        return false;
    }
    originalCenter = new google.maps.LatLng($("#latitude").val(), $("#longitude").val());
    $.ajax({
        url: '/posts/nearby/?latitude=' + $('#latitude').val() + '&longitude=' + $('#longitude').val(),
        type: 'GET',
        success: function (data) {
            clearMarkers();
            $.each(data, function (i, item) {
                addMarker(item.latitude, item.longitude, item.icon, item.content);
            });
        }
    })
}

function initMap() {
    var originalCenter = new google.maps.LatLng($("#latitude").val(), $("#longitude").val());

    // create the map
    var myOptions = {
        zoom: 14,
        center: originalCenter,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };

    var updateCenter = function (obj) {
        if (!obj.get('dragging') && !obj.get('resizing')
                && obj.get('oldCenter')
                && obj.get('oldCenter') !== obj.getCenter()) {
            center = obj.getCenter();
            var lat = center.lat();
            var lng = center.lng();

            $("#latitude").val(lat);
            $("#longitude").val(lng);
            debounceSearch();
        }
        if (!obj.get('dragging')) {
            obj.set('oldCenter', obj.getCenter())
        }
    };

    map = new google.maps.Map(document.getElementById('map'),
            myOptions);
    map.addListener('center_changed', function () {
        updateCenter(this);
    });

    // return the distance in meters
    google.maps.LatLng.prototype.distanceFrom = function (latlng) {
        var lat = [this.lat(), latlng.lat()]
        var lng = [this.lng(), latlng.lng()]
        var R = 6378137;
        var dLat = (lat[1] - lat[0]) * Math.PI / 180;
        var dLng = (lng[1] - lng[0]) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat[0] * Math.PI / 180) * Math.cos(lat[1] * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return Math.round(d);
    }

    infowindow = new google.maps.InfoWindow({
        size: new google.maps.Size(150, 50)
    });

    search();

    var debounceSearch = debounce(function () {
        var newCenter = new google.maps.LatLng($("#latitude").val(), $("#longitude").val());
        var dist = newCenter.distanceFrom(originalCenter);
        if (dist > 500) {
            search();
        }
    }, 1500);
}

function loadView() {
    $('section.content').css({'min-height': $(window).height()});
    $('#search').css({'margin-top': $('#mainNav').height()});
    $('#map').css({'height': $(window).height()});
    $('.page-scroll').removeClass('active');
}

$('#btnSearch').click(function () {
    map.setCenter(new google.maps.LatLng($("#latitude").val(), $("#longitude").val()));
    search();
});

$('#btnAdd').click(function () {
    var url = '/posts/view';
    if ($('#latitude').val()) {
        url += '/?latitude=' + $('#latitude').val() + '&longitude=' + $('#longitude').val();
    }
    window.location.href = url;
});

$(document).on('click', '#btnSend', function () {
    if ($('#contactForm').parsley().validate()) {
        $.ajax({
            type: 'POST',
            url: '/contact/send',
            data: $('#contactForm').serialize(),
            success: function (data) {
                $('#messageBlock .form-group').html('<p class="alert alert-success">' + data.message + '</p>');
                $('#messageBlock').show();
                $('#contactForm').trigger('reset');
            },
            error: function (data) {
                $('#messageBlock .form-group').html('<p class="alert alert-danger">' + data.message + '</p>');
                $('#messageBlock').show();
            }
        });
    }
});

$(window).resize(function () {
    loadView();
});

$(document).ready(function () {
    $('#page-top').addClass('home');
    
    $('.phone').mask('(00)0000-00009');
    loadView();
    geoLocalizacao();
    autocompleteInit();
    setTimeout(initMap, 1000);
});