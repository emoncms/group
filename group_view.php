<?php
defined('EMONCMS_EXEC') or die('Restricted access');
global $path, $fullwidth;
$fullwidth = true;
?>
<link href="<?php echo $path; ?>Modules/group/group.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/group/group.js"></script>

<!-------------------------------------------------------------------------------------------
MAIN
-------------------------------------------------------------------------------------------->
<div id="wrapper">
    <div class="sidebar">
        <!--<div style="padding-left:10px">
            <div id="sidebar-close"><i class="icon-remove"></i></div>
        </div>-->
        <div id="previous_user" style="padding: 0 0 10px 10px">
            <h3>Admin user</h3>'
        </div>

        <h3 style="padding-left:10px">Groups</h3>
        <div id="grouplist"></div>

        <div id="groupcreate"><i class="icon-plus"></i>New Group</div>
    </div>

    <div class="page-content" style="padding-top:15px">
        <div style="padding-bottom:15px">
            <button class="btn" id="sidebar-open" style="display:none"><i class="icon-list"></i></button>
            <div id="createuseraddtogroup" class="if-admin"><i class="icon-plus"></i>Create User</div>
            <div id="addmember" class="if-admin"><i class="icon-plus"></i>Add Member</div>
            <div class="userstitle"><span id="groupname">Users</span></div>
            <div id="groupdescription"></div>

        </div>
        <table id="userlist-table" class="table hide">
            <tr><th>Username</th><th>Active Feeds</th><th>Role  <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)
                                                                       - Sub-administrator: access to the list of members, group dashboards and group graphs
                                                                       - Member: view access to dashboards
                                                                       - Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign" /></th><th></th></tr>
            <tbody id="userlist"></tbody>
        </table>

        <button id="deletegroup" class="hide">Delete Group</button>

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
            <input id="group-create-name" type="text" maxlength="64"></p>

        <p>Group Description:<br>
            <input id="group-create-description" type="text" maxlength="256"></p>

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="group-create-action" class="btn btn-primary">Create</button>
    </div>
</div>

<!-- ADD MEMBER TO GROUP -->
<div id="group-addmember-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="group-addmember-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="group-addmember-modal-label">Add member to group</h3>
    </div>
    <div class="modal-body">

        <p>Username:<br>
            <input id="group-addmember-username" type="text"></p>

        <p>Password:<br>
            <input id="group-addmember-password" type="password"></p>

        <p>Access   <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)
                       - Sub-administrator: access to the list of members, group dashboards and group graphs
                       - Member: view access to dashboards
                       - Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i>:</p>
        <select id="group-addmember-access">
            <option value=1>Administrator</option>
            <option value=2>Sub-administrator</option>
            <option value=3>Member</option>
            <option value=0 selected>Passive member</option>
        </select>

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="group-addmember-action" class="btn btn-primary">Add</button>
    </div>
</div>

<!-- CREATE USER AND ADD MEMBER TO GROUP -->
<div id="group-createuseraddtogroup-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="group-createuseraddtogroup-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="group-addmember-modal-label">Create user and add to group</h3>
    </div>
    <div class="modal-body">
        <p>Email:<br>
            <input id="group-createuseraddtogroup-email" type="email"></p>
        <p>Username:<br>
            <input id="group-createuseraddtogroup-username" type="text"></p>
        <p>Password:<br>
            <input id="group-createuseraddtogroup-password" type="password"></p>
        <p>Confirm password:<br>
            <input id="group-createuseraddtogroup-password-confirm" type="password"></p>
        <p>Access   <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)
                       - Sub-administrator: access to the list of members, group dashboards and group graphs
                       - Member: view access to dashboards
                       - Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i>:</p>
        <select id="group-createuseraddtogroup-role">
            <option value=1>Administrator</option>
            <option value=2>Sub-administrator</option>
            <option value=3>Member</option>
            <option value=0 selected>Passive member</option>
        </select>
        <div id="createuseraddtogroup-message"></div>

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="group-createuseraddtogroup-action" class="btn btn-primary">Create and add to group</button>
    </div>
</div>

<!-- REMOVE USER FROM GROUP -->
<div id="remove-user-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="remove-user-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="remove-user-modal-label">Remove user from group</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you wish to remove this user?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="remove-user-action" class="btn btn-danger">Remove</button>
    </div>
</div>

<!-- DELETE GROUP -->
<div id="delete-group-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="delete-group-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="delete-group-modal-label">Delete group</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you wish to delete this group?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="delete-group-action" class="btn btn-danger">Delete</button>
    </div>
</div>


