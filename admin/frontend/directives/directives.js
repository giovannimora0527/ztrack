var app = angular.module('zmodo-directives', []);

//http://jsfiddle.net/thomporter/DwKZh/

app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attrs, modelCtrl) {
            modelCtrl.$parsers.push(function (inputValue) {
                // this next if is necessary for when using ng-required on your input. 
                // In such cases, when a letter is typed first, this parser will be called
                // again, and the 2nd time, the value will be undefined
                if (inputValue === "undefined")
                    return '';
                var transformedInput = inputValue.replace(/[^0-9]/g, '');
                if (transformedInput !== inputValue) {
                    modelCtrl.$setViewValue(transformedInput);
                    modelCtrl.$render();
                }

                return transformedInput;
            });
        }
    };
});

//http://stackoverflow.com/questions/18144142/jquery-ui-datepicker-with-angularjs

app.directive('jqdatepicker', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            element.datepicker({
                showAnim: "slideDown",
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                yearRange: "-70:+0"
            });
        }
    };
});

/**
 * Directiva para comparar campos
 *
 */
app.directive("passwordMatcher", function () {
    return {
        require: "ngModel",
        scope: {
            otherModelValue: "=passwordMatcher"
        },
        link: function (scope, element, attributes, ngModel) {

            ngModel.$validators.passwordMatcher = function (modelValue) {
                return modelValue === scope.otherModelValue;
            };

            scope.$watch("otherModelValue", function () {
                ngModel.$validate();
            });
        }
    };
});
