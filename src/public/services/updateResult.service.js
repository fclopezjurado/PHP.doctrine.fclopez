/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('updateResultService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/result/update';

    return {
        update: function (resultID, result) {
            var requestParameters = {id: resultID, result: result};

            return $http.post(endpoint, requestParameters)
                .then(function (result) {
                        return result;
                    },
                    function (result) {
                        return result;
                    });
        }
    }
});