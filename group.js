var group = {
    apiCall: function (endpoint, method, data, succeed, fail) {
        fail = fail || function(msg) {
            alert("Error: " + msg)
        }

        return $.ajax({
            url: path + endpoint,
            data: data,
            dataType: 'json',
            success: function (data) {
                if (typeof(data) == 'object' && 'success' in data && !data.success) {
                    msg = data.message ? data.message.replace(/\\n/g, '\n') : 'Error'
                    fail(msg)
                } else {
                    if (typeof(data) === 'object' && 'message' in data && snackbar) {
                        snackbar(data.message);
                    }

                    succeed(data)
                }
            },
            error: function(request, status, error) {
                fail(status + ": " + error)
            }
        })
    },

    create: function (data, succeed, fail) {
        group.apiCall('group/create', 'GET', data, succeed, fail)
    },
    editgroup: function (data, succeed, fail) {
        group.apiCall('group/editgroup', 'GET', data, succeed, fail)
    },
    addmemberauth: function (data, succeed, fail) {
        group.apiCall('group/addmemberauth', 'GET', data, succeed, fail)
    },
    createuseraddtogroup: function (data, succeed, fail) {
        group.apiCall('group/createuseraddtogroup', 'POST', data, succeed, fail)
    },
    removeuser: function (data, succeed, fail) {
        group.apiCall('group/removeuser', 'GET', data, succeed, fail)
    },
    fullremoveuser: function (data, succeed, fail) {
        group.apiCall('group/fullremoveuser', 'GET', data, succeed, fail)
    },
    setuserinfo: function (data, succeed, fail) {
        group.apiCall('group/setuserinfo', 'POST', data, succeed, fail)
    },
    grouplist: function(succeed, fail) {
        group.apiCall('group/grouplist', 'GET', null, succeed, fail)
    },
    extendedgrouplist: function(succeed, fail) {
        group.apiCall('group/mygroups', 'GET', null, succeed, fail)
    },
    deletegroup: function(groupid, succeed, fail) {
        group.apiCall('group/delete', 'GET', { groupid: groupid }, succeed, fail)
    },
    userlist: function (groupid, succeed, fail) {
        group.apiCall('group/userlist', 'GET', { groupid: groupid }, succeed, fail)
    },
//  Unused
//    getuserfeeds: function (data, userid) {
//        group.apiCall('group/getuserfeeds', 'GET', data, succeed)
//    },
    getsessionuserrole: function (groupid, succeed, fail) {
        group.apiCall('group/getsessionuserrole', 'GET', { groupid: groupid }, succeed, fail)
    },
    setMultiFeedProcessList: function (data, succeed, fail) {
        group.apiCall('group/setmultifeedprocesslist', 'POST', data, succeed, fail)
    },
    setProcessList: function (data, succeed, fail) {
        group.apiCall('group/setprocesslist', 'POST', data, succeed, fail)
    },
    deleteTask: function (data, succeed, fail) {
        group.apiCall('group/deletetask', 'POST', data, succeed, fail)
    },
    setTaskEnabled: function (data, succeed, fail) {
        group.apiCall('group/settaskenabled', 'POST', data, succeed, fail)
    },
    sendlogindetails: function (data, succeed, fail) {
        group.apiCall('group/sendlogindetails', 'POST', data, succeed, fail)
    },

// Used during development
//    getapikeys: function (userid, succeed, fail) {
//        group.apiCall('group/getapikeys', 'GET', { groupid: groupid }, succeed, fail)
//    },
//    createinputs: function (apikey, succeed, fail) {
//        group.apiCall('input/post.json', 'GET', {
//            node: 1,
//            json: JSON.stringify({power4:100,power2:100,power3:100}),
//            apikey: apikey
//        }, succeed, fail)
//    },
//    getfeeds: function (apikey, succeed, fail) {
//        group.apiCall('feed/list.json', 'GET', { apikey: apikey }, succeed, fail)
//    },
//    updatefeed: function (feedid, apikey, time, value, succeed, fail) {
//        group.apiCall('feed/insert.json', 'GET', {
//            id: feedid,
//            time: time,
//            value: value,
//            apikey: apikey
//        }, succeed, fail)
//    },
//    createfeeds: function (apikey, name, succeed, fail) {
//        group.apiCall('feed/create.json', 'GET', {
//            tag: 'Test'
//            name: 'Power ' + name
//            datatype: 1
//            engine: 5
//            options: JSON.stringify({interval: 10})
//            apikey: apikey
//        }, succeed, fail)
//    },
//    getuserinputs: function (apikey, succeed, fail) {
//        group.apiCall('input/list.json', 'GET', { apikey: apikey }, succeed, fail)
//    },
//    addinputproccess: function (apikey, inputid, processlist, succeed, fail) {
//        group.apiCall('input/process/set.json?inputid=' + inputid, 'POST', {
//            apikey:
//            processlist: processlist
//        }, succeed, fail)
//    },
}
