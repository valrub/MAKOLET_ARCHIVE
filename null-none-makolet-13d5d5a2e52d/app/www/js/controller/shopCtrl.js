angular.module('shopCtrl', [])

.controller('shopCtrl', function($scope, $rootScope, $filter, $http, $state,
  $ionicLoading, $stateParams, $localStorage, API_URL, Loading, Shops, $controller) {

  $controller('abstractCtrl', {
    $scope: $scope
  });

  $scope.orderFromShop = function() {
    $rootScope.disabledAddress = true;
    $state.go("customerOrder");
  }

  $scope.shop = function() {
    $scope.markers = [];
    Loading.start();
    Shops.current({
      'id': $stateParams.id
    }).$promise.then(function(result) {
      $scope.shop = result.data;
      $rootScope.map = {
        center: {
          latitude: $scope.shop.latitude,
          longitude: $scope.shop.longitude,
        },
        control: {},
        zoom: 16
      };
      $rootScope.map.control = {}
      $scope.markers.push($scope.createMarker($scope.shop
        .id,
        $scope.shop.latitude, $scope.shop.longitude,
        $scope.shop.company_name,
        true,
        $scope.shop.type
      ));
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

});
