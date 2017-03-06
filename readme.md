## Setting up CoreAuth
If you're using OpenLDAP rather than Active Directory, special configuration is required.

You must set your OU (Organizational Unit) to the OU you're storing your users in.
For example, OU=Users. Make sure you include the Base DN too.
An example of a full name could be CN=josephmarsden,OU=Users,DC=core,DC=towerdevs,DC=xyz.
