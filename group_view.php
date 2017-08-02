<?php
defined('EMONCMS_EXEC') or die('Restricted access');
global $path, $fullwidth, $session;
$fullwidth = true;
?>
<link href="<?php echo $path; ?>Modules/group/group.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/group/group.js"></script>
<link href="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/user/user.js"></script>

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
            <div id="create-inputs-feeds" class="if-admin groupselected"><i class="icon-trash"></i>Update inputs/feeds</div>
            <div id="editgroup" class="if-admin groupselected"><i class="icon-edit"></i> Edit Group</div>
            <div id="createuseraddtogroup" class="if-admin groupselected"><i class="icon-plus"></i>Create User</div>
            <div id="addmember" class="if-admin groupselected"><i class="icon-plus"></i>Add Member</div>
            <div class="userstitle"><span id="groupname">Users</span></div>
            <div id="groupdescription"></div>

        </div>
        <div class="table-headers hide groupselected">
            <div class="user-name">Username</div>
            <div class="user-active-feeds">Active feeds</div>
            <div class="user-role">Role <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)
                                           - Sub-administrator: access to the list of members, group dashboards and group graphs
                                           - Member: view access to dashboards
                                           - Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i></div>
            <div class="user-actions">
                <button class="btn feed-graph hide" title="Graph view"><i class="icon-eye-open"></i></button>                
            </div>
        </div>
        <div id="userlist-div" class="hide"></div>
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

<!-- FEED EXPORT -->
<div id="feedExportModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="feedExportModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="feedExportModalLabel"><b><span id="SelectedExport"></span></b> CSV export</h3>
    </div>
    <div class="modal-body">
        <p>Select the time range and interval that you wish to export: </p>
        <table class="table">
            <tr>
                <td>
                    <p><b>Start date & time</b></p>
                    <div id="datetimepicker1" class="input-append date">
                        <input id="export-start" data-format="dd/MM/yyyy hh:mm:ss" type="text" />
                        <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </td>
                <td>
                    <p><b>End date & time</b></p>
                    <div id="datetimepicker2" class="input-append date">
                        <input id="export-end" data-format="dd/MM/yyyy hh:mm:ss" type="text" />
                        <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <p><b>Interval</b></p>
                    <select id="export-interval" >
                        <option value=1>Auto</option>
                        <option value=5>5s</option>
                        <option value=10>10s</option>
                        <option value=30>30s</option>
                        <option value=60>1 min</option>
                        <option value=300>5 mins</option>
                        <option value=600>10 mins</option>
                        <option value=900>15 mins</option>
                        <option value=1800>30 mins</option>
                        <option value=3600>1 hour</option>
                        <option value=21600>6 hour</option>
                        <option value=43200>12 hour</option>
                        <option value=86400>Daily</option>
                        <option value=604800>Weekly</option>
                        <option value=2678400>Monthly</option>
                        <option value=31536000>Annual</option>
                    </select>
                </td>
                <td>
                    <p><b>Date time format</b></p>
                    <div class="checkbox">
                        <label><input type="checkbox" id="export-timeformat" value="" checked>Excel (d/m/Y H:i:s)</label>
                    </div>
                    <label>Offset secs (for daily)&nbsp;<input id="export-timezone-offset" type="text" class="input-mini" disabled=""></label>
                </td>
            </tr>
        </table>
        <div class="alert alert-info">
            <p>Selecting an interval shorter than the feed interval (or Auto) will use the feed interval instead. Averages are only returned for feed engines with built in averaging.</p>
            <p>Date time in excel format is in user timezone. Offset can be set if exporting in Unix epoch time format.</p>
        </div>
    </div>
    <div class="modal-footer">
        <div id="downloadsizeplaceholder" style="float: left">Estimated download size: <span id="downloadsize">0</span>MB</div>
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Close'); ?></button>
        <button class="btn" id="export">Export</button>
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
                    // Active feeds colors
                    var prc = userlist[z].activefeeds / userlist[z].totalfeeds;
                    var color = "#00aa00";
                    if (prc < 0.5)
                        color = "#aaaa00";
                    if (prc < 0.1)
                        color = "#aa0000";
                    if (userlist[z].totalfeeds == 0)
                        color = "#000";
                    // out += "<td><b><span style='color:" + color + "'>" + userlist[z].activefeeds + "</span>/" + userlist[z].totalfeeds + "</b></td>";

                    // html
                    out += "<div class='user' uid='" + userlist[z].userid + "'>";
                    out += "<div class='user-info'>";
                    if (userlist[z].admin_rights != 'full')
                        out += "<div class='user-name'>" + userlist[z].username + "</div>";
                    else
                        out += "<div class='user-name'><a class='setuser' href='" + path + "group/setuser?groupid=" + selected_groupid + "&userid=" + userlist[z].userid + "'>" + userlist[z].username + "</a></div>";
                    out += "<div class='user-active-feeds'><b><span style='color:" + color + "'>" + userlist[z].activefeeds + "</span>/" + userlist[z].totalfeeds + "</b></div> <div class='user-role'>" + role + "</div>";
                    out += "<div class='user-actions'>";
                    if (userlist[z].userid != my_userid)
                        out += "<i class='removeuser icon-trash if-admin' style='cursor:pointer' title='Remove User' uid=" + userlist[z].userid + " admin-rights=" + userlist[z].admin_rights + "> </i>";
                    out += "</div>"; // user-actions
                    out += "</div>"; // user-info
                    out += "<div class='user-feeds-inputs hide' uid='" + userlist[z].userid + "'>";
                    out += "<div class='user-feedslist'>";
                    out += "<div class='user-feedslist-inner'>";
                    userlist[z].feedslist.forEach(function (feed) {
                        out += "<div class='feed'>";
                        out += "<input type='checkbox' fid='" + feed.id + "' />";
                        out += "<div class='feed-name'>" + feed.tag + ':' + feed.name + "</div>";
                        out += "<div class='feed-download' fid='" + feed.id + "' tag='" + feed.tag + "' name='" + feed.name + "'><i class='icon-download'style='cursor:pointer' title='Download csv'> </i></div>";
                        out += "<div class='feed-value'>" + list_format_value(feed.value) + "</div>";
                        out += "<div class='feed-time'>" + list_format_updated(feed.time) + "</div>";
                        out += "</div>"; // feed
                    });
                    out += "</div>"; // user-feedslist-inner
                    out += "</div>"; // user-feedslist
                    //out += "<div class='user-inputs hide'></div>";
                    out += "</div>"; // user-feeds-inputs
                    out += "</div>"; // user
                }
                $("#userlist-div").html(out); // Place userlist html in userlist table 
                $('#userlist-div').show();
                //$(".user-feedslist .feed").last().css("border-bottom","1px solid #aaa");




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
// Action: Show feeds of a user
// ----------------------------------------------------------------------------------------
    $('body').on('click', '.user', function () {
        var userid = $(this).attr('uid');
        $('.user-feeds-inputs[uid=' + userid + ']').toggle();
    });
