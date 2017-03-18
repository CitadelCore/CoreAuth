## Setting up CoreAuth
This server is for our on-premises solution. For new customers and businesses, the cheapest and easiest to set up is our Hosted solution, so please consider that before you start deploying this server. Our products can be found at https://auth.central.core.

If you don't yet have a on-premises license for CoreAuth, you can buy one at our website, https://auth.central.core, or alternatively use our limited free community license for non-commercial use.

1. Configuration

For CoreAuth Enterprise:
To configure CoreAuth, you will need a few different things. Your organization name without any spaces, for example "MyOrg", your organization key (you can find this in the Core^2 Organization Manager), your license serial, and your license key.

After this, set the AuthProcessor option to your preferred authentication connector:
- mysql (Connects to a MySQL or MySQL-compatible server)
- ldap (Works with OpenLDAP and Active Directory)
- radius (Work in progress)
- flatfile (Work in progress)

If your subscription supports External SSO, these External connectors are also available and require additional configuration:
- adfs (Active Directory Federation Services)
- oauth2 (OAuth 2 compatible)
- saml (SAML compatible)

For CoreAuth Community:

Do not enter anything for the Organization and License parameters, and instead set the "Community" configuration option to "true".
Note the following features do not work in CoreAuth Community:
- Remote API Access
- RiskEngine
- Multi-Factor Authentication
- External SSO

2. Integration

CoreAuth is highly integratable with your own code and applications. Here are a few examples of requests you can make to the frontend and backend.

CoreAuth requests are split into two defined sections, Frontend and API. Frontend requests redirect the user to the actual CoreAuth page and shows them dynamic information, for example a MFA authentication dialog or a RiskEngine block or warning page. API requests, on the other hand, are designed for use in your own application that you want to integrate with CoreAuth and are meant to be used with AJAX requests, for example. Please note that user notifications and External SSO are not supported via API requests since CoreAuth is unable to handle redirection to the external web service.

Frontend request examples:

Logging a user in:
Send a POST request to /endpoints/login with the parameters username and password.
This will automatically log a user in.

Logging a user out:
Send a POST request to /endpoints/logout.
This will automatically log a user out.

Changing a password:
Send a POST request to /endpoints/changepassword with the parameters username, password, and newpassword.
This will change the user's password.

Creating an account:
Send a POST request to /endpoints/createaccount with the parameters username and password.
This will create a new user account.
(Please note that this is rate limited to 2 requests each 30 seconds per IP. If you need a more rapid user creation method, use an API request. Alternatively you can raise the rate limit in the configuration file, but this is not reccomended.)

Deleting an account:
Send a POST request to /endpoints/deleteaccount with the parameters username and password.
This will delete the specified user's account.

Enabling/disabling/provisioning MFA (if included in subscription):
Send a POST request to /endpoints/mfacontrol with the parameters username and password.

API request examples:

```
Coming soon!
```

3. Deployment

```
Coming soon!
```

If you're using OpenLDAP rather than Active Directory, special configuration is required.

You must set your OU (Organizational Unit) to the OU you're storing your users in.
For example, OU=Users. Make sure you include the Base DN too.
An example of a full name could be CN=josephmarsden,OU=Users,DC=core,DC=towerdevs,DC=xyz.
