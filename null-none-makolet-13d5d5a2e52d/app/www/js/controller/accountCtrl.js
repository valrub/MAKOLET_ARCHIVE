angular.module('accountCtrl', [])

.controller('tabsCtrl', function($scope, $rootScope, $localStorage, $state) {
  $scope.initTabs = function() {
    if ($localStorage.tabs) {
      $rootScope.tabs = true;
      $rootScope.client = $localStorage.client;
    } else {
      $state.go("login");
      $rootScope.tabs = false;
    }
  }
})


.controller('customerSettingsCtrl', function($scope, $rootScope, $controller,
  $localStorage, Account, Loading) {

  $scope.pushEnable = $rootScope.deviceToken ? true : false;

  $controller('abstractCtrl', {
    $scope: $scope
  });

  $scope.pushStatus = function() {
    Loading.start();
    $scope.pushEnable = !$scope.pushEnable;
    if ($scope.pushEnable) {
      $scope.request = {
        'device_token': $rootScope.deviceToken,
        'device_type': $rootScope.deviceType
      }
      Account.registerDeviceToken($scope.request).$promise.then(function(result) {
        console.log(result);
        Loading.finish();
      }).catch(function(fallback) {
        Loading.finish();
      });
    } else {
      $scope.request = {
        'device_token': $rootScope.deviceToken,
        'device_type': $rootScope.deviceType
      }
      Account.unregisterDeviceToken($scope.request).$promise.then(function(result) {
        console.log(result);
        Loading.finish();
      }).catch(function(fallback) {
        Loading.finish();
      });
    }
  }

  $scope.customerUpdateInfo = function() {
    Loading.start();
    var address = $scope.user.city + ' ' + $scope.user.street + ' ' +
      $scope.user.building;
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'address': address
    }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var lat = results[0].geometry.location.lat();
        var lng = results[0].geometry.location.lng();
      } else {
        var lat = 0;
        var lng = 0;
      }
      $scope.user.latitude = $rootScope.lat = $localStorage.lat = lat;
      $scope.user.longitude = $rootScope.lng = $localStorage.lng =
        lng;
      Account.customerUpdateInfo($scope.user, {
        'id': $localStorage.id
      }).$promise.then(function(result) {
        Loading.finish();
      }).catch(function(fallback) {
        Loading.error(fallback.data.error['message']);
      });
      Loading.finish();
    });
  }

  $scope.customerUpdateFinance = function() {
    Loading.start();
    Account.customerUpdateFinance({
      'id': $localStorage.id
    }, $scope.user).$promise.then(function(result) {
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

})

.controller('shopSettingsCtrl', function($scope, $rootScope, $controller,
  $localStorage, Account, Shops, Loading) {

  $scope.pushEnable = $rootScope.deviceToken ? true : false;

  $controller('abstractCtrl', {
    $scope: $scope
  });

  $scope.pushStatus = function() {
    Loading.start();
    $scope.pushEnable = !$scope.pushEnable;
    if ($scope.pushEnable) {
      $scope.request = {
        'device_token': $rootScope.deviceToken,
        'device_type': $rootScope.deviceType
      }
      Account.registerDeviceToken($scope.request).$promise.then(function(result) {
        console.log(result);
        Loading.finish();
      }).catch(function(fallback) {
        Loading.finish();
      });
    } else {
      $scope.request = {
        'device_token': $rootScope.deviceToken,
        'device_type': $rootScope.deviceType
      }
      Account.unregisterDeviceToken($scope.request).$promise.then(function(result) {
        console.log(result);
        Loading.finish();
      }).catch(function(fallback) {
        Loading.finish();
      });
    }
  }

  $scope.shopCurrent = function() {
    Loading.start();
    Shops.current({
      'id': $localStorage.id
    }).$promise.then(function(result) {
      $scope.shop = result.data;
      $localStorage.lat = $rootScope.lat = $scope.shop.latitude;
      $localStorage.lng = $rootScope.lng = $scope.shop.longitude;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.shopUpdate = function() {
    Loading.start();
    var address = $scope.shop.city + ' ' + $scope.shop.street + ' ' +
      $scope.shop.building;
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'address': address
    }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var lat = results[0].geometry.location.lat();
        var lng = results[0].geometry.location.lng();
      } else {
        var lat = 0;
        var lng = 0;
      }
      $scope.shop.latitude = $rootScope.lat = $localStorage.lat = lat;
      $scope.shop.longitude = $rootScope.lng = $localStorage.lng =
        lng;
      Account.shopUpdate($scope.shop, {
        'id': $localStorage.id
      }).$promise.then(function(result) {
        Loading.finish();
      }).catch(function(fallback) {
        var errorText = '';
        angular.forEach(fallback.data.error['fields'], function(
          value,
          key) {
          if (errorText == '')
            errorText = value;
        });
        Loading.error(errorText);
      });
    });
  }
})

