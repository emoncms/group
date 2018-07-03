
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
    'setuserinfo': function (userid, groupid, username, name, email, location, bio, timezone, role, password, tags) {
        var result = {};
        $.ajax({url: path + "group/setuserinfo", method: "POST", data: "groupid=" + groupid + "&userid=" + userid + "&username=" + username + "&name=" + name + "&email=" + email + "&bio=" + bio + "&timezone=" + timezone + "&location=" + location + "&role=" + role + "&password=" + password + "&tags=" + tags, dataType: 'json', async: false, success: function (data) {
                result = data;
                //console.log(result.responseText)
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
    'extendedgrouplist': function () {
        var result = {};
        $.ajax({url: path + "group/mygroups", dataType: 'json', async: false, success: function (data) {
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
        var result = [];
        $.ajax({url: path + "group/userlist", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'getuserfeeds': function (groupid, userid) {
        var result = {};
        $.ajax({url: path + "group/getuserfeeds", data: "groupid=" + groupid + "&userid=" + userid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'getsessionuserrole': function (groupid) {
        var result = 0;
        $.ajax({url: path + "group/getsessionuserrole", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    /*'getrole': function (userid, groupid) {
     var result = {};
     $.ajax({url: path + "group/getrole", data: "userid=" + userid + "&groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
     result = data;
     }});
     return result;
     },*/
    'csvexport': function (groupid, feedid, start, end, interval, timeformat, name) {
        var result = {};
        var url = path + "group/csvexport.json?groupid=" + groupid + "&id=" + feedid + "&start=" + start + "&end=" + end + "&interval=" + interval + "&timeformat=" + timeformat + "&name=" + name;
        window.open(url);
        /*  $.ajax({url: path + "group/csvexport", data: "groupid=" + groupid + "&id=" + feedid + "&start=" + start + "&end=" + end + "&interval=" + interval + "&timeformat=" + timeformat + "&name=" + name, dataType: 'json', async: false, success: function (data) {
         result = data;
         }});
         return result;*/
    },
    'setMultiFeedProcessList': function (feedids, processlist, groupid, name, description, tag, frequency, run_on, belongs_to) {
        var result = {};
        $.ajax({url: path + "group/setmultifeedprocesslist.json?feedids=" + JSON.stringify(feedids) + "&groupid=" + groupid, method: "POST", data: "processlist=" + processlist + "&name=" + name + "&description=" + description + "&tag=" + tag + "&frequency=" + frequency + "&run_on=" + run_on + "&belongs_to=" + belongs_to, async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'setProcessList': function (taskid, userid, groupid, processlist) {
        var result = {};
        $.ajax({url: path + "group/setprocesslist.json?id=" + taskid + "&userid=" + userid + "&groupid=" + groupid, method: "POST", data: "processlist=" + processlist, async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'deleteTask': function (taskid, userid, groupid) {
        var result = {};
        $.ajax({url: path + "group/deletetask.json?taskid=" + taskid + "&userid=" + userid + "&groupid=" + groupid, async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'setTaskEnabled': function (value, taskid, userid, groupid) {
        var result = {};
        $.ajax({url: path + "group/settaskenabled.json?taskid=" + taskid + "&userid=" + userid + "&groupid=" + groupid + "&enabled=" + value, async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'sendlogindetails': function (groupid, email, userid, password, role) {
        var result = {};
        $.ajax({url: path + "group/sendlogindetails", data: "groupid=" + groupid + "&email=" + email + "&userid=" + userid + "&password=" + password + "&role=" + role, dataType: 'json', async: false, success: function (data) {
                result = data;
                //console.log(data.responseText);
            }});
        return result;
    },
    
// Add mock data to users - Used during development
    'getapikeys': function (groupid) {
        var result = {};
        $.ajax({url: path + "group/getapikeys", data: "groupid=" + groupid, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'createinputs': function (apikey) {
        var result = {};
        $.ajax({url: path + "input/post.json?node=1&json={power4:100,power2:100,power3:100}&apikey", data: "apikey=" + apikey, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'getfeeds': function (apikey) {
        var result = {};
        $.ajax({url: path + "feed/list.json", data: "apikey=" + apikey, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'updatefeed': function (feedid, apikey, time, value) {
        var result = {};
        $.ajax({url: path + "feed/insert.json?id=" + feedid + "&time=" + time + "&value=" + value, data: "apikey=" + apikey, dataType: 'json', async: true, success: function (data) {
                result = data;
            }});
        return result;
    },
    'createfeeds': function (apikey, name) {
        var result = {};
        $.ajax({url: path + "feed/create.json?tag=Test&name=Power" + name + "&datatype=1&engine=5&options={\"interval\":10}&apikey", data: "apikey=" + apikey, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'getuserinputs': function (apikey) {
        var result = {};
        $.ajax({url: path + "input/list.json", data: "apikey=" + apikey, dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
    'addinputproccess': function (apikey, inputid, processlist) {
        var result = {};
        $.ajax({url: path + "input/process/set.json?&inputid=" + inputid, data: "apikey=" + apikey + "&processlist=" + processlist, method: "POST", dataType: 'json', async: false, success: function (data) {
                result = data;
            }});
        return result;
    },
}

