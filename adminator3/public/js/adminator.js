function cleanup(arr, prop) {
    var new_arr = [];
    var lookup = {};

    for (var i in arr) {
        lookup[arr[i][prop]] = arr[i];
    }
    for (i in lookup) {
        new_arr.push(lookup[i]);
    }
    return new_arr;
}

var filter=cleanup(data, 'id') 

$(function () {
    $('#table').bootstrapTable({
        data: filter
    });
});
