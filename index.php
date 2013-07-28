<?php
/* 
	Twitter Feed using OAuth & API 1.1 
	
	This script is based on work by Kris Coverdale <br>
   (http://itsnotrocketscience.nfshost.com/2013/06/18/php-integration-with-twitter-api-1-1/)
	
	I have repuposed the script and added a few odds and ends. 
	Please use as you like, and spread it around as you see fit. 

*/

include('twitteroauth/OAuth.php');
include('twitteroauth/twitteroauth.php');

function getTweets($twitteruser) { 
    // Set number of tweets
	$notweets = 4;
include('inc/config.php');	
      
    function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
      $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
      return $connection;
    }
       
    $connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
    $tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
     
    return ($tweets);
}
	// Determin Twitter Time

function twitter_time($a) {
    //get current timestampt
    $b = strtotime("now"); 
    //get timestamp when tweet created
    $c = strtotime($a);
    //get difference
    $d = $b - $c;
    //calculate different time values
    $minute = 60;
    $hour = $minute * 60;
    $day = $hour * 24;
    $week = $day * 7;
        
    if(is_numeric($d) && $d > 0) {
        //if less then 3 seconds
        if($d < 3) return "right now";
        //if less then minute
        if($d < $minute) return floor($d) . " seconds ago";
        //if less then 2 minutes
        if($d < $minute * 2) return "about 1 minute ago";
        //if less then hour
        if($d < $hour) return floor($d / $minute) . " minutes ago";
        //if less then 2 hours
        if($d < $hour * 2) return "about 1 hour ago";
        //if less then day
        if($d < $day) return floor($d / $hour) . " hours ago";
        //if more then day, but less then 2 days
        if($d > $day && $d < $day * 2) return "yesterday";
        //if less then year
        if($d < $day * 365) return floor($d / $day) . " days ago";
        //else return more than a year
        return "over a year ago";
    }
}

$tweets = getTweets($twitteruser);
foreach ($tweets as $line){
    $status = $line->text;
    $tweetTime =  $line->created_at;
    $tweetId = $line->id_str;
	$Timage = $line->user->profile_image_url;
	$twitteruser = $line->user->screen_name;
	$name = $line->user->name;
	$screen_name = $line->screen_name;
	$about = $line->user->description;
	$status = preg_replace('%(http://([a-z0-9_.+&!#~/,\-]+))%i','<a href="http://$2">$1</a>',$status);
  	$status = preg_replace('/@([a-z0-9_]+)/i','<a href="http://twitter.com/$1">@$1</a>',$status);
  	$status = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1#<a href="http://search.twitter.com/search?q=%23\2">\2</a>', $status);
    $outputTweet .= '<div class="tweets">

<a href="http://twitter.com/'.$twitteruser.'" title="'.$twitteruser.' on Twitter" target="_blank"><img src="'.$Timage.'" align="left" alt="@'.$screen_name.' twitter avatar" border="0"> '.$twitteruser.'</a>: '.$status.'</span> <a style="font-size:85%" href="http://twitter.com/'.$twitteruser.'/statuses/'.$tweetId.'">'. twitter_time($tweetTime) .'</a></div>';
   $name = $line->user->name;
}


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Twitter Feed</title>
<style>

.tweets {
	width: 200px;
	height: 115px;
	padding: 4px;
	float: left;
	background-color: #FFF;
	margin-right: 4px;
	margin-left: 4px;
	font-family: Arial, Helvetica, sans-serif;
	color: #666;
	font-size: 12px;
	border: thin dashed #666;
}
.tweets img{
	padding: 2px;	
	
}
a:link {
	color: #C00;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #03F;
}
a:hover {
	text-decoration: underline;
	color: #03F;
}
a:active {
	text-decoration: none;
	color: #03F;
}
</style>
</head>

<body>
<h2><a href="http://twitter.com/<?php echo $twitteruser; ?>" title="<?php echo '@'.$twitteruser.' on Twitter';  ?>"><?php echo $name; ?> Twitter Feed</a></h2>
<p><?php echo $about; ?></p>
<?php echo $outputTweet; ?>   
</body>
</html>
