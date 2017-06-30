AppDumbu.filter('cutFullName', function() {
    return function(name) {
        if ("" == name) {
            return '...';
        }
        if (new String(name).length > 20)
            return new String(name).substring(0, 19) + '...';
        else
            return name;
    };
});
