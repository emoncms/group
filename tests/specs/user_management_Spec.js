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
    let group_description = "The description of the group";
    let user_to_add = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let user_to_add_password = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);

    beforeAll(function () {
        helper.logIfDebug('\nBefore all\n------------------');
        helper.loginDefaultUser();
        helper.goToGroupsPage();
        helper.createGroup(group_name);
        helper.goToGroup(group_name);
    });

    afterAll(function () {
        helper.logIfDebug('\nAfter all\n------------------');
        helper.goToGroupsPage();
        helper.goToGroup(group_name);
        helper.deleteGroup(group_name);
        helper.logout();
    });

    it('can create a user and add it to the group', function () {
        helper.logIfDebug('\nSpecification: A Group user can create a user and add it to the group\n---------------------');
        helper.createUserAddToGroup(user_to_add, user_to_add_password, 'Passive member');
        let created_user_name = $$('.user-info .user-name')[1];
        expect(created_user_name.getText()).toBe(user_to_add);
        let created_user_role = $$('.user-info .user-role')[1];
        expect(created_user_role.getText()).toBe('Passive member');
    });

    it('can impersonate the new user because is an Admnistrator', function () {
        helper.logIfDebug('\nSpecification: A Group user can impersonate another user because is an Admnistrator\n---------------------');
        helper.impersonateUser(user_to_add);
        expect(browser.alertText()).toBe('You are now logged as ' + user_to_add);
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add)).toBe(true);
    });

    it('can go to the Groups page to ensure the new user cannot see any group because is a Passive member', function () {
        helper.logIfDebug('\nSpecification: A Group user can go to the Groups page to ensure the new user cannot see any group because is a Passive member\n---------------------');
        helper.goToGroupsPage()
        expect(browser.isExisting('.group')).toBe(false);
    });

    it('can log back', function () {
        helper.logIfDebug('\nSpecification: A Group user can log back\n---------------------');
        helper.logAsPreviousUser();
        expect(browser.isExisting('a*=logasprevioususer')).toBe(false);
        expect(browser.isExisting('.group*=' + group_name)).toBe(true);
    });

    it('can remove a user from the group but keep it in the system', function () {
        helper.logIfDebug('\nSpecification: A Group user can remove a user from the group but keep it in the system\n---------------------');
        helper.removeUserFromGroup(user_to_add, group_name);
        expect(browser.isExisting('.user-name=' + user_to_add)).toBe(false);
    });

    it('can add a member with password', function () {
        helper.logIfDebug('\nSpecification: A Group user can add a member with password\n---------------------');
        helper.goToGroup(group_name);
        helper.addExistingUserToGroup(user_to_add, user_to_add_password, 'Sub-administrator');
        let created_user_name = $$('.user-info .user-name')[1];
        expect(created_user_name.getText()).toBe(user_to_add);
        let created_user_role = $$('.user-info .user-role')[1];
        expect(created_user_role.getText()).toBe('Sub-administrator');
    });

    it('can completely remove a user from the system', function () {
        helper.logIfDebug('\nSpecification: A Group user can completely remove a user from the system\n---------------------');
        helper.removeUserFromGroupAndSystem(user_to_add, group_name);
        expect(browser.isExisting('.user-name=' + user_to_add)).toBe(false);
        helper.addExistingUserToGroup(user_to_add, user_to_add_password, 'Sub-administrator')
        expect(browser.alertText()).toBe('Incorrect authentication');
        browser.alertAccept();
        browser.click('#group-addmember-modal .btn[data-dismiss="modal"]');
    });

});

