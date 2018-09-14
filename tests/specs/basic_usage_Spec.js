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
    it('can login', function () {
        helper.logIfDebug('\nSpecification: A Group user can login\n---------------------');
        helper.loginDefaultUser();
        let page_url = browser.getUrl();
        expect(page_url).not.toBe(login_details.login_url);
        expect(browser.isExisting('a*=Logout')).toBe(true);
    });

    it('can logout', function () {
        helper.logIfDebug('\nSpecification: A Group user can logout');
        helper.logout();
        let page_url = browser.getUrl();
        expect(page_url).toBe(login_details.login_url);
        expect(browser.isExisting('[name=username]')).toBe(true);
    });
});

