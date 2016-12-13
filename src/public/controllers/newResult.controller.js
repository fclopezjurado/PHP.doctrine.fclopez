/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('NewResultController',
    function ($scope, getUsersService, newResultService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const userIDIsEmpty = '\'User ID\' field cannot be empty';
        const resultMustBeNumberic = '\'Result ID\' field must be numeric';

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

        vm.create = function () {
            if ((vm.result !== undefined) && (vm.userID !== undefined) && ($.isNumeric(vm.result))
                && ($.isNumeric(vm.userID))) {
                vm.error = null;
                vm.requestToCreateResult(vm.userID, vm.result);
            }
            else if (vm.userID === undefined) {
                vm.error = noticeWord + userIDIsEmpty;
            }
            else {
                vm.result = null;
                vm.error = noticeWord + resultMustBeNumberic;
            }
        };

        vm.requestToCreateResult = function (userID, result) {
            var serverResponseBody;

            newResultService.create(userID, result).then(
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