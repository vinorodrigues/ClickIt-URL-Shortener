<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 */

// common

$lang['#DATE_FORMAT'] = 'j/m/y';
$lang['#DATE_LONG_FORMAT'] = 'D, j M Y';

$lang['DATABASE_ERROR'] = 'Database Error';
$lang['ACCESS_DENIED'] = 'User access denied';
$lang['NO_ACCESS'] = 'Your user access level does not allow this action';

$lang['STATUS_200'] = 'OK';
$lang['STATUS_201'] = 'Created';
$lang['STATUS_202'] = 'Accepted';
#$lang['STATUS_203'] = 'Non-Authoritative Information';  // (since HTTP/1.1)
#$lang['STATUS_204'] = 'No Content';
#$lang['STATUS_205'] = 'Reset Content';
$lang['STATUS_206'] = 'Partial Content';
#$lang['STATUS_207'] = 'Multi-Status';  // (WebDAV) (RFC 4918)
#$lang['STATUS_226'] = 'IM Used';  // (RFC 3229)
$lang['STATUS_400'] = 'Bad Request';
$lang['STATUS_401'] = 'Unauthorized';
$lang['STATUS_402'] = 'Payment Required';
$lang['STATUS_403'] = 'Forbidden';
$lang['STATUS_404'] = 'Not Found';
$lang['STATUS_405'] = 'Method Not Allowed';
$lang['STATUS_406'] = 'Not Acceptable';
#$lang['STATUS_407'] = 'Proxy Authentication Required';
$lang['STATUS_408'] = 'Request Timeout';
$lang['STATUS_409'] = 'Conflict';
$lang['STATUS_410'] = 'Gone';
#$lang['STATUS_411'] = 'Length Required';
$lang['STATUS_412'] = 'Precondition Failed';
#$lang['STATUS_413'] = 'Request Entity Too Large';
#$lang['STATUS_414'] = 'Request-URI Too Long';
#$lang['STATUS_415'] = 'Unsupported Media Type';
#$lang['STATUS_416'] = 'Requested Range Not Satisfiable';
#$lang['STATUS_417'] = 'Expectation Failed';
#$lang['STATUS_418'] = "I'm a teapot";
#$lang['STATUS_422'] = 'Unprocessable Entity';  //(WebDAV) (RFC 4918)
#$lang['STATUS_423'] = 'Locked';  // (WebDAV) (RFC 4918)
#$lang['STATUS_424'] = 'Failed Dependency';  // (WebDAV) (RFC 4918)
#$lang['STATUS_425'] = 'Unordered Collection';  // (RFC 3648)
#$lang['STATUS_426'] = 'Upgrade Required';  // (RFC 2817)
#$lang['STATUS_444'] = 'No Response';
#$lang['STATUS_449'] = 'Retry With';
#$lang['STATUS_450'] = 'Blocked by Windows Parental Controls';
#$lang['STATUS_499'] = 'Client Closed Request';
$lang['STATUS_500'] = 'Internal Error';
$lang['STATUS_501'] = 'Not implemented';
$lang['STATUS_502'] = 'Bad Gateway';
$lang['STATUS_503'] = 'Service Unavailable';
$lang['STATUS_504'] = 'Gateway Timeout';
#$lang['STATUS_505'] = 'HTTP Version Not Supported';
#$lang['STATUS_506'] = 'Variant Also Negotiates';  // (RFC 2295)
#$lang['STATUS_507'] = 'Insufficient Storage';  // (WebDAV) (RFC 4918)[6]
#$lang['STATUS_509'] = 'Bandwidth Limit Exceeded';  // (Apache bw/limited extension)
#$lang['STATUS_510'] = 'Not Extended';  // (RFC 2774)


// template page

$lang['-'] = ' - ';  // title and post copyright
$lang['['] = '';  // nav-menu
$lang['|'] = ' | ';  // nav-menu
$lang[']'] = '';  // nav-menu

$lang['HELLO'] = 'Hello <a href="*|url|*" class="user">*|username|*</a>';
$lang['HOME'] = '<a href="*|url|*" class="home">Home</a>';
$lang['LOGON'] = '<a href="*|url|*" class="loginout">Login</a>';
$lang['LOGOFF'] = '<a href="*|url|*" class="loginout">Logout</a>';
$lang['LIST_PAGE'] = '<a href="*|url|*" class="list">Your URLs</a>';
$lang['ARCH_PAGE'] = '<a href="*|url|*" class="arch">Your Archives</a>';
$lang['ADMIN_PAGE'] = '<a href="*|url|*" class="admin">Settings</a>';

