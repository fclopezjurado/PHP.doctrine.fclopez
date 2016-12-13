/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('getResultByIDService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/result/id/';

    return {
        getResults: function (resultID) {
            var endpointURI = endpoint + resultID;

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