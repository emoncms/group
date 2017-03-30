<?php
global $path, $fullwidth;
$fullwidth = true;
?>
<link href="<?php echo $path; ?>Modules/group/group.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Modules/group/group.js"></script>

<!-------------------------------------------------------------------------------------------
  MAIN
-------------------------------------------------------------------------------------------->
<div id="wrapper">
    <div class="sidebar">
        <div style="padding-left:10px">
            <div id="sidebar-close"><i class="icon-remove"></i></div>
            <h3>Groups</h3>
        </div>
        
        <div id="grouplist"></div>
        
        <div id="groupcreate"><i class="icon-plus"></i>New Group</div>
    </div>

    <div class="page-content" style="padding-top:15px">
        <div style="padding-bottom:15px">
        <button class="btn" id="sidebar-open"><i class="icon-list"></i></button>
        <div id="adduser"><i class="icon-plus"></i>Add User</div>
        <div class="userstitle"><span id="groupname">Users</span></div>
        
        </div>
        <table id="userlist-table" class="table hide">
            <tr><th>Username</th><th>Access</th></tr>
            <tbody id="userlist"></tbody>
        </table>
        
        <div id="nogroupselected" class="alert alert-block">
            <h4 class="alert-heading">No Group Selected</h4>
            <p>Select or create group from sidebar</p>
        </div>
    </div>
</div>

<!-------------------------------------------------------------------------------------------
  MODALS
-------------------------------------------------------------------------------------------->
<!-- GROUP CREATE -->
<div id="group-create-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="group-create-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="group-create-modal-label">Create New Group</h3>
    </div>
    <div class="modal-body">

    <p>Group Name:<br>
    <input id="group-create-name" type="text"></p>

    <p>Group Description:<br>
    <input id="group-create-description" type="text"></p>
    
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="group-create-action" class="btn btn-primary">Create</button>
    </div>
</div>

<!-- ADD USER TO GROUP -->
<div id="group-adduser-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="group-adduser-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="group-adduser-modal-label">Add user to group</h3>
    </div>
    <div class="modal-body">

    <p>Username:<br>
    <input id="group-adduser-username" type="text"></p>

    <p>Password:<br>
    <input id="group-adduser-password" type="password"></p>

    <p>Access:<br>
    <select id="group-adduser-access">
      <option val=0>Normal</option>
      <option val=1>Administrator</option>
    </select>
    
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="group-adduser-action" class="btn btn-primary">Add</button>
    </div>
</div>

<!-------------------------------------------------------------------------------------------
  JAVASCRIPT
-------------------------------------------------------------------------------------------->
<script>
var path = "<?php echo $path; ?>";
sidebar_resize();

var selected_groupid = 0;

// ----------------------------------------------------------------------------------------
// Draw: grouplist
// ----------------------------------------------------------------------------------------
draw_grouplist();

function draw_grouplist() {
    var grouplist = group.grouplist();
    var out = "";
    for (var z in grouplist) {
        out += "<div class='group' gid="+grouplist[z].groupid+">"+grouplist[z].name+"</div>";
    }
    $("#grouplist").html(out);
}

// ----------------------------------------------------------------------------------------
// Action: click on group
// ----------------------------------------------------------------------------------------
$("#grouplist").on("click",".group",function(){
    // Group selection CSS
    $(".group").removeClass('activated');
    $(this).addClass('activated');
    // Get selected group from attributes
    var groupid = $(this).attr("gid");
    var groupname = $(this).html();
    selected_groupid = groupid;
    
    draw_userlist(groupid);

    $("#groupname").html(groupname);    // Place group name in title
    $("#userlist-table").show();        // Show userlist table
    $("#adduser").show();               // Show add user button
    $("#nogroupselected").hide();       // Hide no group selected alert
});

function draw_userlist(groupid) {
    // Load user list
    var userlist = group.userlist(groupid);
    
    // Compile the user list html
    var out = "";
    for (var z in userlist) {
        out += "<tr>";
        out += "<td class='user' uid="+userlist[z].userid+">"+userlist[z].username+"</td>";
        out += "<td>"+userlist[z].access+"</td>";
        out += "</tr>";
    }
    $("#userlist").html(out);           // Place userlist html in userlist table
}

// ----------------------------------------------------------------------------------------
// Action: Group creation
// ----------------------------------------------------------------------------------------
$("#groupcreate").click(function(){
    $('#group-create-modal').modal('show');
});

$("#group-create-action").click(function() {
    var name = $("#group-create-name").val();
    var description = $("#group-create-description").val();
    
    var result = group.create(name,description);
    $('#group-create-modal').modal('hide');
    alert(JSON.stringify(result));
    draw_grouplist();
});

// ----------------------------------------------------------------------------------------
// Action: Add user
// ----------------------------------------------------------------------------------------
$("#adduser").click(function(){
    $('#group-adduser-modal').modal('show');
});

$("#group-adduser-action").click(function() {
    var username = $("#group-adduser-username").val();
    var password = $("#group-adduser-password").val();
    var access = $("#group-adduser-access").val();
    
    var result = group.adduserauth(selected_groupid,username,password,access);
    $('#group-adduser-modal').modal('hide');
    alert(JSON.stringify(result));
    draw_userlist(selected_groupid);
});

// ----------------------------------------------------------------------------------------
// Sidebar
// ----------------------------------------------------------------------------------------
$("#sidebar-open").click(function(){
    $(".sidebar").css("left","250px");
    $("#sidebar-close").show();
});

$("#sidebar-close").click(function(){
    $(".sidebar").css("left","0");
    $("#sidebar-close").hide();
});

function sidebar_resize() {
    var width = $(window).width();
    var height = $(window).height();
    var nav = $(".navbar").height();
    $(".sidebar").height(height-nav-40);
    
    if (width<1024) {
        $(".sidebar").css("left","0");
        $("#wrapper").css("padding-left","0");
        $("#sidebar-open").show();
    } else {
        $(".sidebar").css("left","250px");
        $("#wrapper").css("padding-left","250px");
        $("#sidebar-open").hide();
        $("#sidebar-close").hide();
    }
}

$(window).resize(function(){
    sidebar_resize();
});
</script>
