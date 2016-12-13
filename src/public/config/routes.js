/**
 * Created by fran lopez on 10/12/2016.
 *
 * CommonJS file to the application routing.
 * For each endpoint, angularJS loads a different view with the associated CSS stylesheet.
 */

angular.module('app').config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider
            .when('/new_user', {
                templateUrl: 'views/new_user.html',
                css: 'css/form.css'
            })
            .when('/delete_user', {
                templateUrl: 'views/delete_user.html',
                css: 'css/form.css'
            })
            .when('/list_users', {
                templateUrl: 'views/list_users.html',
                css: 'css/form.css'
            })
            .when('/update_user', {
                templateUrl: 'views/update_user.html',
                css: 'css/form.css'
            })
            .when('/new_result', {
                templateUrl: 'views/new_result.html',
                css: 'css/form.css'
            })
            .when('/delete_results', {
                templateUrl: 'views/delete_results.html',
                css: 'css/form.css'
            })
            .when('/list_results', {
                templateUrl: 'views/list_results.html',
                css: 'css/form.css'
            })
            .when('/update_result', {
                templateUrl: 'views/update_result.html',
                css: 'css/form.css'
            })
            .otherwise({
                redirectTo: '/new_user'
            });
    }]);