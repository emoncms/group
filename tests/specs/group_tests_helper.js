/************************************************
 * This helper uses login detailsfrom the file '../login_details.js'
 * This files looks like: 
 *      module.exports = {
 *          login_url: 'http://your_emonCMS_installation',
 *          username: 'an_existing_user',
 *          password: 'the_password'
 *      };
 *   
 * **********************************************/

let login_details = require('../login_details');
let debug = process.env.DEBUG;

module.exports = {
    loginDefaultUser: function () {
        login(login_details.login_url, login_details.username, login_details.password);
    },
    goToGroupsPage: function () {
        this.logIfDebug('Going to Groups page');
        browser.click('a=Setup')
                .click('a=Groups');
    },
    goToMyAccountPage: function () {
        this.logIfDebug('Going to My Account page');
        browser.click('a=Setup')
                .click('a=My Account');
    },
    createGroup: function (group_name, description) {
        this.logIfDebug('Creating a group: ' + group_name);
        browser.click('#groupcreate')
                .setValue('#group-create-name', group_name);
        if (description != undefined)
            browser.setValue('#group-create-description', description);
        browser.click('#group-create-action')
                .click('.group=' + group_name);
    },
    goToGroup: function (group_name) {
        this.logIfDebug('Going to group: ' + group_name);
        browser.click('.group=' + group_name);
    },
    createUserAddToGroup: function (username, password, role) {
        this.logIfDebug('Creating user and adding it to group: ' + username + ' - ' + password + ' - ' + role);
        browser.click('#createuseraddtogroup')
                .setValue('#group-createuseraddtogroup-name', username)
                .setValue('#group-createuseraddtogroup-email', username + '@tururu.com')
                .setValue('#group-createuseraddtogroup-password', password)
                .setValue('#group-createuseraddtogroup-password-confirm', password)
                .setValue('#group-createuseraddtogroup-username', username)
                .selectByVisibleText('#group-createuseraddtogroup-role', role)
                .click('#group-createuseraddtogroup-action');
    },
    addExistingUserToGroup: function (username, password, role) {
        this.logIfDebug('Adding a existing user to group: ' + username + ' - ' + password + ' - ' + role);
        browser.click('#addmember')
                .setValue('#group-addmember-username', username)
                .setValue('#group-addmember-password', password)
                .selectByVisibleText('#group-addmember-access', role)
                .click('#group-addmember-action');
    },
    logout: function () {
        this.logIfDebug('Logging out');
        browser.click('a*=Logout');
    },
    removeUserFromGroup: function (username, group_name) {
        this.goToGroup(group_name);
        this.logIfDebug('Removing a user from group ' + group_name);
        let users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == username) {
                users_divs[i].$('[title="Remove user"]').click();
                break;
            }
        }
        browser.click('[for="removeuser-from-group"')
                .click('#remove-user-action')
                .click('#remove-user-action');
    },
    removeUserFromGroupAndSystem: function (username, group_name) {
        this.goToGroup(group_name);
        this.logIfDebug('Removing a user from group and system, group:' + group_name + ' user: ' + username);
        let users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == username) {
                users_divs[i].$('[title="Remove user"]').click();
                break;
            }
        }
        browser.click('[for="removeuser-delete"')
                .click('#remove-user-action')
                .click('#remove-user-action');
    },
    fullyRemoveAllUsersFromGroup: function (group_name) {
        this.goToGroup(group_name);
        this.logIfDebug('Removing all users from group ' + group_name);
        for (let i = 0; i < 2; i++) {
            browser.click('[title="Remove user"]')
                    .click('[for="removeuser-from-group"')
                    .click('#remove-user-action')
                    .click('#remove-user-action');
        }
    },
    deleteGroup: function (group_name) {
        this.goToGroup(group_name);
        this.logIfDebug('Deleting group ' + group_name);
        browser.click('#deletegroup')
                .click('#delete-group-action');
    },
    impersonateUser: function (username) {
        this.logIfDebug('Impersonating user ' + username);
        let users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == username) {
                users_divs[i].$('.setuser').click();
                break;
            }
        }
    },
    logAsPreviousUser: function () {
        this.logIfDebug('Logging as previous user');
        browser.click('[href*=logasprevioususer]');
    },
    logIfDebug(message) {
        if (debug)
            console.log(message);
    }
};

function login(url, username, password) {
    if (debug)
        console.log('Logging: ' + url + ' - ' + username + ' - ' + password);
    browser.url(url)
            .setValue('[name=username]', username)
            .setValue('[name=password]', password)
            .click('#login');
}