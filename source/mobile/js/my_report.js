angular.module('app')
    .controller('myReportCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.reports = [];
        ajax.req('GET', 'my_city/get_report_by_page', {page: 1, page_size: 10})
            .then(function(response){
                if (response.success){
                    $scope.reports = response.data;
                }
            });

        //查看pdf
        $scope.watch_pdf = function(report){
            window.location.href = SITE_URL + report.path;
        }
    }]);