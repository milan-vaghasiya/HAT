<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
|| Android Firebase Push Notification Configurations
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
 */

/*
|--------------------------------------------------------------------------
| Firebase API Key
|--------------------------------------------------------------------------
|
| The secret key for Firebase API
|
 */

//Jaydip
//$config['key'] = 'AAAAQrdyJA8:APA91bHyLDOeJNm2SJSODkWRfFp4aEH_0_-1wnp5dIH4hm_qWeC2Jx-BNrbDYNQYyx3bSk5WDEFIdwT01NjeojJPmKm1dILViOU55l8fARzkA4wBn7jeZpg4x9VKL17KAnOtyvOADvFx';

//Milan
//$config['key'] = 'AAAACZlrYos:APA91bHuGzWzIcOBL6Zm4sgZ7Cbhi0mRWk7dbpEgsDR2hLatqRxpjKSNpYY3VP1aTLH3vJqIiJEgIkDseO4wY3EOn8M3OyYvFWAS2DrgrkCZkUzthOi2aZ7bcQaIz6aorD0db35h9Se_';

//Nativebit
$config['key'] = 'AAAAoe6z20w:APA91bHiHMGAS4P-FGsZa_uvViAbAVcG_jCcx1RRHh2VLhpqAKx0Gcntw7v-Tjvtc7GvD4EZ_kYcfjpFEYm2OohMl5FB_xJ0CBXv4gN5S5mE9J4NZYDRO4SFPtvpbdcOzbTSRhYHtrNN';

/*
|--------------------------------------------------------------------------
| Firebase Cloud Messaging API URL
|--------------------------------------------------------------------------
|
| The URL for Firebase Cloud Messafing
|
 */

$config['fcm_url'] = 'https://fcm.googleapis.com/fcm/send';
