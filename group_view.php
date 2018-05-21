<?php
//http://jsfiddle.net/nhft183g/2/

defined('EMONCMS_EXEC') or die('Restricted access');
global $path, $fullwidth, $session;
$fullwidth = true;
?>
<link href="<?php echo $path; ?>Modules/group/group.css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/group/group.js"></script>
<link href="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo $path; ?>Lib/bootstrap-datetimepicker-0.0.11/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/user/user.js"></script>
<?php if ($task_support === true) { ?>
    <script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/task/task.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/task/task-custom-table-fields.js"></script>
<?php } ?>
<!-------------------------------------------------------------------------------------------
MAIN
-------------------------------------------------------------------------------------------->
<div id="wrapper">
    <div class="sidebar">
        <div style="padding-left:10px">
            <div id="sidebar-close"><i class="icon-remove"></i></div>
        </div>
        <div id="previous_user" style="padding: 0 0 10px 10px">
            <h3>Admin user</h3>'
        </div>

        <h3 style="padding-left:10px">Search</h3>
        <div id="search-wrapper">
            <input id="search-box"></input>
            <div id="search-list">
                <h4 class="hide search-list-groups-show">Groups</h4>
                <div id="search-list-groups" class="hide search-list-groups-show"></div>
                <h4 class="hide search-list-users-show">Users</h4>
                <div id="search-list-users" class="hide search-list-users-show"></div>
                <h4 class="hide search-list-tags-show">Tags</h4>
                <div id="search-list-tags" class="hide search-list-tags-show"></div>
            </div>
        </div>

        <h3 style="padding-left:10px">Groups</h3>
        <div id="grouplist"></div>

        <div id="groupcreate"><i class="icon-plus"></i>New Group</div>
    </div>

    <div class="page-content" style="padding-top:15px">
        <div style="padding-bottom:15px">
            <button class="btn" id="sidebar-open" style="display:none"><i class="icon-list"></i></button>
            <!--<div id="create-inputs-feeds" class="if-admin groupselected"><i class="icon-trash"></i>Update inputs/feeds</div>-->
            <div id="deletegroup" class="if-admin groupselected"><i class="icon-trash"></i>Delete group</div>
            <div id="editgroup" class="if-admin groupselected"><i class="icon-edit"></i> Edit Group</div>
            <div id="createuseraddtogroup" class="if-admin groupselected"><i class="icon-plus"></i>Create User</div>
            <div id="addmember" class="if-admin groupselected"><i class="icon-plus"></i>Add Member</div>
            <div class="userstitle"><span id="groupname">Users</span></div>
            <div id="groupdescription"></div>

        </div>
        <div class="table-headers hide groupselected">
            <div class="user-name">Username</div>
            <div class="user-active-feeds">Active feeds</div>
            <div class="user-role">Role <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)&#10;- Sub-administrator: view access to the list of members, write access to group graphs&#10;- Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i></div>
            <div class="multiple-feeds-actions">
                <button class="btn feed-graph hide" title="Graph view"><i class="icon-eye-open"></i></button>                
                <button class="btn multiple-feed-download hide" title="Download feeds" type="multiple"><i class="icon-download"></i></button>                
                <button class="btn create-task hide if-admin" title="Create task"><i class="icon-list"></i></button>                
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

        <p>Role   <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)&#10;- Sub-administrator: view access to the list of members, write access to group graphs&#10;- Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i>:</p>
        <select id="group-addmember-access">
            <option value=1>Administrator</option>
            <option value=2>Sub-administrator</option>
            <!--<option value=3>Member</option>-->
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
        <p>Role   <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)&#10;- Sub-administrator: view access to the list of members, write access to group graphs&#10;- Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i>:</p>
        <select id="group-createuseraddtogroup-role">
            <option value=1>Administrator</option>
            <option value=2>Sub-administrator</option>
            <!--<option value=3>Member</option>-->
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

<!-- EDIT USER -->
<div id="edit-user-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="edit-user-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="edit-user-modal-label">Edit user</h3>
    </div>
    <div class="modal-body">
        <table class="table">
            <tr><td>Username</td><td><input class="edit-user-username" type="text"></input></td></tr>
            <tr><td>Name</td><td><input class="edit-user-name" type="text"></input></td></tr>
            <tr><td>Email</td><td><input class="edit-user-email" type="text"></input></td></tr>
            <tr><td>Location</td><td><input class="edit-user-location" type="text"></input></td></tr>
            <tr><td>Bio</td><td><input class="edit-user-bio" type="text"></input></td></tr>
            <tr><td>Timezome</td><td><input class="edit-user-timezone" type="text"></input></td></tr>
            <tr><td>Password    <i class="icon-question-sign" title="Leave it blank if you don not wish to change it" /></td><td><input class="edit-user-password" type="password"></input></td></tr>
            <tr><td>Confirm password</td><td><input class="edit-user-confirm-password" type="password"></input></td></tr>
            <tr><td>Role in group   <i title="- Administrator: full access (create users, add member, create group feeds, dashboards graphs, etc)&#10;- Sub-administrator: view access to the list of members, write access to group graphs&#10;- Passive member: no access to group. The aim of the user is to be managed by the group administrator" class=" icon-question-sign"></i>:</td>
                <td><select id="edit-user-role">
                        <option value=1>Administrator</option>
                        <option value=2>Sub-administrator</option>
                        <!--<option value=3>Member</option>-->
                        <option value=0 selected>Passive member</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tags</td>
                <td><div id="edit-user-tags-wrapper">
                        Name  <input class="edit-user-tag-name" type="text" style="width:75px;margin-right:15px"></input><div id="edit-user-matching-tags" class="hide"></div> <button class="btn edit-user-tag-add" style="margin-bottom:10px"><i class="icon-plus"></i>Add</button><br />
                        Value  <input class="edit-user-tag-value" type="text"  style="width:165px"></input>
                        <div id="edit-user-tagslist"></div>
                    </div>
                </td>
            </tr>
        </table>
        <div id="edit-user-modal-message" class="hide"><div class="alert alert-error">Passwords do not match</div></div>
        <div id="edit-user-modal-message-tag" class="hide alert alert-error"></div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="edit-user-action" action="next" class="btn btn-primary">Next</button>
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
        <h3 id="feedExportModalLabel"><b><span id="SelectedExport"></span></b></h3>
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

