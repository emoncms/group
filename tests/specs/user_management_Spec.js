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
                .setValue('#group-create-description', group_description)
                .click('#group-create-action')
                .click('.group=' + group_name);
    });

    afterAll(function () {
        // Delte the group
        browser.click('#deletegroup')
                .click('#delete-group-action');
        // Logout
        browser.click('a*=Logout');
    });

    it('can create a user and add it to the group', function () {
        browser.click('#createuseraddtogroup')
                .setValue('#group-createuseraddtogroup-name', user_to_add)
                .setValue('#group-createuseraddtogroup-email', 'an_email_address@tururu.com')
                .setValue('#group-createuseraddtogroup-username', user_to_add)
                .setValue('#group-createuseraddtogroup-password', user_to_add_password)
                .setValue('#group-createuseraddtogroup-password-confirm', user_to_add_password)
                .selectByVisibleText('#group-createuseraddtogroup-role', 'Passive member');
        browser.click('#group-createuseraddtogroup-action');
        let created_user_name = $$('.user-info .user-name')[1];
        expect(created_user_name.getText()).toBe(user_to_add);
        let created_user_role = $$('.user-info .user-role')[1];
        expect(created_user_role.getText()).toBe('Passive member');
    });

    it('can impersonate another user because is an Admnistrator', function () {
        browser.click('[title="Log in as user"]'); // There is onlyone user so we don't need to be more specific with the selector
        expect(browser.alertText()).toBe('You are now logged as ' + user_to_add);
        browser.alertAccept();
        expect(browser.isExisting('span*=' + login_details.username + ' => ' + user_to_add)).toBe(true);
    });

    it('can go to the Groups page to ensure the new user cannot see any group', function () {
        browser.click('a=Setup')
                .click('a=Groups');
        expect(browser.isExisting('.group')).toBe(false);
    });

    it('can log back', function () {
        browser.click('[href*=logasprevioususer]');
        expect(browser.isExisting('a*=logasprevioususer')).toBe(false);
        expect(browser.isExisting('.group*=' + group_name)).toBe(true);
    });

    it('can remove a user from the group but keep it in the system', function () {
        browser.click('.group=' + group_name)
                .click('[title="Remove user"]') // There is onlyone user so we don't need to be more specific with the selector
                .click('[for="removeuser-from-group"')
                .click('#remove-user-action')
                .click('#remove-user-action');
        expect(browser.isExisting('.user-name=' + user_to_add)).toBe(false);
    });

    it('can add a member with password', function () {
        browser.click('#addmember')
                .setValue('#group-addmember-username', user_to_add)
                .setValue('#group-addmember-password',user_to_add_password)                
                .selectByVisibleText('#group-addmember-access', 'Sub-administrator')
                .click('#group-addmember-action');
        let created_user_name = $$('.user-info .user-name')[1];
        expect(created_user_name.getText()).toBe(user_to_add);
        let created_user_role = $$('.user-info .user-role')[1];
        expect(created_user_role.getText()).toBe('Sub-administrator');        
    });
    
    it('can completely remove a user from the system', function(){
         browser.click('.group=' + group_name)
                .click('[title="Remove user"]') // There is onlyone user so we don't need to be more specific with the selector
                .click('[for="removeuser-delete"')
                .click('#remove-user-action')
                .click('#remove-user-action');
        expect(browser.isExisting('.user-name=' + user_to_add)).toBe(false);
        
        browser.click('#addmember')
                .setValue('#group-addmember-username', user_to_add)
                .setValue('#group-addmember-password',user_to_add_password)                
                .selectByVisibleText('#group-addmember-access', 'Sub-administrator')
                .click('#group-addmember-action');
        expect(browser.alertText()).toBe('Incorrect authentication');
        browser.alertAccept();
    });

});