$lang['COPYRIGHT'] = 'Copyleft <small>(CC)</small> 2011 <a href="*|url|*">Tecsmith</a>';
$lang['LICENSE'] = '<a rel="license" href="*|url|*">Some rights reserved</a>';
$lang['TERMSOS'] = '<a rel="toc" href="*|url|*">Terms of Service</a>';
$lang['PRIVACY'] = '<a rel="privacy" href="*|url|*">Privacy Policy</a>';
$lang['SUPPORT'] = '<a rel="support" href="*|url|*">Help!</a>';

$lang['YOU_MUST_LOGIN'] = 'You need to login to access this resource.';

// library page

$lang['PROVIDE_USERNAME'] = 'Please provide a user name';
$lang['FORM_TOKEN_MISMATCH'] = 'Form security token mismatch';
$lang['PASSWORD_MISMATCH'] = 'Password mismatch';
$lang['USER_NOT_FOUND'] = 'User Name not found';
$lang['LOGIN_SUCCESSFUL'] = 'Login successful';
$lang['ACCOUNT_LOCKED_OUT'] = 'You have exceeded your password attempts';
$lang['LOGGED_OUT_OTHER_SESSIONS'] = 'You have been logged out of other sessions';
$lang['MUST_CHANGE_PASSWORD'] = 'It\'s recommended that your change you password at this time';

$lang['TIME_AGO'] = '*|time|* ago';
$lang['YEAR'] = 'year';
$lang['YEARS'] = 'years';
$lang['MONTH'] = 'month';
$lang['MONTHS'] = 'months';
$lang['WEEK'] = 'week';
$lang['WEEKS'] = 'weeks';
$lang['DAY'] = 'day';
$lang['DAYS'] = 'days';
$lang['HOUR'] = 'hour';
$lang['HOURS'] = 'hours';
$lang['MINUTE'] = 'minute';
$lang['MINUTES'] = 'minutes';
$lang['SECOND'] = 'second';
$lang['SECONDS'] = 'seconds';
$lang['NOW'] = 'an instant';

// login page

$lang['ALREADY_SIGNED_IN'] = 'You are already signed in as *|username|*';
$lang['USERNAME'] = 'User Name';
$lang['PASSWORD'] = 'Password';
$lang['LOGIN'] = 'Login';
$lang['REMEMBER_ME'] = 'Remember me';

$lang['DONT_HAVE_ACCOUNT'] = 'Don\'t have an account? <a href="*|url|*">Signup Now!</a>';
$lang['FORGOT_PASSWORD'] = '<a href="*|url|*">Forgot</a> your password?';

// forgot page

$lang['FORGOTTEN_PASSWORD'] = 'Forgotten Password';
$lang['YOU_FORGOT'] = array(
	'Oh no!  You forgot your password.',
	'That\'s okay - just enter in your login user name or your email below and we\'ll send you an email that will reset your login.',
	);
$lang['USERNAME_OR_EMAIL'] = 'User Name or Email';
$lang['SEND_REMINDER'] = 'Send Reminder';
$lang['GOT_FORGOT_KEY'] = array(
	'If you have already been sent a reminder you can validate here:',
	);
$lang['TOKEN'] = 'Token';
$lang['CHECK'] = 'Check';
$lang['VALIDATE'] = 'Validate';
$lang['FORGOT_EMAIL'] = array(
	'Hello *|realname|*,',
	'',
	'You have been sent this message because you (or someone claiming to be you) has requested a forgotten password.',
	'',
	'To sign in and change your password please visit:',
	'*|fullurl|*',
	'',
	'Or visit *|url|* and enter in the following data:',
	'    Token: *|token|*',
	'    Check: *|check|*',
	'',
	'(Should you have remembered your password that will also work - assuming you\'ve not been locked out.)',
	'',
	'-Regards,',
	'c1k.it',
	);
$lang['FORGOT_EMAIL_SENT'] = 'Email with verification instructions sent';
$lang['FORGOT_EMAIL_NOT_SENT'] = 'Problem with sending emails. Please contact the <a href="mailto:*|email|*">webmaster<a>';
$lang['RETURN_TO_HOMEPAGE'] = 'Return to the <a href="*|url|*">home page</a>.';
$lang['USER_NAME_OR_EMAIL_NOT_FOUND'] = 'User Name or Email not found or account disabled';
$lang['TRY_AGAIN'] = '<a href="*|url|*">Try again</a>?';
$lang['TOKEN_NOT_FOUND'] = 'Token not found or check mismatch';

