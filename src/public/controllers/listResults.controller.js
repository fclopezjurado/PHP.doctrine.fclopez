/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('ListResultsController',
    function ($scope, getResultsService, getResultByIDService, getResultsByUserIDService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const listTypeIsEmpty = '\'List type\' field cannot be empty';
        const userIDIsEmpty = '\'User ID\' field cannot be empty';
        const resultIDIsEmpty = '\'Result ID\' field cannot be empty';

        const allResultsType = 1;
        const byIDType = 2;
        const byUserIDType = 3;
        const allResultsListType = 'ALL results';
        const byIDListType = 'By ID';
        const byUserIDListType = 'By USER ID';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;

        vm.listTypes = [];
        vm.results = [];
        vm.usersIDs = [];

        vm.init = function () {
            vm.listTypes.push({value: allResultsType, text: allResultsListType});
            vm.listTypes.push({value: byIDType, text: byIDListType});
            vm.listTypes.push({value: byUserIDType, text: byUserIDListType});

            vm.listByIDIsVisible = false;
            vm.listByUserIDIsVisible = false;
            vm.listType = '1';

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

        vm.changeListType = function () {
            switch (parseInt(vm.listType)) {
                case allResultsType:
                    vm.listByIDIsVisible = false;
                    vm.listByUserIDIsVisible = false;
                    break;
                case byIDType:
                    vm.listByIDIsVisible = true;
                    vm.listByUserIDIsVisible = false;
                    break;
                case byUserIDType:
                    vm.listByIDIsVisible = false;
                    vm.listByUserIDIsVisible = true;
                    break;
            }
        };

        vm.list = function () {
            if (vm.listType !== undefined) {
                vm.error = null;

                switch (parseInt(vm.listType)) {
                    case allResultsType:
                        vm.output = JSON.stringify(vm.results);
                        break;
                    case byIDType:
                        if (vm.listByID === undefined)
                            vm.error = noticeWord + resultIDIsEmpty;
                        else
                            vm.requestToListResults(getResultByIDService, vm.listByID);
                        break;
                    case byUserIDType:
                        if (vm.listByUserID === undefined)
                            vm.error = noticeWord + userIDIsEmpty;
                        else
                            vm.requestToListResults(getResultsByUserIDService, vm.listByUserID);
                        break;
                }

            }
            else
                vm.error = noticeWord + listTypeIsEmpty;
        };

        vm.requestToListResults = function (getResultService, filter) {
            var serverResponseBody;

            getResultService.getResults(filter).then(
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