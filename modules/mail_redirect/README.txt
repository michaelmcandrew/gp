General Usage:

Redirect any system generated email to your configured email domain.

usage:
- in your test site's settings.php set:
  $conf = array('mail_redirect_domain' => "mydomain.com");

result:
- input $to: john_smith@about.com
- output $to: john_smith@mydomain.com

This module was developed for a multi-developer test environment where ongoing development work runs in parallel with 
the operation of the production site. The developers regularly sync their test site's db to that of the production 
server. Our general development environment provides numerous sites folders for a mutli-site setup so that each developer 
has their own local and server based sandboxes for testing and development. As an example:

3 developers: tom, joe, hank

site folders as:
- www.oursite.com (production site)
- oursite.joe (joe's local)
- oursite.tom
- outsite.hank
- joe.oursite.com (joe's server sandbox)
- hank.oursite.com
- tom.oursite.com

Set up subdomains on a shared host account (we use Dreamhost.com) which provides unlimited subdomains and catch-all email accounts.

e.g. mail domains:
- joe.somedomain.com
- hank.somedomain.com

Set each of these up with catch-all mail accounts.

For Joe's local development system (oursite.joe):
- in sites/oursite.joe/settings.php
- defined $conf = array('mail_redirect_domain' => "joe.somedomain.com");

Now, when mail_redirect module is enabled all the site email will redirect to that domain. E.g.:

Janet_Smith@gmail.com -> Janet_Smith@joe.somedomain.com

All mail will be sent to one catch-all account and it is possible to see what email the system has sent out and who they 
have been sent to.

