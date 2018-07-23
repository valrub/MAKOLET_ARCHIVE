angular.module('abstractCtrl', [])

.controller('abstractCtrl', function($scope, $rootScope, $filter, $http, $timeout,
  $ionicPopup, $ionicLoading, $stateParams, API_URL, $localStorage, $window,
  $ionicNavBarDelegate, $ionicScrollDelegate, Shops, Orders, Loading, Account, $ionicModal,
  $state, $ionicHistory, $ionicPopup, $timeout, $ionicScrollDelegate, $ionicPlatform) {

  $rootScope.markers = [];
  $rootScope.map = {
    center: {
      latitude: $localStorage.hasOwnProperty('lat') ? 0 : $localStorage.hasOwnProperty('lat'),
      longitude: $localStorage.hasOwnProperty('lng') ? 0 : $localStorage.hasOwnProperty('lng'),
    },
    options: {
        scrollwheel: false,
        draggable: false
    },
    control: {},
    zoom: 16,
  };

  $scope.customerCurrent = function() {
    Loading.start();
    Account.customer().$promise.then(function(result) {
      $scope.user = result.data;
      $rootScope.userInfo = $scope.user;
      $localStorage.lat = $scope.user.latitude;
      $localStorage.lng = $scope.user.longitude;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }


  $scope.openHelp = function() {
    $ionicModal.fromTemplateUrl('help.html', {
       scope: $scope,
       animation: 'slide-in-up'
     }).then(function(modal) {
       $scope.modalHelp = modal;
       $scope.openHelp = function() {
         $scope.modalHelp.show();
       };
       $scope.closeHelp = function() {
         $scope.modalHelp.hide();
       };
       $scope.openHelp();
     });
  }

  $scope.openTermsOfUse = function() {
    $ionicModal.fromTemplateUrl('terms-of-use.html', {
       scope: $scope,
       animation: 'slide-in-up'
     }).then(function(modal) {
       $scope.modalTermsOfUse = modal;
       $scope.openTermsOfUse = function() {
         $scope.modalTermsOfUse.show();
       };
       $scope.closeTermsOfUse = function() {
         $scope.modalTermsOfUse.hide();
       };
       $scope.openTermsOfUse();
     });
  }

  $scope.steps = [{
    'name': $filter('translate')('LIST_OF_GOODS'),
    'id': 1,
    'active': true
  }, {
    'name': $filter('translate')('SHIPPING_ADDRESS'),
    'id': 2,
    'active': false
  }, {
    'name': $filter('translate')('PROPOSALS'),
    'id': 3,
    'active': false
  }, {
    'name': $filter('translate')('ORDER_STATUS'),
    'id': 4,
    'active': false
  }];

  $scope.reset = function() {
    $scope.device = {};
    $scope.device.device_token = $rootScope.deviceToken;
    Account.logOut($scope.device).$promise.then(function(result) {
      $ionicHistory.clearCache();
      $localStorage.$reset();
      for (var prop in $rootScope) {
          if (prop.substring(0,1) !== '$') {
              if (prop != 'deviceToken' && prop != 'deviceType') {
                delete $rootScope[prop];
              }
          }
      }
      $rootScope.client = 'empty';
      $state.go("login");
    }).catch(function(fallback) {
      $ionicHistory.clearCache();
      $localStorage.$reset();
      for (var prop in $rootScope) {
          if (prop.substring(0,1) !== '$') {
              if (prop != 'deviceToken' && prop != 'deviceType') {
                delete $rootScope[prop];
              }
          }
      }
      $rootScope.client = 'empty';
      $state.go("login");
    });
  }

  $scope.listShops = function() {
    Loading.start();
    $rootScope.shops = [];
    $rootScope.markers = [];

    $scope.coordinate = {}
    if ($rootScope.customAddress) {
      $rootScope.markers.push($scope.createMarker(0,
        $rootScope.lat, $rootScope.lng, '', false, 0
      ));
      $scope.coordinate.lat = $rootScope.lat;
      $scope.coordinate.lng = $rootScope.lng;
    } else {
      $rootScope.markers.push($scope.createMarker(0,
        $localStorage.lat, $localStorage.lng, '', false, 0
      ));
      $scope.coordinate.lat = $localStorage.lat;
      $scope.coordinate.lng = $localStorage.lng;
    }

    Shops.all($scope.coordinate).$promise.then(function(result) {
      if ($rootScope.customAddress) {
        $rootScope.map.center = {
          latitude: $rootScope.lat,
          longitude: $rootScope.lng
        };
      } else {
        $rootScope.map.center = {
          latitude: $localStorage.lat,
          longitude: $localStorage.lng
        };
      }

      if ($scope.hasOwnProperty('order')) {
        if ($scope.order.hasOwnProperty('id')) {
          $rootScope.map.center = {
            latitude: $scope.order.latitude,
            longitude: $scope.order.longitude
          };
        }
      }

      $rootScope.shops = result.data;
      angular.forEach($rootScope.shops, function(value, key) {
        value.checkbox = true;
      });
      angular.forEach($rootScope.shops, function(value, key) {
        $rootScope.markers.push($scope.createMarker(value.id,
          value.latitude, value.longitude, value.company_name,
          true, value.type
        ));
      });
      Loading.finish();
      $timeout(function() {
        $rootScope.map.control.refresh();
        $rootScope.map.center = { latitude: $rootScope.map.center.latitude,
                                  longitude: $rootScope.map.center.longitude};
      }, 1000);
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.coordinatesToAddress = function(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'latLng': latlng,
    }, function(results, status) {
      var city = '';
      var street = '';
      var house = '';
      if (status == google.maps.GeocoderStatus.OK) {
        var street = "";
        var city = "";
        var building = "";
        for (var i = 0; i < results[0].address_components.length; i++) {
          var addr = results[0].address_components[i];
          if (addr.types[0] == 'street_address') {
            street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
          } else if (addr.types[0] == 'establishment') {
            street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
          } else if (addr.types[0] == 'route') {
            street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
          } else if (addr.types[0] == ['locality']) {
            city = addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
          } else if (addr.types[0] == 'street_number') {
            building = addr.short_name == 'Unnamed Road' ? '' : addr.short_name;
          }
        }
        $rootScope.city = city;
        $rootScope.street = street;
        $rootScope.building = building;
      }
    });
  }

  $scope.parseAddress = function(results) {
    $rootScope.parseResult = {}
    var street = '';
    var city = '';
    var building = '';
    for (var i = 0; i < results.address_components.length; i++) {
      var addr = results.address_components[i];
      if (addr.types[0] == 'street_address') {
        street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
        $rootScope.parseResult.street = street;
      } else if (addr.types[0] == 'establishment') {
        street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
        $rootScope.parseResult.street = street;
      } else if (addr.types[0] == 'route') {
        street += addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
        $rootScope.parseResult.street = street;
      } else if (addr.types[0] == ['locality']) {
        city = addr.long_name == 'Unnamed Road' ? '' : addr.long_name;
        $rootScope.parseResult.city = city;
      } else if (addr.types[0] == 'street_number') {
        building = addr.short_name == 'Unnamed Road' ? '' : addr.short_name;
        $rootScope.parseResult.building = building;
      }
    }
  }


  $scope.getCoordinate = function(address) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'address': address
    }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        return {
          'lat': results[0].geometry.location.lat().toFixed(5),
          'lng': results[0].geometry.location.lng().toFixed(5)
        }
      }
    });
  };

  $scope.createMarker = function(i, latitude, longitude, title, shop, type) {
    idKey = "id";
    if (shop) {
      switch (parseInt(type)) {
        case 1:
          icon = 'https://dl.dropboxusercontent.com/u/58070958/makolet/sal24x24.png';
          break;
        case 2:
          icon = 'https://dl.dropboxusercontent.com/u/58070958/makolet/donkey24x24.png';
          break;
        default:
          icon = 'https://dl.dropboxusercontent.com/u/58070958/makolet/sal24x24.png';
          break;
      }

      options = {
        labelClass: 'marker-labels',
        labelAnchor: '30 -2',
        labelContent: title
      }
    } else {
      icon =
        'https://dl.dropboxusercontent.com/u/58070958/makolet/customer_icon.png';
      options = {}
    }
    var ret = {
      latitude: latitude,
      longitude: longitude,
      title: title,
      icon: icon,
      options: options
    };
    ret[idKey] = i;
    return ret;
  };

});
