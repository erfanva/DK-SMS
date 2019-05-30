var app = angular.module("myApp", []);
app.controller('myCtrl', function ($scope, $http) {
    $scope.Data = {
        report:{
            messages_count : 0,
            apis: [],
            most_common: []
        },
        messages:[],
        search:{
            phone:""
        }
    };
    $scope.Func = {
        find : function(){
            $http.get("sms/get?number="+$scope.Data.search.phone).then(function (response) {
                console.log(response.data.messages);
                $scope.Data.messages = response.data.messages;
            });
        }
    };
    $http.get("sms/report").then(function (response) {
        $scope.Data.report = response.data;
    });
    $http.get("sms/get").then(function (response) {
        console.log(response.data.messages);
        $scope.Data.messages = response.data.messages;
    });
    
});