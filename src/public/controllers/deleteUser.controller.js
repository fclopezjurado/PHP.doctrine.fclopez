/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('DeleteUserController',
    function ($scope, getUsersService, deleteUserService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const userIsEmpty = '\'User\' field cannot be empty';

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

        vm.delete = function () {
            if (vm.user !== undefined) {
                vm.error = null;
                vm.requestToDeleteUser(deleteUserService, vm.user);
            }
            else {
                vm.token = null;
                vm.error = noticeWord + userIsEmpty;
            }
        };

        vm.requestToDeleteUser = function (deleteUserService, userID) {
            var serverResponseBody;

            deleteUserService.delete(userID).then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.output = serverResponseBody.message;

                        $.each(vm.users, function(userIndex) {
                            if (vm.users[userIndex].id === parseInt(userID)) {
                                vm.users.splice(userIndex, 1);
                                return false;
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