.controller('accountCtrl', function($scope, $rootScope, $location, $http,
  $localStorage,
  $ionicLoading, $filter, $timeout, $controller, $ionicPopup, $state,
  $window,
  $ionicHistory, API_URL, $localStorage, Account, Loading, Shops) {

  $controller('abstractCtrl', {
    $scope: $scope
  });

  $scope.passwordActions = function(event) {
    if (angular.element(event.target).hasClass('ion-eye')) {
      angular.element(event.target).addClass('ion-eye-disabled');
      angular.element(event.target).removeClass('ion-eye');
      angular.element(event.target).parent().find('input').eq(0).attr('type', 'text');
    } else {
      angular.element(event.target).removeClass('ion-eye-disabled');
      angular.element(event.target).addClass('ion-eye');
      angular.element(event.target).parent().find('input').eq(0).attr('type', 'password');
    }
  }

  $scope.newMarket = function() {
    if ($scope.phoneMarket) {
      $scope.isMarketRegister = false;
      $scope.phoneMarket = '';
      $timeout(function() {
        Loading.finish();
      }, 1500);
      Loading.start();
    } else {
      $scope.isMarketRegister = true;
    }
  }

  $scope.signUp = function() {
    $scope.user = {};
    $scope.user.email = $scope.email;
    $scope.user.first_name = $scope.firstName;
    $scope.user.last_name = $scope.lastName;
    $scope.user.phone = $scope.phone;
    $scope.user.city = $scope.city;
    $scope.user.street = $scope.street;
    $scope.user.building = $scope.building;
    $scope.user.entrance = $scope.entrance;
    $scope.user.apartment = $scope.apartment;
    $scope.user.password = $scope.password;
    $scope.user.device_token = $rootScope.deviceToken;
    $scope.user.device_type = $rootScope.deviceType;

    Loading.start();
    Account.register($scope.user).$promise.then(function(result) {
      $scope.login();
    }).catch(function(fallback) {
      var errorText = '';
      angular.forEach(fallback.data.error['fields'], function(value,
        key) {
        if (errorText == '')
          errorText = value;
      });
      Loading.error(errorText);
    });
  }

  $scope.init = function() {
    Loading.start();
    $timeout(function() {
      $rootScope.client = $localStorage.client;
      switch ($rootScope.client) {
        case 'customer':
          $state.go("customerOrder");
          Loading.finish();
          break;
        case 'shop':
          $state.go("shopSummary");
          Loading.finish();
          break;
        default:
          $rootScope.user = false;
          Loading.finish();
      }
    }, 500);
  }

  $scope.login = function() {
    $scope.user = {};
    $scope.user.email = $scope.email;
    $scope.user.password = $scope.password;
    $scope.user.device_token = $rootScope.deviceToken;
    $scope.user.device_type = $rootScope.deviceType;
    Loading.start();
    Account.login($scope.user).$promise.then(function(result) {
      $localStorage.token = result.token;
      $http.defaults.headers.common['Authorization'] = 'Bearer ' +
        $localStorage.token;
      if (result.user.type == 1) {
        $localStorage.id = result.user.customer.id;
        $rootScope.client = $localStorage.client = 'customer';
        $localStorage.user = result.user;
        $localStorage.lat = $rootScope.lat = result.user.customer.latitude;
        $localStorage.lng = $rootScope.lng = result.user.customer.longitude;
        $rootScope.address = result.user.customer.city + ' ' + result
          .user.customer.street + ' ' +
          result.user.customer.building + ' ' +
          result.user.customer.apartment;
        if (result.user.customer['solvent']) {
          $state.go("customerOrder");
        } else {
          $state.go("customerFinance");
        }
      } else {
        $localStorage.id = result.user.shop.id;
        $localStorage.name = result.user.shop.name;
        $localStorage.lat = $rootScope.lat = result.user.shop.latitude;
        $localStorage.lng = $rootScope.lng = result.user.shop.longitude;
        $rootScope.client = $localStorage.client = 'shop';
        $state.go("shopSummary");
      }
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }
});
