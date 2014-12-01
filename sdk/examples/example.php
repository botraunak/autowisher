<?php
/*
	Developer: Raunak Sett
	Facebook: htttp://fb.me/botraunak
	Twiiter: http://twitter.com/botraunak
	Email: sett.raunak@gmail.com
	
	Dated: 06-06-2014
	Auto Wisher v1.0 
*/
require '../src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '414427155363670',
  'secret' => '3f7a653d230a3bf0075b28794cb7912a',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
	$access_token = $facebook->getAccessToken();
	echo $access_token.'<br />';
	} catch (FacebookApiException $e) {
    echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
  }
}
echo '<!DOCTYPE html>';
echo '<html xmlns:fb="http://www.facebook.com/2008/fbml">';
echo '<body>';
    if ($user) 
	{
      $user_posts = $facebook->api('/me/feed?fields=from,message,actions,created_time,id&limit=9999&since=1398321000&until=1398407400&access_token='.$access_token);
	  $bday_posts = $user_posts['data'];
	  echo '<pre>';
	  $end = false;
	 while(!$end) 
	 {
		foreach ($bday_posts as $post)
		{
			$unix_stamp = date("U",strtotime($post['created_time']));
			if ($unix_stamp >= 1398321000 )
			{
				echo '<p> From: '.$post['from']['name'].'</p>';
				echo '<p> Message: '.$post['message'].'</p>';
				echo '<p> Created time: '.$post['created_time'].'</p><br />';
				$post_id = $post['id'];
				$final = $facebook ->api('/'.$post_id.'/comments', 
								'post', 
							array(
								'access_token' => $access_token,
								'message' => 'Thanks '.$post['from']['name'].'!',
							)
				);
				$final2 = $facebook ->api('/'.$post_id.'/likes', 
								'post', 
							array(
								'access_token' => $access_token,
							)
				);
		
				
			}
			else
			{
				$end = true;
				break;
			}
		}
	  
		$link = $user_posts['paging']['next'];
		$next = file_get_contents($link);
	  
		$user_posts = json_decode($next, true);
		$bday_posts = $user_posts['data'];
	  }
      echo '</pre>';
	  echo '<img src="https://graph.facebook.com/'.$user.'/picture">';
	  echo '<a href = "'.$facebook->getLogoutURL(array(next=>'http://sarthak.heliohost.org/raubot/sdk/examples/')).'">LOGOUT</a>'; 
	} 
	else 
	{  
	$params = array(
					scope => 'read_stream,publish_actions,'
					);
	$loginurl = $facebook->getLoginURL($params);
	echo '<a href = "'.$loginurl.'">LOGIN</a>';      
    }
    echo '<div id="fb-root"></div>';
	

?>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
  </body>
</html>
