/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('getUserByIDService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/user/id/';

    return {
        getUser: function (userID) {
            var endpointURI = endpoint + userID;

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