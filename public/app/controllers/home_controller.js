app.controller('HomeController', function ($scope, $http, $route, $location, ChartService, toastr) {
    
    $scope.userRole = window.userRole;

    $scope.chart_monthly_sales = new CanvasJS.Chart("chartMonthlySales", {
        animationEnabled: true,
        theme: "light2",
        data: [{
            color: "#bdc1ec",
            type: "column",
            showInLegend: false,
            indexLabel: "{y}",
            indexLabelPlacement: 'outside',
            indexLabelFontSize: 12,
            indexLabelFontColor: '#666',
            legendMarkerColor: "grey",
            dataPoints: []
        }]
    });

    $scope.chart_salesperson = new CanvasJS.Chart("chartSalesperson", {
        animationEnabled: true,
        theme: "light2",
        data: [{
            type: 'doughnut',
            radius:  "75%",
            toolTipContent: "{label} <br> {y} - <b>#percent%</b>",
            indexLabel: "{label}",
            indexLabelFontSize: 11,
            indexLabelFontColor: "#666",
            indexLabelLineColor: "#ccc",
            dataPoints: []
        }]
    });

    $scope.$on('$viewContentLoaded', function (view) {
        var role = window.userRole;

        if (role == 'ADM' || role == 'SYS') {
            ChartService.sales()
                .success(function (response) {
                    var data = [];

                    response.forEach(function (item) {
                        data.push({
                            label: item.period,
                            y: parseFloat(item.amount)
                        });
                    });

                    $scope.chart_monthly_sales.options.data[0].dataPoints = data;
                    $scope.chart_monthly_sales.render();
                }).error(function (response) {
                    toastr.error(response.msg || 'Error en el servidor');
                });

            ChartService.salesperson()
                .success(function (response) {
                    var data = [];
                    var others = 0;

                    response.forEach(function (item, key) {
                        if (key < 10) {
                            data.push({
                                label: item.name, 
                                y: parseFloat(item.amount)
                            });
                        } else {
                            others += parseFloat(item.amount);
                        }
                    });

                    // add the "OTROS" label
                    if (others > 0) {
                        data.push({
                            label: 'OTROS',
                            y: others
                        });
                    }

                    $scope.chart_salesperson.options.data[0].dataPoints = data;
                    $scope.chart_salesperson.render();
                }).error(function (response) {
                    toastr.error(response.msg || 'Error en el servidor');
                });
        }
    });
});