/**
 * Created by fran lopez on 10/12/2016.
 *
 * CommonJS file to define the angular module for our application.
 * This module has 'ngRoute' and 'angularCSS' modules as dependencies.
 */

var mainApplicationModuleName = 'app';

angular.module(mainApplicationModuleName, ['ngRoute', 'angularCSS']);

angular.element(document).ready(function () {
    angular.bootstrap(document, [mainApplicationModuleName]);
});