<!-- CREATE TASK -->
<div id="taskCreateModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="taskCreateModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="taskCreateModalLabel"><?php echo _('Create task'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo _('Once a task is created you will need to set up the Process List and enable it'); ?></p>
        <table>
            <tr>
                <td style="width:125px"><?php echo _('Task belongs to'); ?></td>
                <td id="task-create-belongs-to">
                    <input name="belongs-to" type="radio" value="session-user" /><?php echo _('Current user') ?>
                    <br /><input name="belongs-to" type="radio" value="feed-user" checked /><?php echo _("Feed owner") ?>
                </td></tr>
            <tr><td><?php echo _('Name*'); ?></td><td><input id="task-create-name" type="text" /></td></tr>
            <tr><td><?php echo _('Description'); ?></td><td><input id="task-create-description" type="text" /></td></tr>
            <tr><td><?php echo _('Tag'); ?></td><td><input id="task-create-tag" type="text" /></td></tr>
            <tr><td><?php echo _('Frequency'); ?></td><td id="task-create-frequency"></td></tr>
            <tr><td><?php echo _('Start date'); ?></td><td><div class="input-append date" id="task-create-run-on" data-format="dd/MM/yyyy hh:mm"><input data-format="dd/MM/yyyy hh:mm" type="text" /><span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div></td></tr>
        </table>
        <div id="task-create-message" class="alert alert-block hide"></div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
        <button id="taskCreate-confirm" class="btn btn-primary"><?php echo _('Create task'); ?></button>
    </div>
</div>

<!-- DELETE TASK -->
<div id="delete-task-modal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="delete-task-modal-label" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="delete-task-modal-label">Delete task</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you wish to delete this task?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button id="delete-task-action" class="btn btn-danger">Delete</button>
    </div>
</div>

<?php if ($task_support === true) require "Modules/process/Views/process_ui.php"; ?>

<!-------------------------------------------------------------------------------------------
JAVASCRIPT
-------------------------------------------------------------------------------------------->
<script>
    var path = "<?php echo $path; ?>";
    var my_userid = <?php echo $session["userid"]; ?>;
    sidebar_resize();
    var selected_groupid = 0;
    var selected_groupindex = 0;
    var grouplist = [];
    var my_role = 0;
    var userlist = [];
    var tags_used_in_group = [];
    var summary_for_search = [];
    var task_support = <?php echo $task_support === false ? 0 : 1; ?> === 1 ? true : false;

    // ----------------------------------------------------------------------------------------
    // Task: ini
    // ---------------------------------------------------------------------e-------------------
    if (task_support == true) {
        load_custom_table_fields();
        processlist_ui.init(2); // 2 means that contexttype is task (1 for feeds and virtual feeds, 1 for input)
    }

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
        setTimeout(function () { // We need some extra time to let processlist_ui.init(1) to finish
            for (var gindex in grouplist) {
                if (grouplist[gindex].name == selected_group) {
                    $(".group[gindex=" + gindex + "]").addClass('activated');
                    draw_group(gindex);
                }
            }
        }, 100);
    } else {
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
        } else {
            // Sort userlist
            userlist.sort(function (a, b) {
                var nameA = a.username.toLowerCase(), nameB = b.username.toLowerCase();
                if (nameA < nameB) //sort string ascending
                    return -1;
                if (nameA > nameB)
                    return 1;
                return 0; //default return value (no sorting)
            });
            // Fill tags_used_in_group
            for (var z in userlist)
                for (var tag in userlist[z].tags)
                    if (!tags_used_in_group.includes(tag))
                        tags_used_in_group.push(tag);
            // Replace feeds available for processes - processlist_ui.init(1) has loaded current user's ones, we are goin to add feeds of all users in the group
            if (task_support == true)
                processlist_load_group_users_feeds();
        }

        // Html
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
                        /*case 3:
                         role = 'Member';
                         break;*/
                    default:
                        role = "Invalid role";
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

                // html user
                out += "<div class='user' uid='" + userlist[z].userid + "'>";
                out += "<div class='user-info'>";
                if (userlist[z].admin_rights != 'full' || my_role != 1)
                    out += "<div class='user-name'>" + userlist[z].username + "</div>";
                else
                    out += "<div class='user-name'><a class='setuser' href='" + path + "group/setuser?groupid=" + selected_groupid + "&userid=" + userlist[z].userid + "' username='" + userlist[z].username + "'>" + userlist[z].username + "</a></div>";
                out += "<div class='user-active-feeds'><b><span style='color:" + color + "'>" + userlist[z].activefeeds + "</span>/" + userlist[z].totalfeeds + "</b></div> <div class='user-role'>" + role + "</div>";
                out += "<div class='user-actions'>";
                if (userlist[z].userid != my_userid) {
                    if (userlist[z].admin_rights == 'full')
                        out += "<i class='edit-user icon-edit if-admin'  style='cursor:pointer' title='Edit user' uid=" + userlist[z].userid + " uindex=" + z + "> </i>";
                    out += "<i class='removeuser icon-trash if-admin' style='cursor:pointer' title='Remove user from group' uid=" + userlist[z].userid + " admin-rights=" + userlist[z].admin_rights + "> </i>";
                }
                out += "</div>"; // user-actions
                out += "</div>"; // user-info

                // html feeds and tasks
                out += "<div class='user-feeds-inputs hide' uid='" + userlist[z].userid + "'>";
                // Feeds                
                out += "<div class='user-feedslist'>";
                out += "<div class='user-feedslist-inner'>";
                // Add tags
                var tags_list = [];
                userlist[z].feedslist.forEach(function (feed) {
                    if (tags_list.indexOf(feed.tag) == -1) {
                        tags_list.push(feed.tag);
                        out += "<div class='feed-tag' tag='" + feed.tag + "'>";
                        out += "<input class='feed-tag-checkbox' type='checkbox' tag='" + feed.tag + "' uid='" + userlist[z].userid + "' />" + feed.tag;
                        // Add feed tah have the current tag
                        userlist[z].feedslist.forEach(function (feed_again) {
                            if (feed_again.tag == feed.tag) {
                                out += "<div class='feed hide' tag='" + feed_again.tag + "' uid='" + userlist[z].userid + "'>";
                                out += "<input type='checkbox' fid='" + feed_again.id + "' tag='" + feed_again.tag + "' uid='" + userlist[z].userid + "' />";
                                out += "<div class='feed-name'>" + feed_again.name + "</div>";
                                out += "<div class='feed-download' fid='" + feed_again.id + "' tag='" + feed_again.tag + "' name='" + feed_again.name + "'><i class='icon-download'style='cursor:pointer' title='Download csv'> </i></div>";
                                out += "<div class='feed-value'>" + list_format_value(feed_again.value) + "</div>";
                                out += "<div class='feed-time'>" + list_format_updated(feed_again.time) + "</div>";
                                out += "</div>"; // feed
                            }
                        });
                        out += "</div>";
                    }
                });
                out += "</div>"; // user-feedslist-inner
                out += "</div>"; // user-feedslist

                // Tasks div
                if (task_support === true && userlist[z].taskslist.length > 0) {
                    out += "<div class='user-tasks' groupid='" + groupid + "' userid='" + userlist[z].userid + "'>";
                    out += "<div class='user-taskslist-inner'>";
                    // Add tags
                    var tags_list = [];
                    table.data = userlist[z].taskslist; // we use it to draw some task fields
                    userlist[z].taskslist.forEach(function (task) {
                        if (tags_list.indexOf(task.tag) == -1) {
                            tags_list.push(task.tag);
                            out += "<div class='task-tag' tag='" + task.tag + "'>";
                            out += "Tasks - " + (task.tag === '' ? 'NoGroup' : task.tag);
                            // Add tasks tah have the current tag
                            userlist[z].taskslist.forEach(function (task_again, row) {
                                if (task_again.tag == task.tag) {
                                    out += "<div class='task hide' tag='" + task.tag + "' uid='" + userlist[z].userid + "' taskid='" + task_again.id + "'>";
                                    out += "<div class='task-name' title='Name'>" + task_again.name + "</div>";
                                    out += "<div class='task-processlist' title='Process list'>" + table.fieldtypes.processlist.draw(table, row, '', 'processList') + "</div>";
                                    out += "<div class='task-frequency' title='Frequency'>" + table.fieldtypes.frequency.draw(table, row, '', 'frequency') + "</div>";
                                    out += "<div class='task-enabled' style='cursor:pointer' title='Enabled' uindex=" + z + " taskid=" + task_again.id + ">" + (task_again.enabled == 1 ? 'On' : 'Off') + "</div>";
                                    if (userlist[z].admin_rights == 'full') {
                                        out += '<div id="task-actions">';
                                        out += "<div class='task-delete' title='Delete task' uid=" + userlist[z].userid + " taskid=" + task_again.id + "><i class='icon-trash if-admin' style='cursor:pointer'> </i></div> ";
                                        out += "<div class='task-view' title=\"Edit task in user's account\"><a class='setuser' href='" + path + "group/setuser?groupid=" + selected_groupid + "&userid=" + userlist[z].userid + "&view=tasks&tag=" + (task.tag === '' ? 'NoGroup' : task.tag) + "' username='" + userlist[z].username + "'><i class='icon-eye-open' /></a></div>";
                                        out += "<div class='task-edit-processlist' title='Edit process list' uindex=" + z + " taskid=" + task_again.id + " ><i style='cursor:pointer' class='icon-wrench' /></div>";
                                        out += "</div>"; // task-actions                             
                                    }
                                    out += "</div>"; // task
                                }
                            });
                            out += "</div>"; // task-tag
                        }
                    });
                    out += "</div>"; // user-taskslist-inner                    
                    out += "</div>"; // .user-tasks
                }

                // Close divs
                out += "</div>"; // user-feeds-inputs
                out += "</div>"; // user
            }
            $("#userlist-div").html(out); // Place userlist html in userlist table 

            // Show
            $('#userlist-div').show();

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
        var servertime = (new Date()).getTime(); // - table.timeServerLocalOffset;
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

    // Loads into processlist_ui.feedlist array feeds from all the users in the groups
    function processlist_load_group_users_feeds() {
        processlist_ui.feedlist = {};
        userlist.forEach(function (user) {
            user.feedslist.forEach(function (feed) {
                processlist_ui.feedlist[feed.id] = JSON.parse(JSON.stringify(feed));
                processlist_ui.feedlist[feed.id].name = user.username + ": " + feed.name;
            });
        });
    }

