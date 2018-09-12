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
    let user_to_add_1 = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    let user_to_add_2 = Math.random().toString(36).substring(2, 8) + Math.random().toString(36).substring(2, 8);
    beforeAll(function () {
        // Login
        browser.url(login_details.login_url)
                .setValue('[name=username]', login_details.username)
                .setValue('[name=password]', login_details.password)
                .click('#login');
        // Go to groups
        browser.click('a=Setup')
                .click('a=Groups');
        //Create a group
        browser.click('#groupcreate')
                .setValue('#group-create-name', group_name)
                .click('#group-create-action')
                .click('.group=' + group_name);
        // Add users to group
        browser.click('#createuseraddtogroup')
                .setValue('#group-createuseraddtogroup-name', user_to_add_1)
                .setValue('#group-createuseraddtogroup-email', user_to_add_1 + '@tururu.com')
                .setValue('#group-createuseraddtogroup-password', 'aaaaaaaaaaaaaaaaaaaaaaa')
                .setValue('#group-createuseraddtogroup-password-confirm', 'aaaaaaaaaaaaaaaaaaaaaaa')
                .setValue('#group-createuseraddtogroup-username', user_to_add_1)
                .selectByVisibleText('#group-createuseraddtogroup-role', 'Administrator')
                .click('#group-createuseraddtogroup-action');
        browser.click('#createuseraddtogroup')
                .setValue('#group-createuseraddtogroup-name', user_to_add_2)
                .setValue('#group-createuseraddtogroup-email', user_to_add_2 + '@tururu.com')
                .setValue('#group-createuseraddtogroup-password', 'bbbbbbbbbbbbbb')
                .setValue('#group-createuseraddtogroup-password-confirm', 'bbbbbbbbbbbbbb')
                .setValue('#group-createuseraddtogroup-username', user_to_add_2)
                .selectByVisibleText('#group-createuseraddtogroup-role', 'Administrator')
                .click('#group-createuseraddtogroup-action');
    });
    afterAll(function () {
        // Logout
        browser.click('a*=Logout'); // Login
        browser.url(login_details.login_url)
                .setValue('[name=username]', login_details.username)
                .setValue('[name=password]', login_details.password)
                .click('#login');
        // Go to group
        browser.click('a=Setup')
                .click('a=Groups');
        browser.click('.group=' + group_name);
        // Fully remove users
        for (let i = 0; i < 2; i++) {
            browser.click('[title="Remove user"]')
                    .click('[for="removeuser-from-group"')
                    .click('#remove-user-action')
                    .click('#remove-user-action');
        }
        // Delete the group
        browser.click('#deletegroup')
                .click('#delete-group-action');
        // Logout
        browser.click('a*=Logout');
    });
    it('can impersonate another user because is an Admnistrator', function () {
        let users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == user_to_add_1) {
                users_divs[i].$('.setuser').click();
                break;
            }
        }
        expect(browser.alertText()).toBe('You are now logged as ' + user_to_add_1);
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add_1)).toBe(true);
    });
    it('can log back', function () {
        browser.click('[href*=logasprevioususer]');
        expect(browser.isExisting('a*=logasprevioususer')).toBe(false);
        expect(browser.isExisting('.group*=' + group_name)).toBe(true);
    });
    it('can impersonate 2 users but we don\'t loose track of the original', function () {
        browser.click('.group=' + group_name);
        // Impersonate first user
        let users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == user_to_add_1) {
                users_divs[i].$('.setuser').click();
                break;
            }
        }
        browser.alertAccept();
        // Go to the group
        browser.click('a=Setup')
                .click('a=Groups');
        browser.click('.group=' + group_name);
        //Impersonate second user
        users_divs = $$('.user');
        for (let i in users_divs) {
            if (users_divs[i].$('.user-name').getText() == user_to_add_2) {
                users_divs[i].$('.setuser').click();
                break;
            }
        }
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add_2)).toBe(true);
    });
    /*it('can keep impersonating other users but we don\'t loose track of the original user', function () {
     
     });*/



})
        ;

