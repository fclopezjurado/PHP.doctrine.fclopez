/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('DeleteResultsController',
    function ($scope, getResultsService, deleteResultsService, deleteResultByIDService, deleteResultsByUserIDService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const deleteTypeIsEmpty = '\'Delete type\' field cannot be empty';
        const userIDIsEmpty = '\'User ID\' field cannot be empty';
        const resultIDIsEmpty = '\'Result ID\' field cannot be empty';

        const allResultsType = 1;
        const byIDType = 2;
        const byUserIDType = 3;
        const allResultsDeleteType = 'ALL results';
        const byIDDeleteType = 'By ID';
        const byUserIDDeleteType = 'By USER ID';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;

        vm.deleteTypes = [];
        vm.results = [];
        vm.usersIDs = [];

        vm.init = function () {
            vm.deleteTypes.push({value: allResultsType, text: allResultsDeleteType});
            vm.deleteTypes.push({value: byIDType, text: byIDDeleteType});
            vm.deleteTypes.push({value: byUserIDType, text: byUserIDDeleteType});

            vm.deleteByIDIsVisible = false;
            vm.deleteByUserIDIsVisible = false;
            vm.deleteType = '1';

            var serverResponseBody;

            getResultsService.getResults().then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.results = angular.fromJson(serverResponseBody.message);

                        $.each(vm.results, function(resultIndex, result) {
                            if ($.inArray(result.user.id, vm.usersIDs) === -1)
                                vm.usersIDs.push(result.user.id);
                        });
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };

        vm.changeDeleteType = function () {
            switch (parseInt(vm.deleteType)) {
                case allResultsType:
                    vm.deleteByIDIsVisible = false;
                    vm.deleteByUserIDIsVisible = false;
                    break;
                case byIDType:
                    vm.deleteByIDIsVisible = true;
                    vm.deleteByUserIDIsVisible = false;
                    break;
                case byUserIDType:
                    vm.deleteByIDIsVisible = false;
                    vm.deleteByUserIDIsVisible = true;
                    break;
            }
        };

        vm.delete = function () {
            if (vm.deleteType !== undefined) {
                vm.error = null;

                switch (parseInt(vm.deleteType)) {
                    case allResultsType:
                        vm.output = JSON.stringify(vm.results);
                        vm.requestToDeleteResults(deleteResultsService);
                        break;
                    case byIDType:
                        if (vm.deleteByID === undefined)
                            vm.error = noticeWord + resultIDIsEmpty;
                        else
                            vm.requestToDeleteResults(deleteResultByIDService, vm.deleteByID);
                        break;
                    case byUserIDType:
                        if (vm.deleteByUserID === undefined)
                            vm.error = noticeWord + userIDIsEmpty;
                        else
                            vm.requestToDeleteResults(deleteResultsByUserIDService, vm.deleteByUserID);
                        break;
                }

            }
            else
                vm.error = noticeWord + deleteTypeIsEmpty;
        };

        vm.requestToDeleteResults = function (serviceToDeleteResults, filter) {
            var serverResponseBody;

            serviceToDeleteResults.delete(filter).then(
                function (serverResponse) {
                    serverResponseBody = angular.fromJson(serverResponse.data);

                    if ((serverResponseBody.code === okResponseCode) && (serverResponseBody.error === false)) {
                        vm.error = null;
                        vm.output = serverResponseBody.message;

                        switch (serviceToDeleteResults) {
                            case deleteResultsService:
                                if (filter === undefined) {
                                    vm.results = [];
                                    vm.usersIDs = [];
                                }
                                break;
                            case deleteResultByIDService:
                                $.each(vm.results, function(userIndex) {
                                    if (vm.results[userIndex].id === parseInt(filter)) {
                                        vm.results.splice(userIndex, 1);
                                        return false;
                                    }
                                });
                                break;
                            case deleteResultsByUserIDService:
                                $.each(vm.results, function(userIndex) {
                                    if (vm.results[userIndex].user.id === parseInt(filter)) {
                                        vm.results.splice(userIndex, 1);
                                        return false;
                                    }
                                });

                                if ($.inArray(parseInt(filter), vm.usersIDs) !== -1)
                                    vm.usersIDs.splice($.inArray(parseInt(filter), vm.usersIDs), 1);
                                break;
                        }
                    }
                    else if (serverResponseBody.code !== okResponseCode)
                        vm.error = noticeWord + serverResponseBody.message;
                }, function () {
                    vm.error = errorWord + badRequest;
                });
        };
    });