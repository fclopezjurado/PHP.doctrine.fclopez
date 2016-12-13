/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('UpdateUserController',
    function ($scope, getUsersService, updateUserService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const userTokenExists = 'There is a user with the inserted token';
        const anyRequiredFieldIsEmpty = 'There are empty required fields in form';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;
        vm.users = [];

        vm.init = function () {
            var serverResponseBody;

            getUsersService.getUsers().then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.users = angular.fromJson(serverResponseBody.message);
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };

        vm.changeUser = function () {
            var userID = parseInt(vm.user);

            $.each(vm.users, function (userIndex, user) {
               if (user.id === userID) {
                   vm.name = user.username;
                   vm.mail = user.email;
                   vm.password = user.password;
                   vm.token = user.token;
                   vm.isEnabled = user.enabled;
               }
            });
        };

        vm.getUserToken = function () {
            vm.error = null;

            if (vm.user !== undefined)
                $.each(vm.users, function (userIndex, user) {
                    if ((user.token === vm.token) && (user.id !== parseInt(vm.user)))
                        vm.error = noticeWord + userTokenExists;
                });
        };

        vm.update = function () {
            if ((vm.user !== undefined) && (vm.name !== undefined) && (vm.name !== null) && (vm.name.length > 0)
                && (vm.mail !== undefined) && (vm.mail !== null) && (vm.mail.length > 0) && (vm.password !== undefined)
                && (vm.password !== null) && (vm.password.length > 0) && (vm.token !== undefined) && (vm.token !== null)
                && (vm.token.length > 0)) {
                vm.error = null;

                if (vm.isEnabled === undefined)
                    vm.isEnabled = false;

                vm.requestToUpdateUser(updateUserService);
            }
            else {
                vm.token = null;
                vm.error = noticeWord + anyRequiredFieldIsEmpty;
            }
        };

        vm.requestToUpdateUser = function (updateUserService) {
            var serverResponseBody;
            var updatedUser;

            updateUserService.update(vm.user, vm.name, vm.mail, vm.password, vm.token, vm.isEnabled).then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.output = serverResponseBody.message;
                        updatedUser = angular.fromJson(vm.output);

                        $.each(vm.users, function (userIndex, user) {
                            if (user.id === updatedUser.id) {
                                vm.users[userIndex].username = updatedUser.username;
                                vm.users[userIndex].email = updatedUser.email;
                                vm.users[userIndex].password = updatedUser.password;
                                vm.users[userIndex].token = updatedUser.token;
                                vm.users[userIndex].enabled = updatedUser.enabled;
                            }
                        });
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };
    });