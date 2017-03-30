<?php
global $path, $fullwidth;
$fullwidth = true;
?>
<link href="<?php echo $path; ?>Modules/group/group.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Modules/group/group.js"></script>

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

<script>
var path = "<?php echo $path; ?>";
sidebar_resize();

var grouplist = group.grouplist();

var out = "";
for (var z in grouplist) {
    out += "<div class='group' gid="+grouplist[z].groupid+">"+grouplist[z].name+"</div>";
}
$("#grouplist").html(out);

$("#grouplist").on("click",".group",function(){
    $(".group").removeClass('activated');
    $(this).addClass('activated');
    var groupid = $(this).attr("gid");
    var groupname = $(this).html();
    var userlist = group.userlist(groupid);
    
    var out = "";
    for (var z in userlist) {
        out += "<tr>";
        out += "<td class='user' uid="+userlist[z].userid+">"+userlist[z].username+"</td>";
        out += "<td>"+userlist[z].access+"</td>";
        out += "</tr>";
    }
    $("#userlist").html(out);
    $("#groupname").html(groupname);
    $("#userlist-table").show();
    $("#adduser").show();
    $("#nogroupselected").hide();
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
