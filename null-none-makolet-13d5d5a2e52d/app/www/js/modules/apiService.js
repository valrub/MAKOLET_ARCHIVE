angular.module('apiService', [])

.factory("Loading", function($ionicLoading, $filter, $ionicPopup) {
  return {
    start: function() {
      $ionicLoading.show({
        content: $filter('translate')('LOADING'),
        animation: 'fade-in',
        showBackdrop: true,
        maxWidth: 200,
        showDelay: 0
      });
    },
    finish: function() {
      $ionicLoading.hide();
    },
    error: function(message) {
      $ionicLoading.hide();
      $ionicPopup.alert({
        title: $filter('translate')('ERROR_TITLE'),
        template: message,
        okText: $filter('translate')('OK')
      });
    }
  }
})

.factory('Account', ['$resource', 'API_URL', 'Loading', function($resource,
  API_URL, Loading) {
  return $resource(API_URL, {}, {
    login: {
      method: 'POST',
      url: API_URL + '/api/auth/login'
    },
    register: {
      method: 'POST',
      url: API_URL + '/api/auth/register'
    },
    customerUpdateInfo: {
      method: 'PUT',
      url: API_URL + '/api/customers/:id'
    },
    customerUpdateFinance: {
      method: 'PUT',
      url: API_URL + '/api/customers/:id/card'
    },
    customer: {
      method: 'GET',
      url: API_URL + '/api/customers/me'
    },
    logOut: {
      method: 'POST',
      url: API_URL + '/api/auth/logout'
    },
    shopUpdate: {
      method: 'PUT',
      url: API_URL + '/api/shops/:id'
    },
    registerDeviceToken: {
      method: 'POST',
      url: API_URL + '/api/device'
    },
    unregisterDeviceToken: {
      method: 'DELETE',
      url: API_URL + '/api/device'
    }
  });
}])

.factory('Shops', ['$resource', 'API_URL', 'Loading', function($resource,
  API_URL, Loading) {
  return $resource(API_URL, {}, {
    all: {
      method: 'GET',
      url: API_URL + '/api/shops/'
    },
    current: {
      method: 'GET',
      url: API_URL + '/api/shops/:id'
    },
  });
}])

.factory('Orders', ['$resource', 'API_URL', 'Loading', function($resource,
  API_URL, Loading) {
  return $resource(API_URL, {}, {
    customerOrders: {
      method: 'GET',
      url: API_URL + '/api/orders'
    },
    customerOrdersSummary: {
      method: 'GET',
      url: API_URL + '/api/orders/summary'
    },
    customerOrdersPaginate: {
      method: 'GET',
      url: API_URL + '/api/orders/summary?page=:id'
    },
    customerOrder: {
      method: 'GET',
      url: API_URL + '/api/orders/:id'
    },
    createOrders: {
      method: 'POST',
      headers : {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      url: API_URL + '/api/orders'
    },
    shopSummary: {
      method: 'GET',
      url: API_URL + '/api/orders/summary'
    },
    shopSummaryPaginate: {
      method: 'GET',
      url: API_URL + '/api/orders/summary?page=:id'
    },
    shopOrders: {
      method: 'GET',
      url: API_URL + '/api/orders'
    },
    shopOrdersPaginate: {
      method: 'GET',
      url: API_URL + '/api/orders?page=:id'
    },
    shopProposalsPropose: {
      method: 'POST',
      url: API_URL + '/api/proposals/propose'
    },
    feedbacks: {
      method: 'POST',
      url: API_URL + '/api/feedbacks'
    },
    customerProposalsAccept: {
      method: 'POST',
      url: API_URL + '/api/proposals/accept'
    },
    shopOrder: {
      method: 'GET',
      url: API_URL + '/api/orders/:id'
    },
    deleteOrder: {
      method: 'DELETE',
      url: API_URL + '/api/orders/:id'
    },
    closeOrder: {
      method: 'POST',
      url: API_URL + '/api/orders/close'
    },
    last: {
      method: 'GET',
      url: API_URL + '/api/orders/last'
    }
  });
}]);