<!-------------------------------------------------------------------------------------------
JAVASCRIPT
-------------------------------------------------------------------------------------------->
<script>
    var path = "<?php echo $path; ?>";
    sidebar_resize();
    var selected_groupid = 0;
    var grouplist = [];
    var user_role = 0;

// ----------------------------------------------------------------------------------------
// Draw: grouplist
// ----------------------------------------------------------------------------------------
    draw_grouplist();
// Startup group
    var selected_group = decodeURIComponent(window.location.hash).substring(1);
    console.log("Selectedgroup:" + selected_group)
    if (selected_group != "") {
        for (var gindex in grouplist) {
            if (grouplist[gindex].name == selected_group) {
                $(".group[gindex=" + gindex + "]").addClass('activated');
                var groupid = grouplist[gindex].groupid;
                selected_groupid = groupid;
                draw_userlist(groupid);
                $("#groupname").html(grouplist[gindex].name); // Place group name in title
                $("#groupdescription").html(grouplist[gindex].description); // Place group description in title
                $("#userlist-table").show(); // Show userlist table
                $("#deletegroup").show();
                $("#addmember").show(); // Show add user button
                $("#createuseraddtogroup").show(); // Show create user button
                $("#nogroupselected").hide(); // Hide no group selected alert
                if (grouplist[gindex].role != 1)
                    $('.if-admin').hide();
            }
        }
    }

    function draw_grouplist() {
        grouplist = group.grouplist();
        var out = "";
        for (var z in grouplist) {
            out += "<div class='group' gindex=" + z + " gid=" + grouplist[z].groupid + ">" + grouplist[z].name + "</div>";
        }
        $("#grouplist").html(out);
    }

// ----------------------------------------------------------------------------------------
// Action: click on group
// ----------------------------------------------------------------------------------------
    $("#grouplist").on("click", ".group", function () {
        // Group selection CSS
        $(".group").removeClass('activated');
        $(this).addClass('activated');
        // Get selected group from attributes
        var gindex = $(this).attr("gindex");
        var groupid = grouplist[gindex].groupid;
        selected_groupid = groupid;
        draw_userlist(groupid);
        document.location.hash = grouplist[gindex].name
        $("#groupname").html(grouplist[gindex].name); // Place group name in title
        $("#groupdescription").html(grouplist[gindex].description); // Place group description in title
        $("#userlist-table").show(); // Show userlist table
        $("#deletegroup").show();
        $("#addmember").show(); // Show add user button        
        $("#createuseraddtogroup").show();
        $("#nogroupselected").hide(); // Hide no group selected alert
    });
    function select_group(gindex) {

    }

    function draw_userlist(groupid) {
        // Load user list
        var userlist = group.userlist(groupid);
        userlist.sort(function (a, b) {
            return b.activefeeds - a.activefeeds;
        });
        if (userlist.success != undefined) {
            alert(userlist.message);
        } else {
            // Compile the user list html
            var out = "";
            for (var z in userlist) {
                out += "<tr>";
                out += "<td class='user' uid=" + userlist[z].userid + "><a href='" + path + "group/setuser?groupid=" + selected_groupid + "&userid=" + userlist[z].userid + "'>" + userlist[z].username + "</a></td>";
                // Active feds

                var prc = userlist[z].activefeeds / userlist[z].feeds;
                var color = "#00aa00";
                if (prc < 0.5)
                    color = "#aaaa00";
                if (prc < 0.1)
                    color = "#aa0000";
                if (userlist[z].feeds == 0)
                    color = "#000";
                out += "<td><b><span style='color:" + color + "'>" + userlist[z].activefeeds + "</span>/" + userlist[z].feeds + "</b></td>";
                // Role
                var role;
                switch (userlist[z].role) {
                    case 0:
                        role = 'Passive member';
                        break;
                    case 1:
                        role = 'Administrator';
                        break;
                    case 2:
                        role = 'Sub-administrator';
                        break;
                    case 3:
                        role = 'Member';
                        break;
                }
                out += "<td>" + role + "</td>";
                // Actions
                out += "<td><i class='removeuser icon-trash' style='cursor:pointer' title='Remove User' uid=" + userlist[z].userid + "></i></td>";
                out += "</tr>";
            }
            $("#userlist").html(out); // Place userlist html in userlist table   
        }
    }

