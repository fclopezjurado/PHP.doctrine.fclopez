/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('deleteResultByIDService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/result/delete/id/';

    return {
        delete: function (resultID) {
            var endpointURI = endpoint + resultID;

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