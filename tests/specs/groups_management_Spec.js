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

describe('A Group user', function () {

    let group_name = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let group_description = "The description of the group";
    let user_to_add = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let user_to_add_password = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);

    beforeAll(function () {
        browser.url(login_details.login_url);
        browser.setValue('[name=username]', login_details.username);
        browser.setValue('[name=password]', login_details.password);
        browser.click('#login');
    });

    afterAll(function () {
        browser.click('a*=Logout');
    });

    it('can go to the Groups page', function () {
        browser.click('a=Setup');
        browser.click('a=Groups');
        expect(browser.getTitle()).toBe('Emoncms - group');
    });

    it('can create a group', function () {
        browser.click('#groupcreate');
        browser.setValue('#group-create-name', group_name);
        browser.setValue('#group-create-description', group_description);
        browser.click('#group-create-action');
        expect(browser.isExisting('.group=' + group_name)).toBe(true);
    });

    it('can browse a group', function () {
        browser.click('.group=' + group_name);
        expect(browser.getText('#groupname')).toBe(group_name);
        expect(browser.getText('#groupdescription')).toBe(group_description);
    });

    it('is a member with role Administrator of the group just created', function () {
        expect(browser.getText('.user-info .user-name')).toBe(login_details.username);
        expect(browser.getText('.user-info .user-role')).toBe('Administrator');
    });

    it('can delete the group', function () {
        browser.click('#deletegroup');
        browser.click('#delete-group-action');
        expect(browser.isExisting('.group=' + group_name)).toBe(false);
    });

    it('and the list of users disappears from the UI', function () {
        expect(browser.isExisting('.user-info')).toBe(false);
    });

});

