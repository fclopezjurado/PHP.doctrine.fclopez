/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('getResultsByUserIDService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/results/user_id/';

    return {
        getResults: function (userID) {
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