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
    it('can login', function () {
        browser.url(login_details.login_url);
        browser.setValue('[name=username]', login_details.username);
        browser.setValue('[name=password]', login_details.password);
        browser.click('#login');
        let page_url = browser.getUrl();
        expect(page_url).not.toBe(login_details.login_url);
        expect(browser.isExisting('a*=Logout')).toBe(true);
    });

    it('can logout', function () {
        browser.click('a*=Logout');
        let page_url = browser.getUrl();
        expect(page_url).toBe(login_details.login_url);
        expect(browser.isExisting('[name=username]')).toBe(true);
    });
});

