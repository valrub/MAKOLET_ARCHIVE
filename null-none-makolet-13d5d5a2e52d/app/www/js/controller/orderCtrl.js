angular.module('orderCtrl', [])

.controller('customerOrderCtrl', function($scope, $rootScope, $filter, $http, $timeout,
  $ionicPopup, $ionicLoading, $stateParams, API_URL, $localStorage, $window,
  $ionicNavBarDelegate, $ionicScrollDelegate, Shops, Orders, Loading,
  $state, $ionicHistory, $controller, $ionicHistory, $ionicModal) {

  $controller('abstractCtrl', {
    $scope: $scope
  });
  $scope.currentStep = 0;
  $rootScope.orderInfo = [];

  $scope.openShop = function(id) {
    $scope.markersShop = [];
    Loading.start();
    Shops.current({
      'id': id
    }).$promise.then(function(result) {
      $scope.shop = result.data;
      $rootScope.mapShop = {
        center: {
          latitude: $scope.shop.latitude,
          longitude: $scope.shop.longitude,
        },
        control: {},
        zoom: 16
      };
      $rootScope.mapShop.control = {}
      $scope.markersShop.push($scope.createMarker($scope.shop
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
    $ionicModal.fromTemplateUrl('shop.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.modalShop = modal;
      $scope.openModalShop = function() {
        $scope.modalShop.show();
      };
      $scope.closeModalShop = function() {
        $scope.modalShop.hide();
      };
      $scope.openModalShop();
    });
  }

  $scope.customerOrders = function() {
    Loading.start();
    Orders.customerOrdersSummary().$promise.then(function(result) {
      $scope.orders = result;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.customerOrdersPaginate = function(page) {
    $scope.currentStatus = '';
    Loading.start();
    Orders.customerOrdersPaginate({
      'id': page
    }).$promise.then(function(result) {
      $scope.orders = result;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.removeOrder = function(id) {
    var confirmPopup = $ionicPopup.confirm({
      title: $filter('translate')('DELETE'),
      buttons: [{
        text: $filter('translate')('CANCEL')
      }, {
        text: $filter('translate')('OK'),
        type: 'button-positive',
        onTap: function(e) {
          if (id) {
            $scope.order.id = null;
            Loading.start();
            Orders.deleteOrder({
              'id': id
            }).$promise.then(function(result) {
              $ionicHistory.clearHistory();
              $ionicHistory.clearCache();
              $scope.order = {};
              angular.forEach($scope.steps, function(value, key) {
                value.active = false;
              });
              $scope.steps[0].active = true;
              $scope.date = new Date();
              $rootScope.notes = '';
              $scope.numberOrder = null;
              $scope.currentStep = 0;
              $rootScope.orderInfo = [];
              $rootScope.orderInfoCurrent = [];
              $scope.listOrder();
              Loading.finish();
              $state.go('customerOrder')
            }).catch(function(fallback) {
              Loading.error(fallback.data.error['message']);
            });
          }
        }
      }]
    });
  }

  $scope.refreshOrder = function(id) {
    if (id) {
      Loading.start();
      $scope.updateOrder(id);
      $scope.$broadcast('scroll.refreshComplete');
    } else {
      $scope.$broadcast('scroll.refreshComplete');
    }
  }

  $scope.openTab = function(index) {
    switch (index) {
      case 1:
        if ($scope.order.id) {
          $scope.readonly = true;
        }
        break;
      case 2:
        if ($scope.currentStep < index) {
          return 0;
        }
        if ($scope.order.id) {
          $scope.listShops();
          $scope.readonly = true;
        }
        break;
      case 3:
        if ($scope.currentStep < 3) {
          return 0;
        }
        if ($scope.order.id) {
          $scope.readonly = true;
        }
        break;
      case 4:
        if ($scope.currentStep < 3) {
          return 0;
        }
        if ($scope.order.status < 3) {
          return 0;
        }
        break;
    }
    angular.forEach($scope.steps, function(value, key) {
      if (value.id == index) {
        value.active = true;
      } else {
        value.active = false;
      }
    });
  }


  $scope.updateOrder = function(id) {
    Orders.customerOrder({
      'id': id,
    }).$promise.then(function(result) {
      $scope.numberOrder = result.data.id;
      $scope.date = result.data.created_at;
      switch (result.data.status) {
        case '1':
          $scope.currentStep = 3;
          break;
        case '2':
          $scope.currentStep = 3;
          break;
        default:
          $scope.currentStep = 4;
      }
      $scope.order = result.data;
      $rootScope.orderInfo = [];
      var index = 0;
      $rootScope.notes = result.data.customer_notes;
      angular.forEach(result.data.goods, function(value, key) {
        $rootScope.orderInfo.push({
          'id': value.id,
          'goods': value.name,
          'quantities': value.quantity
        })
      });
      angular.forEach($scope.steps, function(value, key) {
        if (value.id == $scope.currentStep) {
          value.active = true;
        } else {
          value.active = false;
        }
      });
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.items = 0;

  $scope.addOrderInfo = function() {
    $rootScope.orderInfoCurrent.push({
      'id': $scope.items,
      'goods': '',
      'quantities': 1
    });
    $scope.items++;
  }

  $scope.removeOrderInfo = function(item) {
    var index = $rootScope.orderInfoCurrent.indexOf(item);
    $rootScope.orderInfoCurrent.splice(index, 1);
  }

  $scope.proposalAction = function(proposal, accept) {
    Loading.start();
    $scope.proposals = {
      'proposal': proposal.id,
      'accept': accept
    };
    Orders.customerProposalsAccept($scope.proposals).$promise.then(function(
      result) {
      if (accept) {
        $scope.steps[2].active = false;
        $scope.steps[3].active = true;
        $scope.currentStep = 4;
        $scope.updateOrder($scope.order.id);
      } else {
        proposal.status = 3;
      }
      Loading.finish()
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.feedback = {};
  $scope.feedback.rating = 5;
  $scope.feedback.comment = '';
  $scope.sendRating = function(id) {
    $scope.feedback.order = id;
    Orders.feedbacks($scope.feedback).$promise.then(function(result) {
      $state.go("customerOrder");
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.listOrder = function() {
    for (var i = 0; i < 20; i++) {
      $scope.addOrderInfo();
    }
  }

  $scope.order = function() {
    $scope.order = {};
    $scope.markers = [];
    $scope.order.status = 0;
    if (!$rootScope.disabledAddress) {
      $rootScope.customAddress = false;
      $rootScope.address = $localStorage.user.customer.city + ' ' + $localStorage.user.customer.street + ' ' + $localStorage.user.customer.building + ' ' + $localStorage.user.customer.apartment;
    }
    $rootScope.disabledAddress = false;
    $rootScope.orderInfoCurrent = [];
    $rootScope.orderInfo = [];
    if ($stateParams.hasOwnProperty('id')) {
      Loading.start();
      $scope.statusUpdate = true;
      $scope.updateOrder($stateParams.id);
    } else {
      $scope.statusUpdate = false;
      Loading.start();
      Orders.last().$promise.then(function(result) {
        if (result.data.hasOwnProperty('id')) {
          if (result.data.status >= 4) {
            $scope.date = new Date();
            $rootScope.notes = '';
            $scope.numberOrder = null;
            $scope.listOrder();
            Loading.finish();
          } else {
            $scope.updateOrder(result.data.id);
            Loading.finish();
          }
        } else {
          $scope.date = new Date();
          $rootScope.notes = '';
          $scope.numberOrder = null;
          $scope.listOrder();
          Loading.finish();
        }
      }).catch(function(fallback) {
        if (fallback.status == 401) {
          $scope.reset();
          $state.go("login");
          Loading.finish();
        } else {
          var errorText = '';
          angular.forEach(fallback.data.error['fields'], function(value,
            key) {
            if (errorText == '')
              errorText = value;
          });
          Loading.error(errorText);
        }
      });
    }
  }

  $scope.stepSecond = function() {
    $scope.steps[0].active = false;
    $scope.steps[1].active = true;
    $scope.listShops();
  }

  $scope.stepThird = function() {
    Loading.start();
    var shops = [];
    var i = 0;
    angular.forEach($scope.shops, function(value, key) {
      if (value.checkbox) {
        shops[i] = value.id;
        i++;
      }
    });
    var goods = [];
    var quantities = [];
    i = 0;
    angular.forEach($rootScope.orderInfo, function(value, key) {
      goods[i] = value.goods;
      quantities[i] = value.quantities
      i++;
    });

    if (typeof $scope.objectAddress != "undefined") {} else {
      $scope.custom = {};
    }

    $scope.objectAddress = {};
    if ($rootScope.customAddress) {
      $scope.parseAddress($rootScope.customAddress);
      $scope.custom.apartment = $scope.custom.apartment ? $scope.custom.apartment : '';
      $scope.objectAddress.city = $rootScope.parseResult.city;
      $scope.objectAddress.street = $rootScope.parseResult.street;
      $scope.objectAddress.building = $rootScope.parseResult.building;
      $scope.objectAddress.entrance = '';
      $scope.objectAddress.apartment = $scope.custom.apartment;
      $scope.objectAddress.lat = $rootScope.lat;
      $scope.objectAddress.lng = $rootScope.lng;
    } else {
      $scope.objectAddress.city = $localStorage.user.customer.city;
      $scope.objectAddress.street = $localStorage.user.customer.street;
      $scope.objectAddress.building = $localStorage.user.customer.building;
      $scope.objectAddress.entrance = $localStorage.user.customer.entrance;
      $scope.objectAddress.apartment = $localStorage.user.customer.apartment;
      $scope.objectAddress.lng = $localStorage.user.customer.longitude;
      $scope.objectAddress.lat = $localStorage.user.customer.latitude;
    }
    if ($rootScope.customAddress && $scope.custom.apartment == '') {
      Loading.finish();
      $ionicPopup.show({
        template: '<input type="text" ng-model="custom.apartment">',
        title: $filter('translate')('APARTMENT'),
        scope: $scope,
        buttons: [{
          text: $filter('translate')('CANCEL')
        }, {
          text: $filter('translate')('OK'),
          type: 'button-positive',
          onTap: function(e) {
            if (!$scope.custom.apartment) {
              e.preventDefault();
            } else {
              return $scope.custom.apartment;
            }
          }
        }]
      });
      return false;
    }


    $scope.orders = angular.toJson({
      goods: goods,
      quantities: quantities,
      shops: shops,
      notes: $rootScope.notes,
      city: $scope.objectAddress.city,
      street: $scope.objectAddress.street,
      building: $scope.objectAddress.building,
      entrance: $scope.objectAddress.entrance,
      apartment: $scope.objectAddress.apartment,
      latitude: $scope.objectAddress.lat,
      longitude: $scope.objectAddress.lng
    });

    Orders.createOrders($scope.orders).$promise.then(function(result) {
      $scope.order = result.data;
      $scope.numberOrder = result.data.id;
      $scope.updateOrder($scope.numberOrder);
      $scope.currentStep = 3;
      $scope.steps[1].active = false;
      $scope.steps[2].active = true;
      $ionicScrollDelegate.resize();
      Loading.finish();
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
})

.controller('shopOrderCtrl', function($scope, $rootScope, $filter, $http, $timeout,
  $ionicPopup, $ionicLoading, $stateParams, API_URL, $localStorage, $window,
  $ionicNavBarDelegate, $ionicScrollDelegate, Shops, Orders, Loading,
  $state, $ionicHistory, $controller) {

  $controller('abstractCtrl', {
    $scope: $scope
  });


  $scope.prepareOrder = function() {
    $rootScope.shopNote = '';
    $rootScope.sendDelivery = '';
    $rootScope.sendPrice = '';  
  }



  $scope.orderRefresh = function() {
    Orders.shopOrder({
      'id': $stateParams.id
    }).$promise.then(function(result) {
      $scope.order = result.data;
      $scope.markers = [];
      $scope.markers.push($scope.createMarker(0,
        $localStorage.lat, $localStorage.lng, $localStorage.name, true, 0
      ));

      $rootScope.map.center = {
        latitude: $scope.order.latitude,
        longitude: $scope.order.longitude
      };

      $scope.markers.push($scope.createMarker($scope.order.id,
        $scope.order.latitude, $scope.order.longitude, '', false, 0
      ));

      var directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
      });
      var directionsService = new google.maps.DirectionsService();

      $scope.directions = {
        origin: new google.maps.LatLng($localStorage.lat, $localStorage.lng),
        destination: new google.maps.LatLng($scope.order.latitude, $scope.order.longitude),
        showList: false
      }
      var request = {
        origin: $scope.directions.origin,
        destination: $scope.directions.destination,
        travelMode: google.maps.DirectionsTravelMode.WALKING
      };
      directionsService.route(request, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
          directionsDisplay.setMap($rootScope.map.control.getGMap());
        } else {}
      });
      Loading.finish();
    }).catch(function(fallback) {
      Loading.finish();
      $state.go('shopOrders');
    });
  }

  $scope.order = function() {
    Loading.start();
    if ($stateParams.id == '') {
      Orders.last().$promise.then(function(result) {
        $state.go("shopOrder", {
          id: result.data.id
        });
      }).catch(function(fallback) {
        $ionicPopup.alert({
          title: $filter('translate')('ERROR_TITLE'),
          template: $filter('translate')('PENDING_PROPOSALS'),
          okText: $filter('translate')('OK')
        });
        $ionicHistory.goBack();
        Loading.finish();
      });
    }
    $scope.timeDelivery = 1800;
    $scope.timePickerCallback = function(val) {
      $scope.timeDelivery = parseInt(val / 60);
      if (typeof(val) === 'undefined') {
        $scope.timeOrder = null;
      } else {
        $scope.timeOrder = moment().seconds(val);
      }
    }

    $scope.timePickerObject = {
      inputEpochTime: $scope.timeDelivery,
      step: 1,
      format: 24,
      titleLabel: $filter('translate')('SELECT_TIME'),
      setLabel: $filter('translate')('SAVE'),
      closeLabel: $filter('translate')('CLOSE'),
      setButtonType: 'button-positive',
      closeButtonType: 'button-stable',
      callback: function(val) {
        $scope.timePickerCallback(val);
      }
    };

    $rootScope.map.center = {
      latitude: $localStorage.lat ? $localStorage.lat : 0,
      longitude: $localStorage.lng ? $localStorage.lng : 0,
    };

    Orders.shopOrder({
      'id': $stateParams.id
    }).$promise.then(function(result) {
      $scope.order = result.data;
      $scope.markers = [];
      $scope.markers.push($scope.createMarker(0,
        $localStorage.lat, $localStorage.lng, $localStorage.name, true, 0
      ));

      $rootScope.map.center = {
        latitude: $scope.order.latitude,
        longitude: $scope.order.longitude
      };

      $scope.markers.push($scope.createMarker($scope.order.id,
        $scope.order.latitude, $scope.order.longitude, '', false, 0
      ));

      var directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
      });
      var directionsService = new google.maps.DirectionsService();

      $scope.directions = {
        origin: new google.maps.LatLng($localStorage.lat, $localStorage.lng),
        destination: new google.maps.LatLng($scope.order.latitude, $scope.order.longitude),
        showList: false
      }
      var request = {
        origin: $scope.directions.origin,
        destination: $scope.directions.destination,
        travelMode: google.maps.DirectionsTravelMode.WALKING
      };
      directionsService.route(request, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
          directionsDisplay.setMap($rootScope.map.control.getGMap());
        } else {}
      });
      Loading.finish();
    }).catch(function(fallback) {
      Loading.finish();
      $state.go('shopOrders');
    });

  }

  $scope.closeOffer = function(id, price, delivery_price) {
    Loading.start();
    price = (price.toString()).replace(',', '.')
    delivery_price = (delivery_price.toString()).replace(',', '.')

    $scope.data = {
      'order': id,
      'price': parseFloat(price),
      'delivery_price': parseFloat(delivery_price),
    };
    Orders.closeOrder($scope.data).$promise.then(function(
      result) {
      Loading.finish();
      $state.go("shopSummary");
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.sendOffer = function() {
    Loading.start();
    var notes = $filter('translate')('SEND_DELIVERY') + ' ' + ($rootScope.sendDelivery ? $rootScope.sendDelivery : 0)  + ' ש"ח';
    notes += '\n' + $filter('translate')('SEND_PRICE') + ' ' + ($rootScope.sendPrice ? $rootScope.sendPrice : 0) + ' ש"ח';
    notes += '\n' + $rootScope.shopNote;

    $scope.proposals = {
      'proposal': $scope.order.proposal.id,
      'shop_notes': notes,
      'delivery_time': $scope.timeDelivery
    };
    Orders.shopProposalsPropose($scope.proposals).$promise.then(function(
      result) {
      Loading.finish();
      $state.go("shopSummary");
    }).catch(function(fallback) {
      Loading.finish();
      var errorText = '';
      angular.forEach(fallback.data.error['fields'], function(value, key) {
        if (errorText == '')
          errorText = value;
      });
      Loading.error(errorText);
    });
  }

  $scope.shopOrders = function() {
    Loading.start();
    $rootScope.name = $localStorage.name;
    Orders.shopOrders({
      'id': $localStorage.id
    }).$promise.then(function(result) {
      $scope.orders = result;
      $scope.$broadcast('scroll.refreshComplete');
      Loading.finish();
    }).catch(function(fallback) {
      $scope.$broadcast('scroll.refreshComplete');
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.shopOrdersPaginate = function(page) {
    $scope.currentStatus = '';
    Loading.start();
    Orders.shopOrdersPaginate({
      'id': page
    }).$promise.then(function(result) {
      $scope.orders = result;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.shopSummary = function() {
    Loading.start();
    $rootScope.name = $localStorage.name;
    Orders.shopSummary().$promise.then(function(result) {
      $scope.orders = result;
      $scope.$broadcast('scroll.refreshComplete');
      Loading.finish();
    }).catch(function(fallback) {
      $scope.$broadcast('scroll.refreshComplete');
      Loading.error(fallback.data.error['message']);
    });
  }

  $scope.shopSummaryPaginate = function(page) {
    $scope.currentStatus = '';
    Loading.start();
    Orders.summaryPaginate({
      'id': page
    }).$promise.then(function(result) {
      $scope.orders = result;
      Loading.finish();
    }).catch(function(fallback) {
      Loading.error(fallback.data.error['message']);
    });
  }

});