// admin page

$lang['NOT_ADMIN'] = 'You do not have administartor privlages';

$lang['SAVED_IN_NO_ENTRY'] = 'Not found';
$lang['SAVED_IN_PHP'] = 'Set in the configuration file';
$lang['SAVED_IN_DATABASE'] = 'Set as global to all users';
$lang['SAVED_IN_USER'] = 'Set as specific to this user';


// index page

$lang['WELCOME'] = 'Welcome to c1k.it!';
$lang['PREFACE'] = array(
	'c1k.it is URL shortening service.',
	'',
	'Similar to other public shortening services, it is run by <a href="http://tecsmith.com.au">tecsmith.com.au</a> for its online marketing clients.',
	);
$lang['ENTERLONG'] = 'Enter a long URL to make short';
$lang['ENTERSHORT'] = 'Custom short <i>(optional)</i>';
$lang['MAY_CONTAIN_LETTERS'] = 'May contain letters, numbers and underscores';
$lang['CREATE'] = 'Go';

// list page

//$lang['?'] = '&iquest;?';
$lang['LIST'] = 'Active URL\'s';
$lang['ARCHIVES'] = 'Archived URL\'s';
$lang['NOT_LOGGED'] = '<i>unlogged</i>';
#$lang['URL_ICON_DATA'] = '<img src="http://www.getfavicon.org/?url=*|domain|*" width="16" height="16" />';
$lang['URL_ICON_DATA'] = '<img src="http://www.google.com/s2/favicons?domain=*|domain|*" width="16" height="16" />';
$lang['URL_LIST_DATA'] = array(
	'*|icon|*<span class="title">*|title|*</span> <span class="clicks">(*|count|*)</span><br />',
	'<a href="*|longurl|*" class="longurl" title="Maps to *|longurl|*">*|longurl|*</a><br />',
	'<a href="*|shorturl|*" class="shorturl" title="Using local url *|shorturl|*">*|fullshorturl|*</a><br />',
	'<span class="date">Created on *|date|**|stats|*</span>',
	);
$lang['URL_ARCH_DATA'] = array(
	'*|iconarch|*<span class="title">*|title|*</span> <span class="clicks"></span><br />',
	'<span class="longurl" title="Maps to *|longurl|*">*|longurl|*</span><br />',
	'<span class="shorturl" title="Using local url *|shorturl|*">*|fullshorturl|*</span>',
	);
$lang['URL_LIST_DATA_STATS'] = ', stats from *|datef|* to *|datel|*</span>';
$lang['URL_LIST_DATA_NONE'] = ', <i>no clicks yet</i>';
$lang['SHOW_FOR_USER'] = '<label for="userid">Show URL\'s for user</label>';
$lang['ANON_USERS'] = 'Anonymous Users';
$lang['YOU'] = '*';
$lang['NO_RECORDS_FOUND'] = '<i>(Nada)</i>';
$lang['EDIT'] = 'Edit';
$lang['ARCHIVE'] = 'Archive';
$lang['UNARCHIVE'] = 'Unarchive';
$lang['ANONIMIZE'] = 'Anonimize';
$lang['KILL'] = 'Delete';
$lang['ARE_YOU_SURE'] = 'Are you certain you want to *|action|* the entry *|name|*?';
$lang['CANT_BE_UNDONE'] = 'This is a permanent action and cannot be reverted.';

// edit page
// create page

$lang['EDIT_URL'] = 'Edit URL';
$lang['SHORT_NOT_AUTH'] = 'User not authorised to create a custom short';
$lang['VALIDATION_ERROR'] = 'Validation Error';
$lang['DUPLICATION_ERROR'] = 'Duplication Error';
$lang['LONGURL_ALREADY_SHORT'] = 'Long URL "*|longurl|*" already maps to short "<code>*|shorturl|*</code>".';
$lang['SHORT_ALREADY_MAPPED'] = 'Short "<code>*|shorturl|*</code>" already exists and may not be used again.';
$lang['UNABLE_TO_GENERATE_SHORT'] = 'Unable to generate a randomized short at this time, please try later';
$lang['SHORT_CREATED'] = 'Short Created';
$lang['SHORT_CREATED_OK'] = 'Short created as \'*|short|*\'';
$lang['SHORT_CREATED_DESCRIPTIVE'] = array(
	'The short URL has been created.',
	'The URI you can use to access this service is: <br/><code>*|fullshorturl|*</code> *|copy|*' .
		'<br />Or preview <a href="*|previewurl|*">here</a>.',
	);

