/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').factory('getUsersService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/users';

    return {
        getUsers: function () {
            return $http.get(endpoint)
                .then(function (result) {
                        return result;
                    },
                    function (result) {
                        return result;
                    });
        }
    }
});