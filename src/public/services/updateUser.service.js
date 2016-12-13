/**
 * Created by fran lopez on 12/12/2016.
 */

angular.module('app').factory('updateUserService', function ($http) {
    const endpoint = 'http://localhost/PHP.doctrine.fclopez/src/controllers/FrontController.php/user/update';

    return {
        update: function (userID, userName, userMail, userPassword, userToken, userIsEnabled) {
            var requestParameters = {id: userID, name: userName, mail: userMail, password: userPassword,
                token: userToken, enabled: userIsEnabled};

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