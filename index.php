<?php

/*
	Developer: Raunak Sett
	Facebook: htttp://fb.me/botraunak
	Twiiter: http://twitter.com/botraunak
	Email: sett.raunak@gmail.com
	
	Dated: 06-06-2014
	Auto Wisher v1.0 
*/

require 'config.php';
echo '<!DOCTYPE html>';
echo '<html xmlns:fb="http://www.facebook.com/2008/fbml">';
echo '<link href="style.css" rel="stylesheet">';
echo '<main>';
if ($user) 
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST' &&!empty($_POST['bdaystrt']) && !empty($_POST['tmzcrr']))
	{
		$timecorrect = $_POST['tmzcrr']*3600;
		$since_time = strtotime($_POST['bdaystrt']) + $timecorrect; //Birthday Start time - timezone adjustments
		$until_time = $since_time + strtotime("+ 1 day") - strtotime("now"); //Birthday End time - timezone adjustments
		$postsliked = 0;
		
		
		// Getting user posts
		$user_posts = $facebook->api('/me/feed?fields=from,created_time,id&limit=9999&since='.$since_time.'&until='.$until_time.'&access_token='.$access_token);
		$bday_posts = $user_posts['data'];
		echo '<pre>';
		$end = false;
		while(!$end) 
		{
			foreach ($bday_posts as $post)
			{
				$unix_stamp = date("U",strtotime($post['created_time']));
				if ($unix_stamp >= $since_time )
				{
					$post_id = $post['id'];
					$url = "https://graph.facebook.com/".$post_id."/comments";
					$res = getPostData($url, array("access_token"=> $access_token, "message" => "Thanks ".$post['from']['name']."!"));
					$res = json_decode($res);
					if(!empty($res->id)) $postsliked++;
				}
				else
				{	
					$end = true;
					break;
				}
			}
			// Pagination for next posts
			if(!$end)
			{
				$link = $user_posts['paging']['next'];
				$next = file_get_contents($link);
				$user_posts = json_decode($next, true);
				$bday_posts = $user_posts['data'];
			}
		}
		echo '</pre>';
		
		echo '<section class="card">
		<img src="https://graph.facebook.com/'.$user.'/picture?type=large" width = "100px" height = "100px" style="border-radius: 9000px;vertical-align:middle;">
		<span style="font-weight: 100;">
		<strong style="font-size:25px;">'.$postsliked.' </strong>
		posts were liked and were thanked. </span><h1> 
		<a id="logout-button" href = "'.$facebook->getLogoutURL(array(next=>'http://sarthak.heliohost.org/raubot/')).'">LOGOUT</a> </h1>'; 
	}
	else
	{
		echo '<form action="index.php" method="POST">';
		echo '<input type="text" name = "bdaystrt" placeholder= "Birthday Start eg input 10 April 2014"> </input> ';
		echo '<select name="tmzcrr">
      <option value="">--Select Timezone--</option>
      
	  <option value="+12.0">(GMT -12:00) Eniwetok, Kwajalein</option>
      <option value="+11.0">(GMT -11:00) Midway Island, Samoa</option>
      <option value="+10.0">(GMT -10:00) Hawaii</option>
      <option value="+9.0">(GMT -9:00) Alaska</option>
      <option value="+8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
      <option value="+7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
      <option value="+6.0">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
      <option value="+5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
      <option value="+4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
      <option value="+3.5">(GMT -3:30) Newfoundland</option>
      <option value="+3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
      <option value="+2.0">(GMT -2:00) Mid-Atlantic</option>
      <option value="+1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
      <option value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
      <option value="-1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
      <option value="-2.0">(GMT +2:00) Kaliningrad, South Africa</option>
      <option value="-3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
      <option value="-3.5">(GMT +3:30) Tehran</option>
      <option value="-4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
      <option value="-4.5">(GMT +4:30) Kabul</option>
      <option value="-5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
      <option value="-5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
      <option value="-5.75">(GMT +5:45) Kathmandu</option>
      <option value="-6.0">(GMT +6:00) Almaty, Dhaka, Colombo</option>
      <option value="-7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
      <option value="-8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
      <option value="-9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
      <option value="-9.5">(GMT +9:30) Adelaide, Darwin</option>
      <option value="-10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
      <option value="-11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
      <option value="-12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
</select>';
		echo '<input type="submit" value ="Wish Everyone">';
		echo '</form>';
	}
}
	else 
	{  
	$params = array(
					scope => 'read_stream,publish_actions,'
					);
	$loginurl = $facebook->getLoginURL($params);
	// echo '<a href = "'.$loginurl.'">LOGIN</a>';   
	echo  '<section class="card"><h1><strong>Auto Wisher!</strong> to thank all your facebook friends for their wishes at once!</h1>
  <h2>-Raunak Sett</h2>
  
<div class="social-wrap c">

    <a class="zocial facebook" type="submit" href="'.$loginurl.'" >Sign in with Facebook</a>
	</div>
  
</section>';
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
  </main>
</html>