$lang['UNKNOWN_ACTION'] = 'Don\'t know what to do with that request';
$lang['INAPPROPRIATE'] = 'Inapropriate use of this page';
$lang['SUBMIT_EDITS'] = 'Apply Changes';
$lang['RESET_EDITS'] = 'Revert Edits';
$lang['BACK'] = 'Back';
$lang['FIELD_SHORTURL'] = 'Short Bit';
$lang['FIELD_LONGURL'] = 'Long URL';
$lang['FIELD_USERID'] = 'User';
$lang['FIELD_ENABLED'] = 'Enabled';
$lang['FIELD_CLOAK'] = 'Cloak Pages';
$lang['FIELD_TITLE'] = 'Title';
$lang['FIELD_METAKEYW'] = 'Meta-Keywords';
$lang['FIELD_METADESC'] = 'Meta-Description';
$lang['FIELD_LOG'] = 'Log Clicks';
$lang['ON'] = '<img src="images/ico_checked.png" alt="Yes" />';
$lang['OFF'] = '<img src="images/ico_unchecked.png" alt="No" />';
$lang['EDITS_SAVED'] = 'Edits have been saved';
$lang['NO_CHANGES_FOUND'] = 'No changed encountered';
$lang['NOT_YOURS'] = 'The requested resource is not yours to modify';
$lang['EDITING_SHORTS_DANGEROUS'] = 'Changing Short Bit may break external links to this resource';
$lang['ROWS_AFFECTED'] = 'Changes made to the <b>*|rows|*</b> row(s)';

// delete page

$lang['DELETED'] = '\'*|title|*\' was archived';
$lang['UNDONE'] = '\'*|title|*\' was reactivated';
$lang['ANONYMIZED'] = '\'*|title|*\' was anonymized';

// signup page

$lang['SIGNUP_HERE'] = array(
	'Your c1k.it account gives you access to create and edit short URL\'s.',
	'If you already have a c1k.it account, you can <a href="*|url|*">log in here</a>.'
	);
$lang['SIGNUPS_DISABLED'] = 'Signup\'s to this site are disabled';
$lang['SUBMIT_USER'] = 'Submit for Account';
$lang['CONFIRM_EMAIL'] = 'Confirm Email';
$lang['CAPTCHA_FAILED'] = 'CAPTCHA code not accepted';
$lang['USERNAME_NOT_AVAIL'] = 'Username not available for use';
$lang['USERNAME_TOO_SHORT'] = 'Username too short';
$lang['USERNAME_NOT_VALID'] = 'Username can only contain letter, numbers and underscores';
$lang['EMAILS_DO_NOT_MATCH'] = 'Email confirmation does not match email submitted';
$lang['EMAIL_NOT_VALID'] = 'Invalid email address';
$lang['EMAIL_NOT_AVAIL'] = 'Email already in use';
$lang['USER_CREATED'] = 'User Created';
$lang['USER_CREATED_OK'] = 'User Created Successfuly';
$lang['NEW_USER_EMAIL'] = array(
	'Welcome *|realname|*,',
	'',
	'Your user account has been created and you can signon at:',
	'  *|url|*',
	'',
	'You can signon with the username you provided, and with the password:',
	'  *|password|*',
	'(You will be asked to change this when you signon.)',
	'',
	'Thank you for your patronage.',
	'',
	'-Regards,',
	'c1k.it',
	);
$lang['NEW_USER_EMAIL_FOR_ADMIN'] = array(
	'',
	'---------- WEBMASTER -------------------------------------------------',
	'NEW USER REQUEST:',
	'',
	'On approval forward above part to: *|email|*',
	'Or delete user at: *|delete_url|*',
	'----------------------------------------------------------------------',
	);
$lang['NEW_USER_CREATED'] = 'New User Created';
$lang['NEW_USER_EMAIL_SENT'] = 'An email has been sent to you with your temporary password';
$lang['NEW_USER_EMAIL_SENT_TO_ADMIN'] = 'An email has been sent to the webmaster for approval';
$lang['NEW_USER_EMAIL_NOT_SENT'] = 'We\'re experiencing email issues, please try later';
$lang['REALNAME'] = 'Display Name';
$lang['EMAIL'] = 'Email';

