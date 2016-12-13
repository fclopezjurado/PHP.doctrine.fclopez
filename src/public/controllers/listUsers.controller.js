/**
 * Created by fran lopez on 13/12/2016.
 */

angular.module('app').controller('ListUsersController',
    function ($scope, getUsersService, getUserByIDService, getUserByTokenService) {
        const errorWord = 'ERROR. ';
        const noticeWord = 'NOTICE. ';
        const listTypeIsEmpty = '\'List type\' field cannot be empty';
        const userIDIsEmpty = '\'User ID\' field cannot be empty';
        const userTokenIsEmpty = '\'User token\' field cannot be empty';

        const allUsersType = 1;
        const byIDType = 2;
        const byTokenType = 3;
        const allUsersListType = 'ALL users';
        const byIDListType = 'By ID';
        const byTokenListType = 'By TOKEN';

        const badRequest = 'Bad request';
        const okResponseCode = 200;

        var vm = this;

        vm.listTypes = [];
        vm.users = [];

        vm.init = function () {
            vm.listTypes.push({value: allUsersType, text: allUsersListType});
            vm.listTypes.push({value: byIDType, text: byIDListType});
            vm.listTypes.push({value: byTokenType, text: byTokenListType});

            vm.listByIDIsVisible = false;
            vm.listByTokenIsVisible = false;
            vm.listType = '1';

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

        vm.changeListType = function () {
            switch (parseInt(vm.listType)) {
                case allUsersType:
                    vm.listByIDIsVisible = false;
                    vm.listByTokenIsVisible = false;
                    break;
                case byIDType:
                    vm.listByIDIsVisible = true;
                    vm.listByTokenIsVisible = false;
                    break;
                case byTokenType:
                    vm.listByIDIsVisible = false;
                    vm.listByTokenIsVisible = true;
                    break;
            }
        };

        vm.list = function () {
            if (vm.listType !== undefined) {
                vm.error = null;

                switch (parseInt(vm.listType)) {
                    case allUsersType:
                        vm.output = JSON.stringify(vm.users);
                        break;
                    case byIDType:
                        if (vm.listByID === undefined)
                            vm.error = noticeWord + userIDIsEmpty;
                        else
                            vm.requestToListUser(getUserByIDService, vm.listByID);
                        break;
                    case byTokenType:
                        if (vm.listByToken === undefined)
                            vm.error = noticeWord + userTokenIsEmpty;
                        else
                            vm.requestToListUser(getUserByTokenService, vm.listByToken);
                        break;
                }

            }
            else
                vm.error = noticeWord + listTypeIsEmpty;
        };

        vm.requestToListUser = function (getUserService, userFilter) {
            var serverResponseBody;

            getUserService.getUser(userFilter).then(
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