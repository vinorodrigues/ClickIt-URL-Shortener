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

$lang['DATABASE_ERROR'] = 'Database Error';
$lang['ACCESS_DENIED'] = 'User access denied';
$lang['NO_ACCESS'] = 'Your user access level does not allow this action';

$lang['STATUS_200'] = 'OK';
$lang['STATUS_201'] = 'Created';
$lang['STATUS_202'] = 'Accepted';
#$lang['STATUS_203'] = 'Non-Authoritative Information';  // (since HTTP/1.1)
#$lang['STATUS_204'] = 'No Content';
#$lang['STATUS_205'] = 'Reset Content';
#$lang['STATUS_206'] = 'Partial Content';
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

$lang['|'] = ' &#166; ';
$lang['HELLO'] = 'Hello <a href="*|url|*" class="user">*|username|*</a>';
$lang['HOME'] = '<a href="*|url|*" class="home">Home</a>';
$lang['LOGON'] = '<a href="*|url|*" class="loginout">Login</a>'; 
$lang['NOT_YOU'] = '<a href="*|url|*" class="loginout">Not you</a>?';
$lang['LIST_PAGE'] = '<a href="*|url|*" class="list">Your URLs</a>';
$lang['ARCH_PAGE'] = '<a href="*|url|*" class="arch">Archives</a>';
$lang['ADMIN_PAGE'] = '<a href="*|url|*" class="admin">Admin</a>';

$lang['COPYRIGHT'] = 'Copyleft <small>(CC)</small> 2011 <a href="*|url|*">Tecsmith</a>';
$lang['LICENSE'] = '<a rel="license" href="*|url|*">Some rights reserved</a>';
$lang['TERMSOS'] = '<a rel="toc" href="*|url|*">Terms of Service</a>';
$lang['PRIVACY'] = '<a rel="privacy" href="*|url|*">Privacy Policy</a>';

$lang['YOU_MUST_LOGIN'] = 'You need to login to access this resource.';

// library page

$lang['PROVIDE_USERNAME'] = 'Please provide a user name';
$lang['FORM_TOKEN_MISMATCH'] = 'Form security token mismatch';
$lang['PASSWORD_MISMATCH'] = 'Password mismatch';
$lang['USER_NOT_FOUND'] = 'User Name not found';
$lang['LOGIN_SUCCESSFUL'] = 'Login successful';
// $lang['LOGGED_OUT_OTHER_SESSIONS'] = 'You have been logged out of other sessions';

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
$lang['SHOW_FOR_USER'] = '<label for="userid">Show URL\'s for user</label>:';
$lang['ANON_USERS'] = 'Anonymous Users';
$lang['YOU'] = '*';
$lang['NO_RECORDS_FOUND'] = '<i>(Nada)</i>';
$lang['EDIT'] = 'Edit';
$lang['ARCHIVE'] = 'Archive';
$lang['UNARCHIVE'] = 'Unarchive';
$lang['ANONIMIZE'] = 'Anonimize';

// edit page
// create page

$lang['SHORT_NOT_AUTH'] = 'User not authorised to create a custom short';
$lang['VALIDATION_ERROR'] = 'Validation Error';
$lang['DUPLICATION_ERROR'] = 'Duplication Error';
$lang['LONGURL_ALREADY_SHORT'] = 'Long URL "*|longurl|*" already maps to short "<code>*|shorturl|*</code>".';
$lang['SHORT_ALREADY_MAPPED'] = 'Short "<code>*|shorturl|*</code>" already exists and may not be used again.';
$lang['UNABLE_TO_GENERATE_SHORT'] = 'Unable to generate a randomized short at this time, please try later';
$lang['CREATED'] = 'Short Created';
$lang['SHORT_CREATED'] = 'Short created as \'*|short|*\'';
$lang['SHORT_CREATED_DESCRIPTIVE'] = array(
	'The short URL has been created.',
	'The URI you can use to access this service is:<br/><code>*|fullshorturl|*</code>',
	);

$lang['UNKNOWN_ACTION'] = 'Don\'t know what to do with that request';
$lang['INAPPROPRIATE'] = 'Inapropriate use of this page';
$lang['SUBMIT'] = 'Update';
$lang['RESET'] = 'Revert Edits';
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
$lang['FIELD_ANALYTICS'] = 'Google Analitics';
$lang['ON'] = '<span class="green">on</span>';
$lang['OFF'] = '<span class="red">off</span>';
$lang['EDITS_SAVED'] = 'Edits have been saved';
$lang['NOT_YOURS'] = 'The requested resource is not yours to modify';
$lang['EDITING_SHORTS_DANGEROUS'] = 'Changing Short Bit may break external links to this resource';
$lang['ROWS_AFFECTED'] = 'Changes made to the <b>*|rows|*</b> row(s)';

// delete page

$lang['DELETED'] = '\'*|title|*\' was archived';
$lang['UNDONE'] = '\'*|title|*\' was reactivated';
$lang['ANONYMIZED'] = '\'*|title|*\' was anonymized';

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

$lang['SEC_PREFIX'] = ' - in ';
$lang['SEC_SUFIX'] = ' sec';
$lang['REDIRECTING_TO'] = 'Redirecting to <a href="*|url|*">*|url|*</a>';

?>