// user page

$lang['YOUR_ACCOUNT'] = 'Account Details';

$lang['USER_LEVEL_BS'] = 'Logon only';
$lang['USER_LEVEL_CR'] = 'Create URL\'s';
$lang['USER_LEVEL_LS'] = 'Create & List URL\'s';
$lang['USER_LEVEL_EL'] = 'Create, List & Edit URL\'s';
$lang['USER_LEVEL_DS'] = 'Create, List, Edit & Archive URL\'s';
$lang['USER_LEVEL_CU'] = 'Create, List, Edit & Archive Custom URL\'s';
$lang['USER_LEVEL_ES'] = 'All rights, incl. Edit Short bit';
$lang['USER_LEVEL_DL'] = 'All rights, incl. Edit Short bit & Delete';
$lang['USER_LEVEL_AD'] = 'Site Administrator';
$lang['USER_LEVEL_GD'] = 'Site Super Administrator';

$lang['FIELD_ID'] = 'User ID';
$lang['FIELD_USERNAME'] = $lang['USERNAME'];
$lang['FIELD_USERLEVEL'] = 'User Access Level';
$lang['FIELD_REALNAME'] = $lang['REALNAME'];
$lang['FIELD_EMAIL'] = $lang['EMAIL'];
$lang['FIELD_CREATEDON'] = 'Member Since';
$lang['FIELD_LASTVISITON'] = 'Last visit on';
$lang['FIELD_ENABLED'] = 'Enabled';
$lang['NOT_SET'] = '<span class="red">--</span>';

$lang['SHOW_USER'] = 'Show User';
$lang['EDIT_ACCOUNT'] = 'Edit Account';
$lang['CHANGE_EMAIL'] = 'Change Email';
$lang['CHANGE_PASSWORD'] = 'Change Password';
$lang['DELETE_ACCOUNT'] = 'Delete Account';
$lang['VIEW_URLS'] = 'View URLs';
$lang['VIEW_ARCHIVES'] = 'View Archives';

$lang['PASSWORD_WARNING'] = array(
	'Remember that if this site is not transmitted in SSL (you\'ll see a <code>https://</code> in the URL, and a verified lock will appear on your browser), that your password will be sent to this server in clear text and can be snooped on at transmission.',
	'<b class="red">It is recommended that you use a different password for every site you access, including this one.</b>',
	'We will not store your password as clear text – rather the password in encrypted with a unidirectional MD5 algorithm – i.e. it cannot be decrypted.',
	);
$lang['PASSWORD_CONDITIONS'] =
	'Passwords must: <ul>' .
	'<li>Must be at least 8 characters long</li>' .
	'<li>Must contain at least one one lower case letter</li>' .
	'<li>Must contain at least one upper case letter</li>' .
	'<li>Must contain at least one numeric digit</li>' .
	'</ul>';
$lang['FIELD_OLDPASSWD'] = 'Old Password';
$lang['FIELD_PASSWD'] = 'New Password';
$lang['FIELD_PASSWD2'] = 'Confirm Password';

$lang['PASSWORD_NOT_VALID'] = 'Password not valid';
$lang['OLD_PASSWORD_MISMATCH'] = 'Incorrect old password';
$lang['CONFIRM_PASSWORD_MISMATCH'] = 'Confirmed password does not match';

$lang['FIELD_DELETE'] = 'Type the word \'DELETE\' to confirm';
$lang['DELETE_WARNING'] = array(
	'Deleting a user is a permanent operation.',
	'However, any allocated Short URL\'s will be moved to the anonymous account, as these cannot be deleted.',
	'<b class="red">This action cannot be reverted.</b>',
	);
$lang['YOU_ARE_ABOUT_TO_DELETE'] = 'You are about to delete user *|userid|* \'*|username|*\' (\'*|realname|*\').';
$lang['USER_NOT_DELETED'] = 'User not deleted';
$lang['USER_DELETED'] = 'User deleted';

$lang['EMAIL_WARNING'] = array(
	'Please provide a legitimate email address.',
	'Note that by changing the email address of this account the password' .
	' will also be reset to a randomly generated password. This password' .
	' will then be emailed to the address provided and will act as' .
	' verification. Failure of this process will result in an account that' .
	' cannot be accessed.',
	'<b class="red">This process cannot be reverted.</b>',
	);
$lang['FIELD_EMAIL2'] = 'Confirm Email';

