
var group = {
  
  'create':function(name, description){
    var result = {};
    $.ajax({ url: path+"group/create", data: "name="+name+"&description="+description, dataType: 'json', async: false, success: function(data){result = data;} });
    return result;
  },
  
  'adduserauth':function(groupid,username,password,access){
    var result = {};
    $.ajax({ url: path+"group/adduserauth", data: "groupid="+groupid+"&username="+username+"&password="+password+"&access="+access, dataType: 'json', async: false, success: function(data){result = data;} });
    return result;
  },
  
  'grouplist':function(){
    var result = {};
    $.ajax({ url: path+"group/grouplist", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },
  
  'userlist':function(groupid){
    var result = {};
    $.ajax({ url: path+"group/userlist", data: "groupid="+groupid, dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  }
}