// ----------------------------------------------------------------------------------------
// Action: Remove user
// ----------------------------------------------------------------------------------------
    $(".removeuser").click(function (e) {
        e.stopPropagation();
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
// Action: Download feed (copied, but modified, from feedlist_view_classic.php)
// ----------------------------------------------------------------------------------------
    $('.feed-download').click(function (e) {
        e.stopPropagation();
        $("#export").attr('export-type', "feed");
        $("#export").attr('feedid', $(this).attr('fid'));
        var name = $(this).attr('tag') + ":" + $(this).attr('name');
        $("#export").attr('name', name);
        $("#SelectedExport").html(name);
        calculate_download_size(1);

        if ($("#export-timezone-offset").val() == "") {
            var timezoneoffset = user.timezoneoffset();
            if (timezoneoffset == null)
                timezoneoffset = 0;
            $("#export-timezone-offset").val(parseInt(timezoneoffset));
        }
        $('#feedExportModal').modal('show');
    });

    $('#datetimepicker1').datetimepicker({
        language: 'en-EN'
    });

    $('#datetimepicker2').datetimepicker({
        language: 'en-EN',
        useCurrent: false //Important! See issue #1075
    });

    $('#datetimepicker1').on("changeDate", function (e) {
        $('#datetimepicker2').data("datetimepicker").setStartDate(e.date);
    });

    $('#datetimepicker2').on("changeDate", function (e) {
        $('#datetimepicker1').data("datetimepicker").setEndDate(e.date);
    });

    now = new Date();
    today = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 00, 00);
    var picker1 = $('#datetimepicker1').data('datetimepicker');
    var picker2 = $('#datetimepicker2').data('datetimepicker');
    picker1.setLocalDate(today);
    picker2.setLocalDate(today);
    picker1.setEndDate(today);
    picker2.setStartDate(today);

    $('#export-interval, #export-timeformat').on('change', function (e)
    {
        $("#export-timezone-offset").prop("disabled", $("#export-timeformat").prop('checked'));
        if ($("#export").attr('export-type') == 'group') {
            var downloadsize = calculate_download_size($("#export").attr('feedcount'));
        } else {
            calculate_download_size(1);
        }
    });

    $('#datetimepicker1, #datetimepicker2').on('changeDate', function (e)
    {
        if ($("#export").attr('export-type') == 'group') {
            var downloadsize = calculate_download_size($("#export").attr('feedcount'));
        } else {
            calculate_download_size(1);
        }
    });

    $("#export").click(function ()
    {
        var export_start = parse_timepicker_time($("#export-start").val());
        var export_end = parse_timepicker_time($("#export-end").val());
        var export_interval = $("#export-interval").val();
        var export_timezone_offset = parseInt($("#export-timezone-offset").val());
        var export_timeformat = ($("#export-timeformat").prop('checked') ? 1 : 0);
        if (export_timeformat) {
            export_timezone_offset = 0;
        }
        if (!export_start) {
            alert("Please enter a valid start date.");
            return false;
        }
        if (!export_end) {
            alert("Please enter a valid end date.");
            return false;
        }
        if (export_start >= export_end) {
            alert("Start date must be further back in time than end date.");
            return false;
        }
        if (export_interval == "") {
            alert("Please select interval to download.");
            return false;
        }
        var downloadlimit = <?php
global $feed_settings;
echo $feed_settings['csvdownloadlimit_mb'];
?>;
        var downloadsize = calculate_download_size(1);
        if (downloadsize > (downloadlimit * 1048576)) {
            var r = confirm("Estimated download file size is large.\nServer could take a long time or abort depending on stored data size.\Limit is " + downloadlimit + "MB.\n\nTry exporting anyway?");
            if (!r)
                return false;
        }
        $('#feedExportModal').modal('hide');
        result = group.csvexport(selected_groupid, $(this).attr('feedid'), export_start + export_timezone_offset, export_end + export_timezone_offset, export_interval, export_timeformat, $(this).attr('name'));
        if (result.success == false)
            alert(result.message);
    });

    function calculate_download_size(feedcount) {
        var export_start = parse_timepicker_time($("#export-start").val());
        var export_end = parse_timepicker_time($("#export-end").val());
        var export_interval = $("#export-interval").val();
        var export_timeformat_size = ($("#export-timeformat").prop('checked') ? 20 : 11);// bytes per timestamp
        var downloadsize = 0;
        if (!(!$.isNumeric(export_start) || !$.isNumeric(export_end) || !$.isNumeric(export_interval) || export_start > export_end)) {
            downloadsize = ((export_end - export_start) / export_interval) * (export_timeformat_size + (feedcount * 7)); // avg bytes per data
        }
        $("#downloadsize").html((downloadsize / 1024 / 1024).toFixed(2));
        var downloadlimit = <?php
global $feed_settings;
echo $feed_settings['csvdownloadlimit_mb'];
?>;
        $("#downloadsizeplaceholder").css('color', (downloadsize == 0 || downloadsize > (downloadlimit * 1048576) ? 'red' : ''));
        return downloadsize;
    }

    function parse_timepicker_time(timestr) {
        var tmp = timestr.split(" ");
        if (tmp.length != 2)
            return false;

        var date = tmp[0].split("/");
        if (date.length != 3)
            return false;

        var time = tmp[1].split(":");
        if (time.length != 3)
            return false;

        return new Date(date[2], date[1] - 1, date[0], time[0], time[1], time[2], 0).getTime() / 1000;
    }

// ----------------------------------------------------------------------------------------
// Action: Show User actions buttons when feed check boxes are ticked
// ----------------------------------------------------------------------------------------
    $('.feed input').click(function (e) {
        e.stopPropagation();
        var any_checked = false;
        $('.feed input').each(function () {
            if ($(this).is(':checked'))
                any_checked = true;
        })
        if (any_checked)
            $('.feed-graph').show();
        else
            $('.feed-graph').hide();
    });

// ----------------------------------------------------------------------------------------
// Action: open graph page
// ----------------------------------------------------------------------------------------
    $('.feed-graph').click(function (e) {
        var feeds = [];
        $('.feed input').each(function () {
            if ($(this).is(':checked'))
                feeds.push($(this).attr('fid'))
        });
        window.location = path + "graph/groupgraph/" + selected_groupid + ',' + feeds.join(",");
    });


// ----------------------------------------------------------------------------------------
// Other
// ----------------------------------------------------------------------------------------
    $(".setuser").click(function (e) {
        e.stopPropagation();
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
            //group.createinputs(list_apikeys[apikey]);
            var d = new Date();
            var n = d.getTime() / 1000; // seconds
            for (var z = 0; z < 200; z++) {
                group.updateinputs(list_apikeys[apikey], n, n % 937);
                n = n - 10;
            }
        }
        /*var name = 0;
         for (var apikey in list_apikeys) {
         var userinputs = group.getuserinputs(list_apikeys[apikey]);
         for (z = 0; z < 3; z++) {
         var feed = group.createfeeds(list_apikeys[apikey], name);
         if (feed.feedid != undefined)
         result = group.addinputproccess(list_apikeys[apikey], userinputs[z].id, '1:' + feed.feedid);
         name++;
         }
         }*/
    })
</script>
