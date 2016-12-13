/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').factory('deleteResultsService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/results/delete';

    return {
        delete: function () {
            return $http.post(endpoint)
                .then(function (result) {
                        return result;
                    },
                    function (result) {
                        return result;
                    });
        }
    }
});