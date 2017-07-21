<?php
defined('EMONCMS_EXEC') or die('Restricted access');
global $path, $fullwidth, $session;
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
            <div id="create-inputs-feeds" class="if-admin groupselected"><i class="icon-trash"></i>Create/update inputs/feeds</div>
            <div id="editgroup" class="if-admin groupselected"><i class="icon-edit"></i> Edit Group</div>
            <div id="createuseraddtogroup" class="if-admin groupselected"><i class="icon-plus"></i>Create User</div>
            <div id="addmember" class="if-admin groupselected"><i class="icon-plus"></i>Add Member</div>
            <div class="userstitle"><span id="groupname">Users</span></div>
            <div id="groupdescription"></div>

        </div>
        <div id="userlist-div" class="hide"><p>Group members</p></div>
        <table id="userlist-table" class="table hide">
            <tr><th>Username</th><th>Active Feeds</th><th>Role  <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)
                                                                       - Sub-administrator: access to the list of members, group dashboards and group graphs
                                                                       - Member: view access to dashboards
                                                                       - Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign" /></th><th class='userlistactions'></th><th class='userlistactions'></th></tr>
            <tbody id="userlist"></tbody>
        </table>
        <div id="userlist-alert" class="alert alert-block hide">
            <h4 class="alert-heading"></h4>
            <p></p>
        </div>
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

<!-- REMOVE USER -->
<div id="remove-user-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="remove-user-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="remove-user-modal-label">Remove user</h3>
    </div>
    <div class="modal-body">
        <span id="remove-user-modal-step-1">
            <p>What do you want to do?</p>
            <div  class="radio"><input type="radio" name="removeuser-whattodo" value="remove-from-group" /><span>Remove user from group</span></div>
            <div  class="radio"><input type="radio" name="removeuser-whattodo" value="delete" /><span>Completely remove user from database</span></div>
        </span>
        <span id="remove-user-modal-step-2" style="display:none"></span>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="remove-user-action" action="next" class="btn btn-danger">Next</button>
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

<!-- EDIT GROUP -->
<div id="edit-group-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="edit-group-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="edit-group-modal-label">Edit group</h3>
    </div>
    <div class="modal-body">
        <p>Group Name:<br>
            <input id="edit-group-name" type="text" maxlength="64"></p>
        <p>Group Description:<br>
            <input id="edit-group-description" type="text" maxlength="256"></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="edit-group-action" class="btn btn-primary">Done</button>
    </div>
</div>

<!-- FEEDS/INPUTS LIST -->
<div id="feedsinputs-list-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="feedsinputs-list-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="feedsinputs-list-modal-label"></h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="edit-group-action" data-dismiss="modal" class="btn btn-primary">Done</button>
    </div>
</div>

<!-------------------------------------------------------------------------------------------
JAVASCRIPT
-------------------------------------------------------------------------------------------->
<script>
    var path = "<?php echo $path; ?>";
    var my_userid = <?php echo $session["userid"]; ?>;
    console.log(my_userid);
    sidebar_resize();
    var selected_groupid = 0;
    var selected_groupindex = 0;
    var grouplist = [];
    var my_role = 0;
    var userlist = [];

// ----------------------------------------------------------------------------------------
// Draw: grouplist
// ----------------------------------------------------------------------------------------
    draw_grouplist();

// ----------------------------------------------------------------------------------------    
// Startup group
// ----------------------------------------------------------------------------------------
    var selected_group = decodeURIComponent(window.location.hash).substring(1);
    console.log("Selectedgroup:" + selected_group)
    if (selected_group != "") {
        for (var gindex in grouplist) {
            if (grouplist[gindex].name == selected_group) {
                $(".group[gindex=" + gindex + "]").addClass('activated');
                draw_group(gindex);
            }
        }
    }
    else {
        $('.groupselected').hide();
        $("#nogroupselected").show(); // Hide no group selected alert
    }