$lang['EMAIL_CHANGED_EMAIL'] = array(
	'Hello *|realname|*,',
	'',
	'Your user changed email has been saved and you should signon at:',
	'  *|url|*',
	'',
	'You can signon with your username, and with the password:',
	'  *|password|*',
	'(You will be asked to change this when you signon.)',
	'',
	'Thank you for your patronage.',
	'',
	'-Regards,',
	'c1k.it',
	);;
$lang['EMAIL_CHANGED'] = 'Email Changed';
$lang['EMAIL_CHANGED_EMAIL_SENT'] = 'Changed Email confirmation sent';
$lang['EMAIL_CHANGED_EMAIL_NOT_SENT'] = 'Unable to send confirmation Email';

// captcha library

$lang['INCORRECT'] = 'Incorrect, please try again';
$lang['ENTER_WORDS'] = 'Enter CAPTCHA';
$lang['ENTER_NUMBERS'] = 'Enter audible numbers';
$lang['GET_ANOTHER'] = '<img src="images/ico_refresh.png" alt="Get another code" />';
$lang['GET_AUDIO'] = '<img src="images/ico_sound.png" alt="Get audio code" />';
$lang['GET_IMAGE'] = '<img src="images/ico_text.png" alt="Get text code" />';
$lang['GET_HELP'] = '<img src="images/ico_help.png" alt="Help" />';

// admin page

$lang['SITE_ADMIN'] = 'Settings';

$lang['OTHER_SETTINGS'] = 'Other Settings';
$lang['SHOW_SETTINGS_FOR'] = 'Show settings for';
$lang['ALL_USERS'] = 'Sitewide / All Users';
$lang['SETTING'] = 'Setting';
$lang['VALUE'] = 'Value';
$lang['INFO'] = '<img src="images/ico_info.png" title="Help and information on where the setting is saved" />';
$lang['DELETE'] = '<img src="images/ico_arch.png" title="Check to delete item if available" />';
$lang['DELETE_ITEM'] = 'Delete item';
$lang['CHANGE_SITEWIDE'] = 'Sitewide / For all users';
$lang['CHANGE_FOR_USER'] = 'Only for user ID *|userid|*, *|username|* (*|realname|*)';
$lang['CHANGES_MADE'] = '*|cnt|* changes made';
$lang['SETTING_DELETED'] = 'Setting <b>*|name|*</b> deleted';
$lang['SETTING_UPDATED'] = 'Setting <b>*|name|*</b> updated';
$lang['SETTING_INSERTED'] = 'Setting <b>*|name|*</b> inserted';


// ajax page

$lang['MISSING_FUNCTION_NAME'] = 'Missing function name in request';
$lang['MISSING_QUERY_STRING'] = 'Missing query string in request';
$lang['FUNCTION_NAME'] = 'Unknown function name "*|function|*"';
$lang['CANNOT_CONNECT'] = 'Cannot connect to database';

$lang['LONGURL_NOT_VALID'] = 'URL "*|url|*" not valid';
$lang['LONGURL_NOT_VALID_DESC'] = '<span class="validation">Not a valid URL</span>';
$lang['LONGURL_VALID'] = '*|url|*';
$lang['LONGURL_VALID_DESC'] = '<span class="success">OK</span>';  // leave empty
$lang['LONGURL_USED'] = '*|url|*';
$lang['LONGURL_USED_DESC'] = '<span class="warning">Already shortened</span>';

$lang['SHORTURL_NOT_VALID'] = 'Short "*|url|*" not valid';
$lang['SHORTURL_NOT_VALID_DESC'] = '<span class="validation">Not a valid short</span>';
$lang['SHORTURL_VALID'] = '*|url|*';
$lang['SHORTURL_VALID_DESC'] = '<span class="success">OK</span>';  // leave empty
$lang['SHORTURL_TAKEN'] = '*|url|*';
$lang['SHORTURL_TAKEN_DESC'] = '<span class="warning">Exists already</span>';

// load page

$lang['PREVIEW'] = '<i>Preview:</i>';
$lang['SEC_PREFIX'] = ' - in ';
$lang['SEC_SUFIX'] = ' seconds <img src="images/loading.gif" />';
$lang['REDIRECTING_TO'] = 'Redirecting to <a href="*|url|*">*|url|*</a>';
$lang['LINK'] = 'Copy URL: <code>*|url|*</code> *|copy|*';
$lang['LINK_M'] = 'Scannable URL: <code>*|url|*</code> *|copy|*';

?>