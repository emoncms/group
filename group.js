
var group = {
    'create': function (name, description, organization, area, visibility, access) {
        var result = {};
        $.ajax({url: path + "group/create", data: "name=" + name + "&description=" + description + "&organization=" + organization + '&area=' + area + '&visibility=' + visibility + '&access=' + access, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'editgroup': function (groupid, name, description, organization, area, visibility, access) {
        var result = {};
        $.ajax({url: path + "group/editgroup", data: "groupid=" + groupid + "&name=" + name + "&description=" + description + "&organization=" + organization + '&area=' + area + '&visibility=' + visibility + '&access=' + access, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'addmemberauth': function (groupid, username, password, role) {
        var result = {};
        $.ajax({url: path + "group/addmemberauth", data: "groupid=" + groupid + "&username=" + username + "&password=" + password + "&role=" + role, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'createuseraddtogroup': function (groupid, email, username, password, role) {
        var result = {};
        $.ajax({url: path + "group/createuseraddtogroup", data: "groupid=" + groupid + "&email=" + email + "&username=" + username + "&password=" + password + "&role=" + role, dataType: 'json', async: false, success: function (data) {
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
    'fullremoveuser': function (groupid, userid) {
        var result = {};
        $.ajax({url: path + "group/fullremoveuser", data: "groupid=" + groupid + "&userid=" + userid, dataType: 'json', async: false, success: function (data) {
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
    },
    'getsessionuserrole': function (groupid) {
        var result = {};
        $.ajax({url: path + "group/getsessionuserrole", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'getrole': function (userid, groupid) {
        var result = {};
        $.ajax({url: path + "group/getrole", data: "userid=" + userid + "&groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    }
}