// ----------------------------------------------------------------------------------------
// Functions
// ----------------------------------------------------------------------------------------
    function draw_grouplist() {
        grouplist = group.grouplist();
        var out = "";
        for (var z in grouplist) {
            out += "<div class='group' gindex=" + z + " gid=" + grouplist[z].groupid + ">" + grouplist[z].name + "</div>";
        }
        $("#grouplist").html(out);
    }

    function draw_group(gindex) {
        var groupid = grouplist[gindex].groupid;
        selected_groupid = groupid;
        selected_groupindex = gindex;
        draw_userlist(groupid);
        $("#groupname").html(grouplist[gindex].name); // Place group name in title
        $("#groupdescription").html(grouplist[gindex].description); // Place group description in title
        $('.groupselected').show();
        $("#nogroupselected").hide(); // Hide no group selected alert
        if (grouplist[gindex].role != 1)
            $('.if-admin').hide();
    }

    function draw_userlist(groupid) {
        // Get session user role in group
        my_role = group.getsessionuserrole(groupid);
        // Load listof members
        userlist = group.userlist(groupid);
        if (userlist.success == false) {
            $('#userlist-div').hide();
            $('#userlist-table').hide();
            $('#userlist-alert h4').html('No users to show');
            $('#userlist-alert p').html(userlist.message);
            $('#userlist-alert').show();
        }
        else {
            userlist.sort(function (a, b) {
                return b.activefeeds - a.activefeeds;
            });
            if (userlist.success != undefined) {
                alert(userlist.message);
            } else {
                // Hide alert message
                $('#userlist-alert').hide();

                var out = "";
                for (var z in userlist) {
                    out += "<div class='user' uid='" + userlist[z].userid + "'>";
                    out += "<div class='user-info'>" + userlist[z].username + " - " + userlist[z].activefeeds + "/" + userlist[z].totalfeeds + " - " + userlist[z].role + "</div>";
                    out += "<div class='user-feeds-inputs hide' uid='" + userlist[z].userid + "'>";
                    out += "<div class='user-feedslist'>";
                    userlist[z].feedslist.forEach(function (feed) {
                        out += "<div class='feed'><input type='checkbox' fid='" + feed.id + "' />";
                        out += "<div class='feed-name'>" + feed.name + "</div>";
                        out += "<div class='feed-value'>" + list_format_value(feed.value) + "</div>";
                        out += "<div class='feed-time'>" + list_format_updated(feed.time) + "</div>";
                        out += "</div>"; // feed
                    });
                    out += "</div>"; // user-feedslist
                    out += "<div class='user-inputs hide'></div>";
                    out += "</div>"; // user-feeds-inputs
                    out += "</div>"; // user
                }
                $("#userlist-div").append(out); // Place userlist html in userlist table 
                $('#userlist-div').show();




                // Compile the user list html
                var out = "";
                for (var z in userlist) {
                    out += "<tr>";
                    if (my_role === 1 && userlist[z].admin_rights == "full")
                        out += "<td class='user' uid=" + userlist[z].userid + "><a href='" + path + "group/setuser?groupid=" + selected_groupid + "&userid=" + userlist[z].userid + "'>" + userlist[z].username + "</a></td>";
                    else
                        out += "<td class='user' uid=" + userlist[z].userid + ">" + userlist[z].username + "</td>";

                    // Active feeds
                    var prc = userlist[z].activefeeds / userlist[z].totalfeeds;
                    var color = "#00aa00";
                    if (prc < 0.5)
                        color = "#aaaa00";
                    if (prc < 0.1)
                        color = "#aa0000";
                    if (userlist[z].totalfeeds == 0)
                        color = "#000";
                    out += "<td><b><span style='color:" + color + "'>" + userlist[z].activefeeds + "</span>/" + userlist[z].totalfeeds + "</b></td>";
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
                    if (userlist[z].userid == my_userid)
                        out += '<td></td>';
                    else
                        out += "<td><i class='removeuser icon-trash if-admin' style='cursor:pointer' title='Remove User' uid=" + userlist[z].userid + " admin-rights=" + userlist[z].admin_rights + "> </i></td > ";
                    //Feeds list
                    if (my_role != 1 && my_role != 2)
                        out += '<td></td>';
                    else
                        out += "<td><i class='showfeeds icon-list-alt' style='cursor:pointer' index='" + z + "' title='Show feeds' uid=" + userlist[z].userid + " admin-rights=" + userlist[z].admin_rights + "> </i></td > ";
                    // Close table row
                    out += "</tr>";
                }
                $("#userlist").append(out); // Place userlist html in userlist table 
                $('#userlist-table').show();
            }
        }
    }

    // Format value dynamically  (copied from feedlist_view.php)
    function list_format_value(value) {
        if (value == null)
            return 'NULL';
        value = parseFloat(value);
        if (value >= 1000)
            value = parseFloat((value).toFixed(0));
        else if (value >= 100)
            value = parseFloat((value).toFixed(1));
        else if (value >= 10)
            value = parseFloat((value).toFixed(2));
        else if (value <= -1000)
            value = parseFloat((value).toFixed(0));
        else if (value <= -100)
            value = parseFloat((value).toFixed(1));
        else if (value < 10)
            value = parseFloat((value).toFixed(2));
        return value;
    }

    // Calculate and color updated time (copied from feedlist_view.php)
    function list_format_updated(time) {
        time = time * 1000;
        var servertime = (new Date()).getTime();// - table.timeServerLocalOffset;
        var update = (new Date(time)).getTime();

        var secs = (servertime - update) / 1000;
        var mins = secs / 60;
        var hour = secs / 3600;
        var day = hour / 24;

        var updated = secs.toFixed(0) + "s";
        if ((update == 0) || (!$.isNumeric(secs)))
            updated = "n/a";
        else if (secs < 0)
            updated = secs.toFixed(0) + "s"; // update time ahead of server date is signal of slow network
        else if (secs.toFixed(0) == 0)
            updated = "now";
        else if (day > 7)
            updated = "inactive";
        else if (day > 2)
            updated = day.toFixed(1) + " days";
        else if (hour > 2)
            updated = hour.toFixed(0) + " hrs";
        else if (secs > 180)
            updated = mins.toFixed(0) + " mins";

        secs = Math.abs(secs);
        var color = "rgb(255,0,0)";
        if (secs < 25)
            color = "rgb(50,200,50)"
        else if (secs < 60)
            color = "rgb(240,180,20)";
        else if (secs < (3600 * 2))
            color = "rgb(255,125,20)"

        return "<span style='color:" + color + ";'>" + updated + "</span>";
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
        document.location.hash = grouplist[gindex].name
        draw_group(gindex);
    });

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
// Action: Edit group
// ----------------------------------------------------------------------------------------
    $("#editgroup").click(function () {
        $("#edit-group-name").val(grouplist[selected_groupindex].name);
        $("#edit-group-description").val(grouplist[selected_groupindex].description);
        $("#edit-group-organization").val(grouplist[selected_groupindex].organization);
        $("#edit-group-area").val(grouplist[selected_groupindex].area);
        $("#edit-group-visibility").val(grouplist[selected_groupindex].visibility);
        $("#edit-group-access").val(grouplist[selected_groupindex].access);
        $('#edit-group-modal').modal('show');
    });

    $('#edit-group-action').click(function () {
        var name = $("#edit-group-name").val();
        var description = $("#edit-group-description").val();
        var organization = $("#edit-group-organization").val() || 'N/A';
        var area = $("#edit-group-area").val() || 'N/A';
        var visibility = $("#edit-group-visibility").val() || 'private';
        var access = $("#edit-group-access").val() || 'closed';
        var result = group.editgroup(selected_groupid, name, description, organization, area, visibility, access);
        if (!result.success) {
            alert(result.message);
        } else {
            draw_grouplist();
            $('#groupname').html(grouplist[selected_groupindex].name);
            $('#groupdescription').html(grouplist[selected_groupindex].description);
            $('#edit-group-modal').modal('hide');
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
        $('#remove-user-modal-step-1').show();
        $('#remove-user-modal-step-2').hide();
        $('#remove-user-action').html('Next');
        $('#remove-user-action').attr('action', 'next');

        var userid = $(this).attr("uid");
        $('#remove-user-modal').attr("uid", userid);

        var admin_rights = $(this).attr("admin-rights");
        if (admin_rights != "full") {
            $('[name="removeuser-whattodo"][value="delete"]').attr('disabled', true);
        }
        else {
            $('[name="removeuser-whattodo"][value="delete"]').attr('disabled', false);
        }

        $('#remove-user-modal').modal('show');
    });
    $("#remove-user-action").click(function () {
        var action = $(this).attr('action');
        if (action == 'next') {
            $('#remove-user-modal-step-1').hide();
            var what_to_do = $('input[name="removeuser-whattodo"]:checked').val();
            if (what_to_do == 'remove-from-group') {
                $('#remove-user-modal-step-2').html('<p>Are you sure you want to remove this user from group?</p>');
                $(this).attr('action', 'remove-from-group');
            }
            else {
                $('#remove-user-modal-step-2').html('<p>Are you sure you wish to completely delete this user from the database?</p><p>All the data will be lost</p>');
                $(this).attr('action', 'delete-from-database');
            }
            $('#remove-user-modal-step-2').show();
            $('#remove-user-action').html('Done')
        }
        else if (action == "remove-from-group") {
            $('#remove-user-modal').modal('hide');
            var userid = $('#remove-user-modal').attr("uid");
            var result = group.removeuser(selected_groupid, userid);
            if (!result.success) {
                alert(result.message);
            } else {
                draw_userlist(selected_groupid);
            }
        }
        else if (action == "delete-from-database") {
            $('#remove-user-modal').modal('hide');
            var userid = $('#remove-user-modal').attr("uid");
            var result = group.fullremoveuser(selected_groupid, userid);
            if (!result.success) {
                alert(result.message);
            } else {
                draw_userlist(selected_groupid);
            }
        }
    });

    /*
     <span id="removeuser-confirm">
     <p>Are you sure you want to remove this user from group?</p>
     <p>Are you sure you wish to completely delete this user from the database?</p>
     <p>All the data will be lost</p>
     </span>*/
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
            $('.groupselected').hide();
            $("#nogroupselected").show();
        }
    });
