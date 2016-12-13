/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('getUserByTokenService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/user/token/';

    return {
        getUser: function (userToken) {
            var endpointURI = endpoint + userToken;

            return $http.get(endpointURI)
                .then(function (result) {
                        return result;
                    },
                    function (result) {
                        return result;
                    });
        }
    }
});