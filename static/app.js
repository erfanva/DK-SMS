var app = angular.module("myApp", []);
app.controller('myCtrl', function ($scope, $http) {
    $scope.Data = {
        report:{
            messages_count : 0,
            logs_count : 0,
            apis: {
                results: [],
                fields: [],
                count:0
            },
            most_common: {
                results: [],
                fields: [],
                count:0
            }
        },
        messages:{
            results: [],
            fields: [],
            count:0
        },
        search:{
            phone:""
        }
    };
    $scope.Func = {
        find : function(){
            $http.get("sms/get?number="+$scope.Data.search.phone).then(function (response) {
                // console.log(response.data.messages);
                $scope.Data.messages = response.data;
                $scope.Data.messages.fields = {
                    phone: {title:"Phone"},
                    body: {title:"Text"},
                    sent: {title:"Sent"}
                };
            });
            console.log($scope.Data.messages);
        }
    };
    $http.get("sms/report").then(function (response) {
        $scope.Data.report = response.data;
        $scope.Data.report.apis.results.forEach(element => {
            element["usage"] = element["usage_count"] / $scope.Data.report.logs_count;
        });
        $scope.Data.report.apis.fields = {
            api: {title:"Api"},
            usage: {title:"Usage"},
            avg_error: {title:"Error"}
        };
        $scope.Data.report.most_common.fields = {
            phone: {title:"Phone number"},
            count: {title:"Count"}
        };
        console.log($scope.Data.report);
    });
    $http.get("sms/get").then(function (response) {
        // console.log(response.data.messages);
        $scope.Data.messages = response.data;
        $scope.Data.messages.fields = {
            phone: {title:"Phone"},
            body: {title:"Text"},
            sent: {title:"Sent"}
        };
        console.log($scope.Data.messages);
    });
    
});