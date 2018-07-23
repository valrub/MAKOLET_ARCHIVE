angular.module('products', [])

.directive('addProducts', function($ionicModal, $rootScope, $controller){
  return {
    restrict: 'A',
    scope: {
      location: '='
    },
    link: function($scope, element){
      $controller('customerOrderCtrl', {
        $scope: $scope
      });
      $ionicModal.fromTemplateUrl('products.html', {
        scope: $scope,
      }).then(function(modal) {
        $scope.modal = modal;
      });
      element.bind('click', function() {
        $scope.open();
      });
      $scope.open = function() {
        $scope.modal.show();
      };
      $scope.save = function() {
        $rootScope.orderInfo = [];
        angular.forEach($rootScope.orderInfoCurrent, function(value, key) {
          if (value.goods) {
            $rootScope.orderInfo.push({
              'id': value.id,
              'goods': value.goods,
              'quantities': value.quantities
            })
          }
        });
        $scope.modal.hide();
      }
      $scope.close = function() {
        $scope.modal.hide();
      };
    }
  }
})
