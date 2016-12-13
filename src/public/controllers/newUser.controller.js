/**
 * Created by fran lopez on 01/12/2016.
 */

angular.module('app').controller('NewUserController',
    function ($scope, getUserByTokenService, newUserService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const userTokenIsEmpty = '\'Token\' field cannot be empty';
        const userTokenExists = 'There is a user with the inserted token';
        const anyRequiredFieldIsEmpty = 'There are empty required fields in form';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;

        vm.getUserToken = function () {
            if ((vm.token !== undefined) && (vm.token !== null) && (vm.token.length > 0)) {
                vm.error = null;
                vm.requestToGetUserToken(getUserByTokenService, vm.token);
            }
            else {
                vm.token = null;
                vm.error = noticeWord + userTokenIsEmpty;
            }
        };

        vm.requestToGetUserToken = function (getUserByTokenService, userToken) {
            var serverResponseBody;
            var user;

            getUserByTokenService.getUser(userToken).then(function (serverResponse) {
                serverResponseBody = angular.fromJson(serverResponse.data);

                if (serverResponseBody.code === okResponseCode) {
                    user = angular.fromJson(serverResponseBody.message);

                    if (parseInt(user.id) > 0) {
                        vm.token = null;
                        vm.error = errorWord + userTokenExists;
                    }
                }
                else if (serverResponseBody.code !== okResponseCode) {
                    vm.token = null;
                    vm.error = noticeWord + serverResponseBody.message;
                }
            }, function () {
                vm.token = null;
                vm.error = errorWord + badRequest;
            });
        };

        vm.create = function () {
            if ((vm.name !== undefined) && (vm.name !== null) && (vm.name.length > 0) && (vm.mail !== undefined)
                && (vm.mail !== null) && (vm.mail.length > 0) && (vm.password !== undefined) && (vm.password !== null)
                && (vm.password.length > 0) && (vm.token !== undefined) && (vm.token !== null)
                && (vm.token.length > 0)) {
                vm.error = null;

                if (vm.isEnabled === undefined)
                    vm.isEnabled = false;

                vm.requestToCreateUser(newUserService);
            }
            else {
                vm.token = null;
                vm.error = noticeWord + anyRequiredFieldIsEmpty;
            }
        };

        vm.requestToCreateUser = function (newUserService) {
            var serverResponseBody;

            newUserService.create(vm.name, vm.mail, vm.password, vm.token, vm.isEnabled).then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.output = serverResponseBody.message;
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };
    });