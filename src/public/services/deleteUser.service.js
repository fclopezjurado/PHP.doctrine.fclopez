/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('deleteUserService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/user/delete/';

    return {
        delete: function (userID) {
            var endpointURI = endpoint + userID;

            return $http.post(endpointURI)
                .then(function (result) {
                        return result;
                    },
                    function (result) {
                        return result;
                    });
        }
    }
});