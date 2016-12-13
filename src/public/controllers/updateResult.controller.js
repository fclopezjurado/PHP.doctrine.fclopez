/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('UpdateResultController',
    function ($scope, getResultsService, updateResultService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const anyRequiredFieldIsEmpty = 'There are empty required fields in form';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;
        vm.results = [];

        vm.init = function () {
            var serverResponseBody;

            getResultsService.getResults().then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.results = angular.fromJson(serverResponseBody.message);
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };

        vm.changeResult = function () {
            var resultID = parseInt(vm.id);

            $.each(vm.results, function (resultIndex, result) {
               if (result.id === resultID)
                   vm.result = result.result;
            });
        };

        vm.update = function () {
            if ((vm.id !== undefined) && (vm.result !== undefined) && ($.isNumeric(vm.result))) {
                vm.error = null;
                vm.requestToUpdateResult(vm.id, vm.result);
            }
            else {
                vm.token = null;
                vm.error = noticeWord + anyRequiredFieldIsEmpty;
            }
        };

        vm.requestToUpdateResult = function (resultID, result) {
            var serverResponseBody;
            var updatedResult;

            updateResultService.update(resultID, result).then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.output = serverResponseBody.message;
                        updatedResult = angular.fromJson(vm.output);

                        $.each(vm.results, function (userIndex, result) {
                            if (result.id === updatedResult.id)
                                vm.results[userIndex].result = updatedResult.result;
                        });
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };
    });