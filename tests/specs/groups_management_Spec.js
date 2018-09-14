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
    });

    afterAll(function () {
        helper.logIfDebug('\nAfter all\n------------------');
        helper.logout();
    });

    it('can go to the Groups page', function () {
        helper.logIfDebug('\nSpecification: A Group user can go to the Groups page\n---------------------');
        helper.goToGroupsPage()
        expect(browser.getTitle()).toBe('Emoncms - group');
    });

    it('can create a group', function () {
        helper.logIfDebug('\nSpecification: A Group user can create a group\n---------------------');
        helper.createGroup(group_name, group_description);
        expect(browser.isExisting('.group=' + group_name)).toBe(true);
    });

    it('can browse a group', function () {
        helper.logIfDebug('\nSpecification: A Group user can browse a group\n---------------------');
        helper.goToGroup(group_name);
        expect(browser.getText('#groupname')).toBe(group_name);
        expect(browser.getText('#groupdescription')).toBe(group_description);
    });

    it('is a member with role Administrator of the group just created', function () {
        helper.logIfDebug('\nSpecification: A Group user is a member with role Administrator of the group just created\n---------------------');
        expect(browser.getText('.user-info .user-name')).toBe(login_details.username);
        expect(browser.getText('.user-info .user-role')).toBe('Administrator');
    });

    it('can delete the group', function () {
        helper.logIfDebug('\nSpecification: A Group user can delete the group\n---------------------');
        helper.deleteGroup(group_name);
        expect(browser.isExisting('.group=' + group_name)).toBe(false);
    });

    it('has deleted a group and the list of users disappears from the UI', function () {
        helper.logIfDebug('\nSpecification: A Group user has deleted a group and the list of users disappears from the UI\n---------------------');
        expect(browser.isExisting('.user-info')).toBe(false);
    });

});

