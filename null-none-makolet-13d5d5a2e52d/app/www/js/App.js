var app = angular.module('makoletApp', ['ionic', 'autocompleteIonic',
  'ngCordova', 'ngStorage', 'ionic.rating', 'uiGmapgoogle-maps',
  'abstractCtrl', 'shopCtrl', 'accountCtrl', 'orderCtrl',
  'pascalprecht.translate', 'ui.utils.masks', 'ngResource', 'apiService',
  'ionic-timepicker', 'yaru22.angular-timeago', 'angularMoment',
  'validation', 'validation.rule', 'products'
]);

app.service('authInterceptor', function($q, $window, $localStorage) {
  var service = this;

  service.responseError = function(response) {
    if (response.status == 401) {
      $localStorage.$reset();
      $window.location = "/#/login";
    }
    if (response.status == 429) {
      $window.location.reload(true);
    }
    return $q.reject(response);
  };
});

app.config(function($stateProvider, $urlRouterProvider, $httpProvider, $localStorageProvider,
  $ionicConfigProvider, $translateProvider, uiGmapGoogleMapApiProvider, $validationProvider) {

  $httpProvider.interceptors.push('authInterceptor');
  $ionicConfigProvider.views.transition('none');

  $validationProvider.showErrorMessage = false;
  $validationProvider.showSuccessMessage = false;

  $validationProvider.validCallback = function(element) {
    angular.element(element).parent().removeClass('has-error').addClass('has-success-tick');
  };
  $validationProvider.invalidCallback = function(element) {
    angular.element(element).parent().removeClass('has-success-tick').addClass('has-error');
  };

  $translateProvider.useSanitizeValueStrategy(null);

  $translateProvider
    .useStaticFilesLoader({
      prefix: 'languages/',
      suffix: '.json'
    })
    .registerAvailableLanguageKeys(['il', 'en'], {
      'il': 'il',
      'en': 'en',
    })
    .preferredLanguage('en');

  $urlRouterProvider.otherwise('/login');

  $stateProvider.state('login', {
    url: '/login',
    templateUrl: 'templates/login.html',
    cache: false,
    controller: 'accountCtrl'
  }).state('signUp', {
    url: '/sign_up',
    cache: false,
    templateUrl: 'templates/sign-up.html',
    controller: 'accountCtrl'
  });

  $stateProvider.state('customerSettings', {
    url: '/customer/settings',
    cache: false,
    templateUrl: 'templates/customer/settings.html',
    controller: 'customerSettingsCtrl'
  }).state('customerFinance', {
    url: '/customer/settings/finance',
    cache: false,
    templateUrl: 'templates/customer/tranzilla.html',
    controller: 'customerSettingsCtrl'
  }).state('feedback', {
    url: '/feedback',
    cache: false,
    templateUrl: 'templates/feedback.html'
  });

  $stateProvider.state('shopSettings', {
    url: '/shop/settings',
    cache: false,
    templateUrl: 'templates/shop/settings.html',
    controller: 'shopSettingsCtrl'
  }).state('shopFinance', {
    url: '/shop/finance',
    cache: false,
    templateUrl: 'templates/shop/finance.html',
    controller: 'shopSettingsCtrl'
  });

  $stateProvider.state('customerShops', {
    url: '/customer/shops',
    cache: false,
    templateUrl: 'templates/customer/shops.html',
    controller: 'shopCtrl'
  }).state('customerShop', {
    url: '/customer/shops/:id',
    cache: false,
    templateUrl: 'templates/customer/shop.html',
    controller: 'shopCtrl'
  });

  $stateProvider.state('customerOrders', {
    url: '/customer/orders',
    cache: false,
    templateUrl: 'templates/customer/orders.html',
    cache: false,
    controller: 'customerOrderCtrl'
  }).state('customerOrder', {
    url: '/customer/new/order',
    cache: false,
    templateUrl: 'templates/customer/order.html',
    controller: 'customerOrderCtrl'
  }).state('customerOrderStatus', {
    url: '/customer/status/order/:id',
    cache: false,
    templateUrl: 'templates/customer/order.html',
    controller: 'customerOrderCtrl'
  });

  $stateProvider.state('shopOrders', {
    url: '/shop/orders',
    cache: false,
    templateUrl: 'templates/shop/orders.html',
    controller: 'shopOrderCtrl'
  }).state('shopOrder', {
    url: '/shop/order/:id',
    cache: false,
    templateUrl: 'templates/shop/order.html',
    controller: 'shopOrderCtrl'
  }).state('shopSummary', {
    url: '/shop/summary',
    cache: false,
    templateUrl: 'templates/shop/summary.html',
    controller: 'shopOrderCtrl'
  });

});

//app.constant('API_URL', '');
app.constant('API_URL', 'https://makolet.biz');