// ----------------------------------------------------------------------------------------
// Action: Group creation
// ----------------------------------------------------------------------------------------
    $("#groupcreate").click(function () {
        $('#group-create-modal input').val('');
        $('#group-create-modal').modal('show');
    });
    $("#group-create-action").click(function () {
        var name = $("#group-create-name").val();
        var description = $("#group-create-description").val();
        var organization = $("#group-create-organization").val() || 'N/A';
        var area = $("#group-create-area").val() || 'N/A';
        var visibility = $("#group-create-visibility").val() || 'private';
        var access = $("#group-create-access").val() || 'closed';
        var result = group.create(name, description, organization, area, visibility, access);
        if (!result.success) {
            alert(result.message);
        } else {
            $('#group-create-modal').modal('hide');
            draw_grouplist();
        }
    });
// ----------------------------------------------------------------------------------------
// Action: Add member
// ----------------------------------------------------------------------------------------
    $("#addmember").click(function () {
        $('#group-addmember-modal input').val('');
        $("#group-addmember-access").val(0);
        $('#group-addmember-modal').modal('show');
    });
    $("#group-addmember-action").click(function () {
        var username = $("#group-addmember-username").val();
        var password = $("#group-addmember-password").val();
        var access = $("#group-addmember-access").val();
        var result = group.addmemberauth(selected_groupid, username, password, access);
        if (!result.success) {
            alert(result.message);
        } else {
            $('#group-addmember-modal').modal('hide');
            draw_userlist(selected_groupid);
        }
    });
    // ----------------------------------------------------------------------------------------
// Action: Create user and add to group
// ----------------------------------------------------------------------------------------
    $("#createuseraddtogroup").click(function () {
        $('#group-createuseraddtogroup-modal input').val('');
        $("#group-createuseraddtogroup-role").val(0);
        $('#group-createuseraddtogroup-modal').modal('show');
    });
    $("#group-createuseraddtogroup-action").click(function () {
        var email = $("#group-createuseraddtogroup-email").val();
        var username = $("#group-createuseraddtogroup-username").val();
        var password = $("#group-createuseraddtogroup-password").val();
        var confirm_password = $("#group-createuseraddtogroup-password-confirm").val();
        var role = $("#group-createuseraddtogroup-role").val();

        if (password != confirm_password)
            $("#createuseraddtogroup-message").html("<div class='alert alert-error'>Passwords do not match</div>");
        else {
            var result = group.createuseraddtogroup(selected_groupid, email, username, password, role);
            if (!result.success) {
                alert(result.message);
            } else {
                $('#group-createuseraddtogroup-modal').modal('hide');
                draw_userlist(selected_groupid);
            }
        }
    });
// ----------------------------------------------------------------------------------------
// Action: Remove user
// ----------------------------------------------------------------------------------------
    $("#userlist").on("click", ".removeuser", function () {
        $('#remove-user-modal').modal('show');
        var userid = $(this).attr("uid");
        $('#remove-user-modal').attr("uid", userid);
    });
    $("#remove-user-action").click(function () {
        $('#remove-user-modal').modal('hide');
        var userid = $('#remove-user-modal').attr("uid");
        var result = group.removeuser(selected_groupid, userid);
        if (!result.success) {
            alert(result.message);
        } else {
            draw_userlist(selected_groupid);
        }
    });
// ----------------------------------------------------------------------------------------
// Action: Delete group
// ----------------------------------------------------------------------------------------
    $("#deletegroup").click(function () {
        $('#delete-group-modal').modal('show');
    });
    $("#delete-group-action").click(function () {
        $('#delete-group-modal').modal('hide');
        var result = group.deletegroup(selected_groupid);
        if (!result.success) {
            alert(result.message);
        } else {
            draw_grouplist();
            $("#groupname").html("Users");
            $("#groupdescription").html("");
            $("#userlist-table").hide();
            $("#deletegroup").hide();
            $("#addmember").hide();
            $("#createuseraddtogroup").hide();
            $("#nogroupselected").show();
        }
    });
// ----------------------------------------------------------------------------------------
// Sidebar
// ----------------------------------------------------------------------------------------
    $("#sidebar-open").click(function () {
        $(".sidebar").css("left", "250px");
        $("#sidebar-close").show();
    });
    $("#sidebar-close").click(function () {
        $(".sidebar").css("left", "0");
        $("#sidebar-close").hide();
    });
    function sidebar_resize() {
        var width = $(window).width();
        var height = $(window).height();
        var nav = $(".navbar").height();
        $(".sidebar").height(height - nav - 40);
        if (width < 1024) {
            $(".sidebar").css("left", "0");
            $("#wrapper").css("padding-left", "0");
            $("#sidebar-open").show();
        } else {
            $(".sidebar").css("left", "250px");
            $("#wrapper").css("padding-left", "250px");
            $("#sidebar-open").hide();
            $("#sidebar-close").hide();
        }
    }

    $(window).resize(function () {
        sidebar_resize();
    });
</script>
