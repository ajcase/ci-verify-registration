### v2.1 Main App Functionality

- Allows for verification of TOTP and Push.
- Support for multiple devices!
- Walks users through registering their device with CIV.
- Acts as a Service Provider to Cloud Identity Connect and accepts SAML authentication.
- Attributes sent via the Identity Provider (CIC) can be utilized within the code.
- Allows for test push notification to IBM Verify registered devices.
- See all transactions that have been performed on the devices that you have enrolled.

### Updates
- Added timeout for push notification.
- Added logout page to remove the session.
- Added more reactive CSS for smaller browser windows.
- Added a transaction log for understanding when a notification was attempted and to what device. (last 20 transactions only)
- Added function `getAuthenticatorName()` to obtain the device name of the deviceID for use in the transaction log
- Added function `getTransactionLog()` to obtain all verification transactions to the devices. 
- Added new file `footer.php` to start separating components of the templates.
- Added function `getVerificationTransaction()` to get the status of a specific transaction
- Changed output of `GET /verifyPush API` to retrieve the state of the transaction
- Created function `worker()` and `watch()` that polls the verification transaction function `getVerificationTransaction()` for status changes.
- Old Updates: 
    - Added support for IBM Verify registrations.
    - Removed TOTP-only support, tenant must be configured for IBM Verify registration
    - Support for multiple devices, but only one TOTP device.

### Example

![alt text](https://github.ibm.com/ajcase/CIV-DeviceRegistration/blob/master/DeviceRegistration-example.png "Device Registration Example for CIV")

### Technologies Used
+ PHP 7+
  + Httpful (http://phphttpclient.com/)
  + Slim template framework (https://www.slimframework.com/docs/v3/features/templates.html)
  + SimpleSAMLphp (https://simplesamlphp.org/docs/stable/)
  + PHP Settings Required:
    + `short_open_tag = On`
    + `display_errors = On`
    + Note: For Bluemix PHP apps, create a file called `.user.ini` in the root directory of the app. It get's left off of Git.

### Pre-Req for CIV API
Run the following API commands using Postman or another REST Client.
+ Enable Signatures in your API: `PUT /v1.0/authnmethods/signature`
```JSON
{
    "userPresence": {
        "supportedAlgorithms": [
            "RSASHA256"
        ],
        "enabled": true,
        "algorithm": "RSASHA256"
    },
    "fingerprint": {
        "supportedAlgorithms": [
            "RSASHA256"
        ],
        "enabled": true,
        "algorithm": "RSASHA256"
    },
    "enabled": true
}
```
+ Create Authenticator Client and get ID: `POST /v1.0/authenticators/clients`
```JSON
{
  "authorizationCodeLifetime": 600,
  "enabled": true,
  "name": "Security Demonstration",
  "refreshTokenLifetime": 21600,
  "accessTokenLifetime": 3600
}
```

### Configuring the Environment
1. Open the `api_connect.php` file, and edit the variables placing in your client_id, client_secret, and base URL (your CIC tenant URL).
```PHP
<?php
$client_id = "";
$client_secret = "";
$url = "https://tenant.ice.ibmcloud.com/v1.0/endpoint/default/token";
?>
```
  + Note: I am aware this can be combined with `config.php` but haven't gotten there yet.
2. Open the `config.php` file, and configure the variables.
  + `$base_url` is the URL of your CIC tenant without the forward slash. e.g. https://tenant.ice.ibmcloud.com
  + `$account_name` is the account name that represents the IBM Verify registration.
  + `$authclient_id` is the Authenticator Client ID. Get this from API `GET /v1.0/authenticators/clients`
  + `$verifyMessage` is the message value of the test push notification.
  + `$verifyTitle` is the title of the Push notification.
3. Update the logo by placing a new logo called `logo.png` in the /images folder.

### Configure the App as Service Provider (SAML 2.0)
1. Navigate and open the file `/sso/config/config.php` in a text editor.
2. Update the following lines:
  + `'baseurlpath' => 'https://deviceregistration.mybluemix.net:443/sso/www/',` # with your hosted URL.
    + Note: Be sure to include /sss/www if you want to be able to navigate successfully to the SAML SP UI that SimpleSAML
  + `'technicalcontact_name' => 'Administrator',`
  + `'technicalcontact_email' => '',`
  + `'secretsalt' => '',` # this is a secret random SALT in order to provide a secure hash for your app.
  + `'auth.adminpassword' => 'Passw0rd',` # this is the auth password for the admin UI.
3. Save the configuration or continue editing other configurations as needed (please refer to SimpleSAML documentation).
4. Navigate and open the file `/sso/config/authsources.php` in a text editor.
5. Add and configure the following lines in the `default-sp` array
```PHP
'default-sp' => array(
        'saml:SP',

        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => null,

        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => 'https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20', // Your CIC Tenant URL
        'RelayState' => 'https://deviceregistration.mybluemix.net/', // Your default relay state
```
6. Save the configuration of the `authsources.php file`.
7. Obtain your CIC certificate and save it as `cert.crt` and place it into the `/sso/cert` folder
  + Note: Cert filename (cert.crt) must match exactly otherwise you'll have to make additional configurations.
8. Navigate to and open the file `/sso/metadata/saml20-idp-remote.php` and edit apply your metadata information the format below:
```PHP
<?php
$metadata['https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20'] = array( // this is the Entity ID of your Identity Provider
    'SingleSignOnService'  => 'https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20/login', // This is the Login URL of the IdP
    'SingleLogoutService'  => 'https://tenant.ice.ibmcloud.com/idaas/mtfim/sps/idaas/logout',
    'certificate'          => 'cert.crt',
);
```
  + Note: Ensure that the entity ID in the file above matches the entity ID used in the `authsources.php` file.
  + Note: You can also obtain an automated flat file of your metadata by going to `http://yourapp/sso/www/admin/metadata-converter.php`
9. Host the entire project on a webhost with PHP installed.

### Host Locally
1. To host locally, please install PHP 7.0+ on your local machine.
2. Navigate to the working directory, and run the following terminal command: `$ php -S localhost:8888`
3. In your browser, navigate to the website: http://localhost:8888
4. You will be redirected to your identity provider, but if that fails, look at the SAML configuration from the above sections to ensure successful configuration.

### Configure your Identity Provider (SAML 2.0) (eg. CIC)
1. In your IdP configuration, create a new Service Provider
2. Configure the Provider ID (Entity ID). eg. https://deviceregistration.mybluemix.net/sso/www/module.php/saml/sp/metadata.php/default-sp
3. Configure the Assertion Consumer Service URL: eg. https://deviceregistration.mybluemix.net/sso/www/module.php/saml/sp/saml2-acs.php/default-sp
4. Configure the username value as EmailAddress. Note: This value is used to lookup the user in CIC.
5. This application expects the following attributes (send as unspecified):
  + lastname
  + firstname
  + name
  + mail
  + uid  

### Future Items Needed
1. Better UI for device view
2. More responsive Mobile UI
3. Pagination for all device transactions
