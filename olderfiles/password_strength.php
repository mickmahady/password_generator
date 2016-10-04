<?php

function detect_any_uppercase($string) {
  // true if lowercasing changes string
  return strtolower($string) != $string;
}

function detect_any_lowercase($string) {
  // true if uppercasing changes string
  return strtoupper($string) != $string;
}

function count_numbers($string) {
  return preg_match_all('/[0-9]/', $string);
}

function count_symbols($string) {
  // You have to decide which symbols count
  // Regex \W is any non-letter, non-number: too broad
  // Better to list the ones that count
  return preg_match_all('/[!@#$%^&*-_+=?]/', $string);
}


function password_strength($password) {
  $strength = 0;
  $possible_points = 12;
  $length = strlen($password);
  
  if(detect_any_uppercase($password)) {
    $strength += 1;
  }
  if(detect_any_lowercase($password)) {
    $strength += 1;
  }
  
  $strength += min(count_numbers($password), 2);
  $strength += min(count_symbols($password), 2);
  
  if($length >= 8) {
    $strength += 2;
    $strength += min(($length -8) * 0.5, 4);
  }
  
  $strength_percent = $strength / (float) $possible_points;
  $rating = floor($strength_percent * 10);
  return $rating;
}

$password = $_POST['rate'];
$rating = password_strength($password);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Password Strength Meter</title>
    <style>
    #meter div {
      height: 20px; width: 20px;
      margin: 0 1px 0 0; padding: 0;
      float: left;
      background-color: #DDDDDD;
    }
    #meter div.rating-1, #meter div.rating-2 {
      background-color: green;
    }
    #meter div.rating-3, #meter div.rating-4 {
      background-color: blue;
    }
    #meter div.rating-5, #meter div.rating-6 {
      background-color: yellow;
    }
    #meter div.rating-7, #meter div.rating-8 {
      background-color: orange;
    }
    #meter div.rating-9, #meter div.rating-10 {
      background-color: red;
    }
    </style>
  </head>
  <body>

    <p>Your password rating is: <?php echo $rating; ?>

    <div id="meter">
      <?php
      for($i=0; $i < 10; $i++) {
        echo "<div";
        if($rating >= $i) {
          echo " class=\"rating-{$rating}\"";
        }
        echo "></div>";
      }
      ?>
    </div>
    
    <br style="clear: both;" />

    <p>Rate the strength of your password:</p>
    <form action="" method="post">
      Password: <input type="text" name="rate" value="" /><br />
      <input type="submit" value="Submit" />
    </form>

  </body>
</html>
