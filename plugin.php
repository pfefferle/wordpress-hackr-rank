<?php
/*
Plugin Name: hackr rank
Plugin URI:
Description: Be proud of your hackr rank
Version: 1337
Author: Matthias Pfefferle
Author URI: http://notizblog.org/
*/

function hackr_ranker( $atts ) {
  $json = get_hackr_json($atts['twitteruser']);
  
  if (!$json) {
    return "<p>no hackr-rank available</p>";
  }

  $badges = json_decode($json, true);
  $return = "<ul>";
  foreach ($badges["badges"] as $badge) {
    $return .= "<li style='clear: both;'>";
    $return .= "<img src='".$badge['badgeimg']."' alt='".$badge['badgename']."' style='float: right;' />";
    $return .= "<strong>".$badge['badgename']."</strong><br />";
    $return .= $badge['badgedescription'];
    $return .= "</li>";
  }
  $return .= "</ul>";
  
  return $return;
}

function get_hackr_json($twitteruser) {
  if (!$twitteruser) {
    return null;
  }
  
  if ($hackr_rank = get_transient("hackr_rank")) {
    return $hackr_rank;
  } else {
    $url = "http://hackrengine.appspot.com/api/badges?twitteruser=".$twitteruser;
    $response = wp_remote_request($url);

    if ($response['response']['code'] >= 400) {
      return null;
    }
    
    set_transient("hackr_rank", $response['body'], 86400);
    
    return $response['body'];
  }
}

add_shortcode( 'hackr-rank', 'hackr_ranker' );