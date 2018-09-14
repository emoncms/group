/************************************************
 * This test will login in an emonCMS installation.
 * It looks for the login details in the file '../login_details.js'
 * This files looks like: 
 *      module.exports = {
 *          login_url: 'http://your_emonCMS_installation',
 *          username: 'an_existing_user',
 *          password: 'the_password'
 *      };
 *   
 * **********************************************/

let login_details = require('../login_details');
let helper = require('./group_tests_helper.js');

describe('A Group user', function () {

    let group_name = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let user_to_add_1 = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let user_to_add_2 = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);

    beforeAll(function () {
        helper.logIfDebug('\nBefore all\n------------------');
        helper.loginDefaultUser();
        helper.goToGroupsPage();
        helper.createGroup(group_name);
        helper.createUserAddToGroup(user_to_add_1, 'a password', 'Administrator');
        helper.createUserAddToGroup(user_to_add_2, 'another password', 'Administrator');
    });

    afterAll(function () {
        helper.logIfDebug('\nAfter all\n------------------');
        helper.logout();
        helper.loginDefaultUser();
        helper.goToGroupsPage();
        helper.goToGroup(group_name);
        helper.fullyRemoveAllUsersFromGroup(group_name);
        helper.deleteGroup(group_name);
        helper.logout();
    });
    it('can impersonate another user because is an Admnistrator', function () {
        helper.logIfDebug('\nStarting specficication: can impersonate another user because is an Admnistrator\n------------------');
        helper.goToGroupsPage();
        helper.goToGroup(group_name);
        helper.impersonateUser(user_to_add_1);
        expect(browser.alertText()).toBe('You are now logged as ' + user_to_add_1);
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add_1)).toBe(true);
    });
    it('can log back', function () {
        helper.logIfDebug('\nStarting specficication: can log back\n------------------');
        helper.logAsPreviousUser();
        expect(browser.isExisting('a*=logasprevioususer')).toBe(false);
        expect(browser.isExisting('.group*=' + group_name)).toBe(true);
        helper.goToMyAccountPage();
        expect(browser.getText('.username')).toBe(login_details.username);
    });
    it('can impersonate 2 users but we don\'t loose track of the original', function () {
        helper.logIfDebug('\nStarting specficication: can impersonate 2 users but we don\'t loose track of the original\n------------------');
        helper.goToGroupsPage();
        helper.goToGroup(group_name);
        helper.impersonateUser(user_to_add_1);
        browser.alertAccept();
        helper.goToGroupsPage();
        helper.goToGroup(group_name);
        helper.impersonateUser(user_to_add_2);
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add_2)).toBe(true);
        helper.logAsPreviousUser();
        helper.goToMyAccountPage();
        expect(browser.getText('.username')).toBe(login_details.username);
    });
})
        ;

