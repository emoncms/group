
var group = {
    'create': function (name, description, organization, area, visibility, access) {
        var result = {};
        $.ajax({url: path + "group/create", data: "name=" + name + "&description=" + description + "&organization=" + organization + '&area=' + area + '&visibility=' + visibility + '&access=' + access, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'addmemberauth': function (groupid, username, password, access) {
        var result = {};
        $.ajax({url: path + "group/addmemberauth", data: "groupid=" + groupid + "&username=" + username + "&password=" + password + "&access=" + access, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'removeuser': function (groupid, userid) {
        var result = {};
        $.ajax({url: path + "group/removeuser", data: "groupid=" + groupid + "&userid=" + userid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'grouplist': function () {
        var result = {};
        $.ajax({url: path + "group/grouplist", dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'deletegroup': function (groupid) {
        var result = {};
        $.ajax({url: path + "group/delete", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'userlist': function (groupid) {
        var result = {};
        $.ajax({url: path + "group/userlist", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    }
}

