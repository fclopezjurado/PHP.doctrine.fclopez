/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('newUserService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/user/create';

    return {
        create: function (userName, userMail, userPassword, userToken, userIsEnabled) {
            var requestParameters = {name: userName, mail: userMail, password: userPassword, token: userToken,
                enabled: userIsEnabled};

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