app.run(function($ionicPlatform, $rootScope, $translate, $localStorage, $cordovaToast,
  $ionicPlatform, $log, $state, $timeout, $cordovaGeolocation, $http, $ionicHistory, amMoment) {

  amMoment.changeLocale('he');

  $ionicHistory.clearCache();

  $rootScope.devicePlatform = ionic.Platform.platform().toLowerCase();

  if (!$rootScope.devicePlatform) {
    $rootScope.devicePlatform = 'ios';
  }

  $ionicPlatform.ready(function() {
    if ($rootScope.devicePlatform == 'ios') {
      $localStorage.deviceType = $rootScope.deviceType = 'apns';
    } else {
      $localStorage.deviceType = $rootScope.deviceType = 'gcm';
    }

    $localStorage = $rootScope.deviceToken = '';

    if (window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      cordova.plugins.Keyboard.disableScroll(true);
    }
    if (window.StatusBar) {
      StatusBar.styleDefault();
    }
    document.addEventListener("deviceready", function() {
      var push = PushNotification.init({
        android: {
          senderID: "775258095464"
        },
        ios: {
          alert: "true",
          badge: "true",
          sound: "true"
        },
        windows: {}
      });

      push.on('registration', function(data) {
        $localStorage.deviceToken = $rootScope.deviceToken = data.registrationId;
      });


      push.on('notification', function(data) {
        $timeout(function() {
          if ($rootScope.hasOwnProperty('client')) {
            if ($rootScope.client == 'customer') {
              $state.go('customerOrderStatus', {
                id: parseInt(data.message.replace(/[^0-9\.]/g, ''), 10)
              }, {
                reload: true
              });
            } else {
              $state.go('shopOrder', {
                id: parseInt(data.message.replace(/[^0-9\.]/g, ''), 10)
              }, {
                reload: true
              });
            }
          } else {
            $localStorage.$reset();
            $state.go("login");
          }
        }, 700);
        // data.message,
        // data.title,
        // data.count,
        // data.sound,
        // data.image,
        // data.additionalData
      });
    });
  });

  $rootScope.lang = 'il';
  $translate.use($rootScope.lang);
  $rootScope.client = 'empty';

  if ($localStorage.hasOwnProperty('token')) {
    $http.defaults.headers.common['Authorization'] = 'Bearer ' +
      $localStorage.token;
  }

})

app.filter('statusOrderUser', function($filter) {
  return function(status) {
    switch (parseInt(status)) {
      case 1:
        return $filter('translate')('PENDING_A_PROPOSAL');
        break;
      case 2:
        return $filter('translate')('PENDING_TO_APPROVE');
        break;
      case 3:
        return $filter('translate')('ORDERS_IN_PROCESS');
        break;
      case 4:
        return $filter('translate')('PROPOSALS_ACCEPTED');
        break;
      case 5:
        return $filter('translate')('NOT_PAID');
        break;
      case 6:
        return $filter('translate')('PAID');
        break;
      case 7:
        return $filter('translate')('CANCELLED');
        break;
      case 8:
        return $filter('translate')('DISPUTE');
        break;
    }
  }
})

app.filter('example', function($filter) {
  return function(index) {
    var example = ['חלב 2 שקיות', 'קוטג תנובה', 'ביצים', 'תמרים קופסה', 'עגבניות 2 קילו', '5 תפוחי עץ ירוקים', '4 פיתות'];
    if (parseInt(index) < example.length) {
      return example[parseInt(index)];
    } else {
      return '...';
    }
  }
})

app.filter('status', function($filter) {
  return function(status) {
    switch (parseInt(status)) {
      case 1:
        return $filter('translate')('PENDING_A_PROPOSAL');
        break;
      case 2:
        return $filter('translate')('PENDING_TO_APPROVE');
        break;
      case 3:
        return $filter('translate')('ORDERS_IN_PROCESS');
        break;
      case 4:
        return $filter('translate')('PROPOSALS_ACCEPTED');
        break;
      case 5:
        return $filter('translate')('NOT_PAID');
        break;
      case 6:
        return $filter('translate')('PAID');
        break;
      case 7:
        return $filter('translate')('CANCELLED');
        break;
      case 8:
        return $filter('translate')('DISPUTE');
        break;
    }
  };
})

app.filter('cut', function() {
  return function(value, wordwise, max, tail) {
    if (!value) return '';

    max = parseInt(max, 10);
    if (!max) return value;
    if (value.length <= max) return value;

    value = value.substr(0, max);
    if (wordwise) {
      var lastspace = value.lastIndexOf(' ');
      if (lastspace != -1) {
        value = value.substr(0, lastspace);
      }
    }
    return value + (tail || ' …');
  };
})

app.filter('int', function() {
  return function(value, wordwise, max, tail) {
    return parseInt(valie);
  };
})

app.filter('bool', function() {
  return function(value, wordwise, max, tail) {
    return !(/^(false|0)$/i).test(value) && !!value;
  };
})

app.directive('keyboard', function() {
  return function(scope, element, attrs) {
    element.bind("focus", function(event) {
      if (element.attr('type') != 'text') {
        element.removeClass('rtl').addClass('ltr');
      }
    });
    element.bind("keydown keypress", function(event) {
      if (element.attr('type') == 'text' || !element.hasOwnProperty('type')) {
        var text = element.val();
        if (text.split(' ').length == 1) {
          HebrewChars = new RegExp("^[\u0590-\u05FF]+$");
          if (element.length == 0) {
            element.removeClass('ltr').addClass('rtl');
          } else {
            if (HebrewChars.test(element.val())) {
              element.removeClass('ltr').addClass('rtl');
            } else {
              element.removeClass('rtl').addClass('ltr');
            }
          }
        }
      }
    });
  };
});

app.directive('stopEvent', function() {
  return {
    restrict: 'A',
    link: function(scope, element, attr) {
      element.bind('click', function(e) {
        e.stopPropagation();
      });
    }
  };
});


app.filter('tranzila', function($sce, $filter) {
  return function(customerId) {
    var url = 'https://direct.tranzila.com/amsn2001/iframe.php';
    var params = '?currency=1';
    params += '&buttonLabel=' + $filter('translate')('NOTE_OF_CREDIT');
    params += '&lang=il';
    params += '&tranmode=VK';
    params += '&nologo=1';
    params += '&trButtonColor=00AEEF';
    params += '&trTextColor=444444';
    params += '&data=xxx';
    params += '&sum=1';
    params += '&hidesum=1';
    params += '&customer=' + customerId;
    return $sce.trustAsResourceUrl(url + params);
  };
});