// ----------------------------------------------------------------------------------------
// Action: click on group
// ----------------------------------------------------------------------------------------
    $("body").on("click", "#grouplist .group", function () {
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
    $("body").on('click', '#groupcreate', function () {
        $('#group-create-modal input').val('');
        $('#group-create-modal').modal('show');
    });
    $("body").on('click', "#group-create-action", function () {
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
    }
    );
// ----------------------------------------------------------------------------------------
// Action: Edit group
// ----------------------------------------------------------------------------------------
    $("body").on('click', "#editgroup", function () {
        $("#edit-group-name").val(grouplist[selected_groupindex].name);
        $("#edit-group-description").val(grouplist[selected_groupindex].description);
        $("#edit-group-organization").val(grouplist[selected_groupindex].organization);
        $("#edit-group-area").val(grouplist[selected_groupindex].area);
        $("#edit-group-visibility").val(grouplist[selected_groupindex].visibility);
        $("#edit-group-access").val(grouplist[selected_groupindex].access);
        $('#edit-group-modal').modal('show');
    });
    $("body").on('click', '#edit-group-action', function () {
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
    $("body").on('click', "#addmember", function () {
        $('#group-addmember-modal input').val('');
        $("#group-addmember-access").val(0);
        $('#group-addmember-modal').modal('show');
    });
    $("body").on('click', "#group-addmember-action", function () {
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
    $("body").on('click', "#createuseraddtogroup", function () {
        $('#group-createuseraddtogroup-modal input').val('');
        $("#group-createuseraddtogroup-role").val(0);
        $('#group-createuseraddtogroup-modal').modal('show');
    });
    $("body").on('click', "#group-createuseraddtogroup-action", function () {
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
        $('.user-feeds-inputs[uid="' + userid + '"]').toggle();
    });
    $('body').on('click', '.feed-tag', function (e) {
        e.stopPropagation();
        var tag = $(this).attr('tag');
        $(this).find('.feed[tag="' + tag + '"]').toggle();
    });
    $('body').on('click', '.feed-tag-checkbox', function (e) {
        e.stopPropagation();
        var tag = $(this).attr('tag');
        var uid = $(this).attr('uid');
        if ($(this).is(':checked')) {
            $('.feed[tag="' + tag + '"][uid="' + uid + '"] input').prop('checked', 'checked');
        } else
            $('.feed[tag="' + tag + '"][uid="' + uid + '"] input').prop('checked', '');
    });
    $('body').on('click', '.feed', function (e) {
        e.stopPropagation();
    });
    // ----------------------------------------------------------------------------------------
// Action: Show tasks of a user
// ----------------------------------------------------------------------------------------

    $('body').on('click', '.task-tag', function (e) {
        e.stopPropagation();
        var tag = $(this).attr('tag');
        $(this).find('.feed[tag="' + tag + '"]').toggle();
    });
    $('body').on('click', '.task', function (e) {
        e.stopPropagation();
    });
// ----------------------------------------------------------------------------------------
// Action: Remove user
// ----------------------------------------------------------------------------------------
    $("body").on('click', ".removeuser", function (e) {
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
        } else {
            $('[name="removeuser-whattodo"][value="delete"]').attr('disabled', false);
        }

        $('#remove-user-modal').modal('show');
    });
    $("body").on('click', "#remove-user-action", function () {
        var action = $(this).attr('action');
        if (action == 'next') {
            $('#remove-user-modal-step-1').hide();
            var what_to_do = $('input[name="removeuser-whattodo"]:checked').val();
            if (what_to_do == 'remove-from-group') {
                $('#remove-user-modal-step-2').html('<p>Are you sure you want to remove this user from group?</p>');
                $(this).attr('action', 'remove-from-group');
            } else {
                $('#remove-user-modal-step-2').html('<p>Are you sure you wish to completely delete this user from the database?</p><p>All the data will be lost</p>');
                $(this).attr('action', 'delete-from-database');
            }
            $('#remove-user-modal-step-2').show();
            $('#remove-user-action').html('Done')
        } else if (action == "remove-from-group") {
            $('#remove-user-modal').modal('hide');
            var userid = $('#remove-user-modal').attr("uid");
            var result = group.removeuser(selected_groupid, userid);
            if (!result.success) {
                alert(result.message);
            } else {
                draw_userlist(selected_groupid);
            }
        } else if (action == "delete-from-database") {
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
// Action: Edit user
// ----------------------------------------------------------------------------------------
    $("body").on('click', ".edit-user", function (e) {
        e.stopPropagation();
        $('#edit-user-modal-message-tag').hide();
        $('#edit-user-modal-message').hide();
        $('#edit-user-matching-tags').hide();
        $('.edit-user-tag-name').val('');
        $('.edit-user-tag-value').val('');
        var userid = $(this).attr("uid");
        $('#edit-user-action').attr("uid", userid);
        var uindex = $(this).attr("uindex");
        $('.edit-user-username').val(userlist[uindex].username);
        $('.edit-user-name').val(userlist[uindex].name);
        $('.edit-user-email').val(userlist[uindex].email);
        $('.edit-user-location').val(userlist[uindex].location);
        $('.edit-user-bio').val(userlist[uindex].bio);
        $('.edit-user-timezone').val(userlist[uindex].timezone);
        $('.edit-user-password').val('');
        $('.edit-user-confirm-password').val('');
        $('#edit-user-role option[value="' + userlist[uindex].role + '"]').prop('selected', true);
        var html = '';
        for (var tag in userlist[uindex].tags) {
            var value = userlist[uindex].tags[tag];
            html += '<div name="' + tag + '" value="' + value + '" class="btn" style="cursor:default;margin-right:5px">' + tag + ': ' + value + '<span class="remove-tag" name="' + tag + '" style="margin-left:5px; cursor:pointer"><sup><b>X</b></sup></span></div>';
        }
        $('#edit-user-tagslist').html(html);
        $('#edit-user-modal').modal('show');
    });
    $("body").on('click', "#edit-user-action", function () {
        var userid = $('#edit-user-action').attr("uid");
        var username = $('.edit-user-username').val();
        var name = $('.edit-user-name').val();
        var email = $('.edit-user-email').val();
        var location = $('.edit-user-location').val();
        var bio = $('.edit-user-bio').val();
        var timezone = $('.edit-user-timezone').val();
        var role = $('#edit-user-role').val();
        var password = $('.edit-user-password').val();
        var password_confirmation = $('.edit-user-confirm-password').val();
        var tags = {};
        $('#edit-user-tagslist div').each(function () {
            tags[$(this).attr('name')] = $(this).attr('value');
        });
        tags = JSON.stringify(tags);
        var ready_to_save = true;
        if (password != '') {
            if (password != password_confirmation) {
                $('#edit-user-modal-message').show();
                ready_to_save = false;
            }
        }
        if (ready_to_save) {
            var result = group.setuserinfo(userid, selected_groupid, username, name, email, location, bio, timezone, role, password, tags);
            if (result.success != true)
                alert(result.message);
            $('#edit-user-modal').modal('hide');
            draw_userlist(selected_groupid);
            $('#edit-user-modal-message').hide();
        }
    });
    $("body").on('click', ".edit-user-tag-add", function () {
        $('#edit-user-modal-message-tag').hide();
        var name = $('.edit-user-tag-name').val();
        var value = $('.edit-user-tag-value').val();
        var html = '<div name="' + name + '" value="' + value + '" class="btn" style="cursor:default;margin-right:5px">' + name + ': ' + value + '<span class="remove-tag" name="' + name + '" style="margin-left:5px; cursor:pointer"><sup><b>X</b></sup></span></div>';
        var name_found = false;
        // Find out if tag is already used
        $('#edit-user-tagslist div').each(function () {
            if ($(this).attr('name') == name)
                name_found = true;
        });
        // Add tag
        if (name == '' || value == '')
            $('#edit-user-modal-message-tag').html('Name and value cannot be empty').show();
        else if (name_found == true)
            $('#edit-user-modal-message-tag').html('Tag already in use').show();
        else {
            $('#edit-user-tagslist').append(html);
            $('.edit-user-tag-name').val('');
            $('.edit-user-tag-value').val('');
        }
    });
    $("body").on('click', ".remove-tag", function () {
        var name = $(this).attr('name');
        $('#edit-user-tagslist div[name="' + name + '"]').remove();
    });
    $("body").on('keyup', ".edit-user-tag-name", function () {
        var typed = $(this).val();
        $('#edit-user-matching-tags').hide();
        var matching_tags = [];
        if (typed.length > 2) {
            for (var z in tags_used_in_group) {
                if (tags_used_in_group[z].indexOf(typed) != -1)
                    matching_tags.push(tags_used_in_group[z]);
            }
        }

        if (matching_tags.length > 0) {
            $('#edit-user-matching-tags').html("");
            for (var z in matching_tags)
                $('#edit-user-matching-tags').append('<p class="matched-tag" tag="' + matching_tags[z] + '" style="cursor:default">' + matching_tags[z] + '</p>')
            $('#edit-user-matching-tags').show();
        }
    });
    $("body").on('click', ".matched-tag", function () {
        $('.edit-user-tag-name').val($(this).attr('tag'));
        $('#edit-user-matching-tags').hide();
    });
// ----------------------------------------------------------------------------------------
// Action: Delete group
// ----------------------------------------------------------------------------------------
    $("body").on('click', "#deletegroup", function () {
        $('#delete-group-modal').modal('show');
    });
    $("body").on('click', "#delete-group-action", function () {
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
// Action: 
//      - Download feed (copied, but modified, from feedlist_view_classic.php)
//      - Download multiple feeds
// ----------------------------------------------------------------------------------------
    $('body').on('click', '.feed-download, .multiple-feed-download', function (e) {
        e.stopPropagation();
        if ($(this).attr('type') == 'multiple') {
            /*$("#export").attr('export-type', "group");
             var group = $(this).attr('group');
             $("#export").attr('group', group);
             var rows = $(this).attr('rows').split(",");
             var feedids = [];
             for (i in rows) {
             feedids.push(table.data[rows[i]].id);
             } // get feedids from rowids
             $("#export").attr('feedids', feedids);
             $("#export").attr('feedcount', rows.length);
             $("#SelectedExport").html(group + " tag (" + rows.length + " feeds)");
             calculate_download_size(rows.length);*/

            $("#export").attr('export-type', "group");
            // var group = $(this).attr('group');
            //$("#export").attr('group', group);
            var feedids = [];
            $('.feed input:checked').each(function () {
                feedids.push($(this).attr('fid'));
            });
            $("#export").attr('feedids', feedids);
            $("#export").attr('feedcount', feedids.length);
            $("#SelectedExport").html("Download " + feedids.length + " feeds");
            $("#export").attr('name', selected_group);
            calculate_download_size(feedids.length);
        } else {
            $("#export").attr('export-type', "feed");
            $("#export").attr('feedid', $(this).attr('fid'));
            var name = $(this).attr('tag') + ":" + $(this).attr('name');
            $("#export").attr('name', name);
            $("#SelectedExport").html(name);
            calculate_download_size(1);
        }
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
    $("body").on('change', '#export-interval, #export-timeformat', function (e)
    {
        $("#export-timezone-offset").prop("disabled", $("#export-timeformat").prop('checked'));
        if ($("#export").attr('export-type') == 'group') {
            var downloadsize = calculate_download_size($("#export").attr('feedcount'));
        } else {
            calculate_download_size(1);
        }
    });
    $("body").on('changeDate', '#datetimepicker1, #datetimepicker2', function (e)
    {
        if ($("#export").attr('export-type') == 'group') {
            var downloadsize = calculate_download_size($("#export").attr('feedcount'));
        } else {
            calculate_download_size(1);
        }
    });
    $("body").on('click', "#export", function ()
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
        if ($(this).attr('export-type') == 'group')
            var downloadsize = calculate_download_size($(this).attr('feedcount'));
        else
            var downloadsize = calculate_download_size(1);
        if (downloadsize > (downloadlimit * 1048576)) {
            var r = confirm("Estimated download file size is large.\nServer could take a long time or abort depending on stored data size.\Limit is " + downloadlimit + "MB.\n\nTry exporting anyway?");
            if (!r)
                return false;
        }

        $('#feedExportModal').modal('hide');
        if ($(this).attr('export-type') == 'group') {
            var result = group.csvexport(selected_groupid, $(this).attr('feedids'), export_start + export_timezone_offset, export_end + export_timezone_offset, export_interval, export_timeformat, $(this).attr('name'));
        } else {
            var result = group.csvexport(selected_groupid, $(this).attr('feedid'), export_start + export_timezone_offset, export_end + export_timezone_offset, export_interval, export_timeformat, $(this).attr('name'));
        }
    });
    function calculate_download_size(feedcount) {
        var export_start = parse_timepicker_time($("#export-start").val());
        var export_end = parse_timepicker_time($("#export-end").val());
        var export_interval = $("#export-interval").val();
        var export_timeformat_size = ($("#export-timeformat").prop('checked') ? 20 : 11); // bytes per timestamp
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
            time.push(0);
        return new Date(date[2], date[1] - 1, date[0], time[0], time[1], time[2], 0).getTime() / 1000;
    }

// ----------------------------------------------------------------------------------------
// Action: Show User actions buttons when feed check boxes are ticked
// ----------------------------------------------------------------------------------------
    $('body').on('click', '.feed-tag-checkbox, .feed input', function (e) {
        e.stopPropagation();
        var any_checked = false;
        $('.feed input').each(function () {
            if ($(this).is(':checked'))
                any_checked = true;
        })
        if (any_checked) {
            $('.multiple-feeds-actions button').show();
            if (task_support === false)
                $('button.create-task').hide();
        } else
            $('.multiple-feeds-actions button').hide();
        if (grouplist[selected_groupindex].role != 1)
            $('.if-admin').hide();
    });
// ----------------------------------------------------------------------------------------
// Action: Untick tag checbox when all the feed checkboxes are unticked
// ----------------------------------------------------------------------------------------
    $('body').on('click', '.feed input', function (e) {
        e.stopPropagation();
        var tag = $(this).attr('tag');
        var uid = $(this).attr('uid');
        var any_checked = false;
        var any_unchecked = false;
        $('.feed input[tag="' + tag + '"][uid="' + uid + '"]').each(function () {
            if ($(this).is(':checked'))
                any_checked = true;
            else
                any_unchecked = true;
        });
        if (any_checked == true && any_unchecked == false) // all checked
            $('.feed-tag-checkbox[tag="' + tag + '"][uid="' + uid + '"]').prop('checked', 'checked').prop("indeterminate", false);
        else if (any_checked == false && any_unchecked == true) // none checked
            $('.feed-tag-checkbox[tag="' + tag + '"][uid="' + uid + '"]').prop('checked', '').prop("indeterminate", false);
        else // some checked and some unchecked
            $('.feed-tag-checkbox[tag="' + tag + '"][uid="' + uid + '"]').prop("indeterminate", true);

    });
// ----------------------------------------------------------------------------------------
// Action: open graph page
// ----------------------------------------------------------------------------------------
    $("body").on('click', '.feed-graph', function (e) {
        var feeds = [];
        $('.feed input').each(function () {
            if ($(this).is(':checked'))
                feeds.push($(this).attr('fid'))
        });
        window.location = path + "graph/groupgraph/" + selected_groupid + ',' + feeds.join(",");
    });
// ----------------------------------------------------------------------------------------
// Action: search
// ----------------------------------------------------------------------------------------
    $("body").on('focus', '#search-box', function (e) {
        summary_for_search = group.extendedgrouplist();
    });
    $("body").on('keyup', '#search-box', function (e) {
        $('.search-list-groups-show').hide();
        $('.search-list-users-show').hide();
        $('.search-list-tags-show').hide();
        $('#search-list-groups').html('');
        $('#search-list-users').html('');
        $('#search-list-tags').html('');
        var typed = $('#search-box').val();
        if (typed.length > 2) {
            summary_for_search.forEach(function (group) {
                if (group.name.indexOf(typed) != -1) {
                    $('#search-list-groups').append('<p class="search-match" groupid="' + group.groupid + '">' + group.name + '</p>');
                    $('.search-list-groups-show').show();
                }
                group.users.forEach(function (user) {
                    if (user.username.indexOf(typed) != -1) {
                        $('#search-list-users').append('<p class="search-match" groupid="' + group.groupid + '" userid="' + user.userid + '">' + group.name + ': ' + user.username + '</p>');
                        $('.search-list-users-show').show();
                    }
                    for (var tag in user.tags) {
                        if (user.tags[tag].indexOf(typed) != -1) {
                            $('#search-list-tags').append('<p class="search-match" groupid="' + group.groupid + '" userid="' + user.userid + '" tag="' + tag + '">' + group.name + ': ' + user.username + ': ' + tag + ': ' + user.tags[tag] + '</p>');
                            $('.search-list-tags-show').show();
                        } else if (tag.indexOf(typed) != -1) {
                            $('#search-list-tags').append('<p class="search-match" groupid="' + group.groupid + '" userid="' + user.userid + '" tag="' + tag + '">' + group.name + ': ' + user.username + ': ' + tag + ': ' + user.tags[tag] + '</p>');
                            $('.search-list-tags-show').show();
                        }
                    }
                });
            });
        }
    });
    $("body").on('click', '.search-match', function (e) {
        var groupid = $(this).attr('groupid');
        var userid = $(this).attr('userid');
        var tag = $(this).attr('tag');
        $('.group[gid=' + groupid + ']').click();
        $('.user[uid=' + userid + ']').click();
    });
    $("body").on('focusout', '#search-box', function (e) {
        setTimeout(function () { // we delay the execution to allow the click on .search-match to happen
            $('.search-list-groups-show').hide();
            $('.search-list-users-show').hide();
            $('.search-list-tags-show').hide();
            $('#search-list-groups').html('');
            $('#search-list-users').html('');
            $('#search-list-tags').html('');
        }, 100);
    });
// ----------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------
// Other
// ----------------------------------------------------------------------------------------
    $("body").on('click', ".setuser", function (e) {
        e.stopPropagation();
        alert('You are now logged as ' + $(this).attr('username'));
    });
// ----------------------------------------------------------------------------------------
// Sidebar
// ----------------------------------------------------------------------------------------
    $("body").on('click', "#sidebar-open", function () {
        $(".sidebar").css("left", "250px");
        $("#sidebar-close").show();
    });
    $("body").on('click', "#sidebar-close", function () {
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

// ----------------------------------------------------------------------------------------
// Tasks
// ----------------------------------------------------------------------------------------
    $("body").on('click', ".task-tag", function (e) {
        e.stopPropagation();
        var tag = $(this).attr('tag');
        $(this).find('.task[tag="' + tag + '"]').toggle();
    });
    $("body").on('click', ".create-task", function (e) {
        // Frequency field
        $('#task-create-frequency').html(get_frequency_html({type: 'once_a_month'}));
        add_frequency_html_events();
        // Start date field
        $('#task-create-run-on').datetimepicker({language: 'en-EN', useCurrent: true, weekStart: 1});
        var now = new Date();
        var today = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), now.getMinutes());
        var picker = $('#task-create-run-on').data('datetimepicker');
        picker.setLocalDate(today);
        // Reset fields        
        $('#task-create-message').hide();
        $('#task-create-name').val('');
        $('#task-create-description').val('');
        $('#task-create-tag').val('');
        $('#task-create-frequency [value="once_a_month"]').click();
        $('#taskCreateModal').modal('show');

        // Get all the checked feeds and store them into the ProcessList modal
        var feedids = [];
        $('.feed input[type=checkbox]:checked').each(function () {
            feedids.push($(this).attr('fid'));
        });
        $('#processlistModal').attr('feedids', JSON.stringify(feedids));
    });
    $("body").on('click', ".task-delete", function (e) {
        $('#delete-task-modal').attr('uid', $(this).attr('uid'));
        $('#delete-task-modal').attr('taskid', $(this).attr('taskid'));
        $('#delete-task-modal').modal('show');
    });
    $("body").on('click', "#delete-task-action", function (e) {
        var uid = $('#delete-task-modal').attr('uid');
        var taskid = $('#delete-task-modal').attr('taskid');

        var result = group.deleteTask(taskid, uid, selected_groupid);
        if (result.success) {
            //draw_userlist(selected_groupid);
            userlist = group.userlist(selected_groupid);
            if ($('.task[taskid=' + taskid + ']').siblings().length > 0)
                $('.task[taskid=' + taskid + ']').remove();
            else
                $('.task[taskid=' + taskid + ']').parents('.task-tag').remove();
            $("#delete-task-modal").modal('hide');
        } else {
            alert('There have been some problems deleting the task:\n' + result.message.replace(/\\n/g, '\n'));
        }
    });
    $("body").on('click', ".task-edit-processlist", function (e) {
        e.stopPropagation();
        var taskid = $(this).attr('taskid');
        var user = userlist[$(this).attr('uindex')];
        var task = user.taskslist.find(function (task_obj) {
            return task_obj.id === taskid;
        });
        processlist_ui.load(taskid, processlist_ui.decode(task.processList), '', null, null); // show processlist modal
        $("#processlistModal #save-processlist").attr('action', 'edit');
        $("#processlistModal #save-processlist").attr('taskid', taskid);
        $("#processlistModal #save-processlist").attr('uid', user.userid);
        $("#processlistModal #save-processlist").html('Ok');
        $("#process-select").val('task__feed_last_update_greater'); //Set default process to add
        $("#process-select").change();
    });
    $("body").on('click', ".task-enabled", function (e) {
        e.stopPropagation();
        var taskid = $(this).attr('taskid');
        var userindex = $(this).attr('uindex');
        var uid = userlist[userindex].userid;
        var task = userlist[userindex].taskslist.find(function (task) {
            return task.id == taskid
        });
        var new_value = task.enabled == '1' ? 0 : 1;
        var result = group.setTaskEnabled(new_value, taskid, uid, selected_groupid);
        if (result.success != undefined && result.success == false)
            alert(result.message);
        else {
            userlist = group.userlist(selected_groupid);
            task = userlist[userindex].taskslist.find(function (task) {
                return task.id == taskid
            });
            $(this).html(task.enabled == 1 ? 'On' : 'Off');
        }
    });
    $('#taskCreate-confirm').on('click', function () {
        $('#task-create-message').hide();
        var name = $('#task-create-name').val();
        if ($('#task-create-name').val() == '')
            $('#task-create-message').html('<p>Name cannot be empty</p>').show();
        else {
            // Prepare process list
            var processlist = new Array();
            processlist[0] = new Array('group__source_multifeed', $('#processlistModal').attr('feedids').replace(/["\[\]]/gi, '').replace(/,/gi, '-'));
            // Get other task fields    
            var belongs_to = $('#task-create-belongs-to input[name=belongs-to]:checked').val();
            var description = $('#task-create-description').val();
            var tag = $('#task-create-tag').val();
            var frequency = get_frequency_field('#task-create-frequency');
            var run_on = parse_timepicker_time($('#task-create-run-on input').val());
            $('#processlistModal').attr('belongs-to', belongs_to);
            $('#processlistModal').attr('name', name);
            $('#processlistModal').attr('description', description);
            $('#processlistModal').attr('tag', tag);
            $('#processlistModal').attr('frequency', frequency);
            $('#processlistModal').attr('run_on', run_on);
            // Show hide modals
            $('#taskCreateModal').modal('hide');
            $('#processlistModal').hide();
            processlist_ui.load(0, processlist, 'Multi feed task -', null, null); // show processlist modal      
            //Set default process to addtask__feed_last_update_higher
            $("#process-select").val('task__feed_last_update_greater');
            $("#process-select").change();
            $("#processlistModal #save-processlist").attr('action', 'create');
            // Remove actions from the first proccess in the processlist (Source multifeed) as we dont' want the user to be able toedit/remove it
            $('.edit-process[processid=0]').hide();
            $('.delete-process[processid=0]').hide();
            // Change the html of the buttons
            $('#processlistModal #close').html('Cancel');
            $('#processlistModal #save-processlist').html('Ok');
        }
    });
    $("#processlistModal").on('click', '#save-processlist', function () {
        if ($(this).attr('action') == 'create') { // We are creating task from the feeds ticked
            var feedids = JSON.parse($('#processlistModal').attr('feedids'));
            var processlist = processlist_ui.encode(processlist_ui.contextprocesslist);
            processlist = processlist.substring(processlist.indexOf(",") + 1); // We remove the first process (source multi-feed) as we are already sending the list of feedids in another variable, more convenient this way
            var name = $('#processlistModal').attr('name');
            var description = $('#processlistModal').attr('description');
            var tag = $('#processlistModal').attr('tag');
            var frequency = $('#processlistModal').attr('frequency');
            var run_on = $('#processlistModal').attr('run_on');
            var belongs_to = $('#processlistModal').attr('belongs-to');

            var result = group.setMultiFeedProcessList(feedids, processlist, selected_groupid, name, description, tag, frequency, run_on, belongs_to);
            if (result.success) {
                draw_userlist(selected_groupid);
                $("#processlistModal").modal('hide');
            } else {
                alert('There have been some errors saving the process lists:\n' + result.message.replace(/\\n/g, '\n'));
            }
        } else { // we are editing the processlist of an existing task
            var taskid = $(this).attr('taskid');
            var uid = $(this).attr('uid');
            var processlist = processlist_ui.encode(processlist_ui.contextprocesslist);
            var result = group.setProcessList(taskid, uid, selected_groupid, processlist);
            if (result.success != undefined && result.success == false)
                alert(result.message);
            else {
                userlist = group.userlist(selected_groupid);
                var user = userlist.find(function (user) {
                    return user.userid == uid;
                });
                var task = user.taskslist.find(function (task, index) {
                    return task.id == taskid;
                });
                var row = user.taskslist.findIndex(function (task) {
                    return task.id == taskid;
                });
                table.data = user.taskslist; // we use it to draw some task fields
                $('.task[taskid=' + taskid + '] .task-processlist').html(table.fieldtypes.processlist.draw(table, row, '', 'processList'));

                $("#processlistModal").modal('hide');
            }
        }
    });
    $("#processlistModal").on('click', '#process-add', function () {
        // The addintion of a new process to the list redraws the table adding the edit and remove buttons to the "source multifeed" process> we removed them as we don't want the user to edit/remove that process
        $('.edit-process[processid=0]').hide();
        $('.delete-process[processid=0]').hide();
        // And also qwe make the "Changed press to save" button look like OK
        $('#processlistModal #save-processlist').html('Ok').removeClass('btn-warning').addClass('btn-success');
    });

</script>
