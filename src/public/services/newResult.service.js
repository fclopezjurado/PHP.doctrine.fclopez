/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('newResultService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/result/create';

    return {
        create: function (userID, result) {
            var requestParameters = {user_id: userID, result: result};

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