// ----------------------------------------------------------------------------------------
// Action: Show feeds of a user
// ----------------------------------------------------------------------------------------
    $(".showfeeds").click(function () {
        var index = $(this).attr('index');
        var feedslist = group.getuserfeeds(selected_groupid, userlist[index].userid);
        if (feedslist.success == false) {
            alert(feedslist.message);
        }
        else {
            $('#feedsinputs-list-modal-label').html(userlist[index].username + "'s <strong>public</strong> feeds");
            if (Object.keys(feedslist).length == 0)
                $('#feedsinputs-list-modal .modal-body').html('<div class="alert alert-block"><p>The user hasn\'t got any <strong>public</strong> feeds</p></div>');
            else {
                $('#feedsinputs-list-modal .modal-body').html('<table class="table" id="feeds-list"><th>Id</th><th>Tag</th>\n\
        <th>Name</th><th>Process list</th><th>Datatype</th><th>Engine</th><th>Size</th><th>Updated</th><th>Value</th><th></th></table>');
                feedslist.forEach(function (feed) {
                    $('#feeds-list').append('<tr>\n\
                    <td>' + feed.id + '</td> \n\
                    <td>' + feed.tag + '</td> \n\
                    <td>' + feed.name + '</td> \n\
                    <td>' + feed.processList + '</td> \n\
                    <td>' + feed.datatype + '</td> \n\
                    <td>' + feed.engine + '</td> \n\
                    <td>' + feed.size + '</td> \n\
                    <td>' + feed.time + '</td> \n\
                    <td>' + feed.value + '</td> \n\
            </tr>');
                });
            }

            $('#feedsinputs-list-modal').modal('show');
        }
    });


    $('.user').click(function () {
        var userid = $(this).attr('uid');
        $('.user-feeds-inputs[uid=' + userid + ']').toggle();
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
    // For development
    $('#create-inputs-feeds').click(function () {
        var list_apikeys = group.getapikeys(selected_groupid);
        for (var apikey in list_apikeys) {
            group.createinputs(list_apikeys[apikey]);
        }
        var name = 0;
        for (var apikey in list_apikeys) {
            var userinputs = group.getuserinputs(list_apikeys[apikey]);
            for (z = 0; z < 3; z++) {
                var feed = group.createfeeds(list_apikeys[apikey], name);
                if (feed.feedid != undefined)
                    result = group.addinputproccess(list_apikeys[apikey], userinputs[z].id, '1:' + feed.feedid);
                name++;
            }
        }
    })
</script>
