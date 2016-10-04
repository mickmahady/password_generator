<?php

function random_char($string) {
  $i = mt_rand(0, strlen($string)-1);
  return $string[$i];
}

function random_string($length, $char_set) {
  $output = '';
  for($i=0; $i < $length; $i++) {
    $output .= random_char($char_set); 
  }
  return $output;
}

function generate_password($options) {
  // define character sets
  $lower = 'abcdefghijklmnopqrstuvwxyz';
  $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $numbers = '0123456789';
  $symbols = '$*?!-';
  
  // extract configuration flags into variables
  $use_lower = isset($options['lower']) ? $options['lower'] : '0';
  $use_upper = isset($options['upper']) ? $options['upper'] : '0';;
  $use_numbers = isset($options['numbers']) ? $options['numbers'] : '0';;
  $use_symbols = isset($options['symbols']) ? $options['symbols'] : '0';;

  $chars = '';
  if($use_lower == '1') { $chars .= $lower; }
  if($use_upper == '1') { $chars .= $upper; }
  if($use_numbers == '1') { $chars .= $numbers; }
  if($use_symbols == '1') { $chars .= $symbols; }
  
  $length = isset($options['length']) ? $options['length'] : 8;
  
  return random_string($length, $chars);
}

$options = array(
  'length' => $_GET['length'],
  'lower' => $_GET['lower'],
  'upper' => $_GET['upper'],
  'numbers' => $_GET['numbers'],
  'symbols' => $_GET['symbols']
);

$generated_password = generate_password($options);

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

$password = $generated_password;
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
      background-color: red;
    }
    #meter div.rating-3, #meter div.rating-4 {
      background-color: orange;
    }
    #meter div.rating-5, #meter div.rating-6 {
      background-color: yellow;
    }
    #meter div.rating-7, #meter div.rating-8 {
      background-color: blue;
    }
    #meter div.rating-9, #meter div.rating-10 {
      background-color: green;
    }
    </style>
  </head>
  <body>  
    <p>Generate a new password using the form options.</p>
    <form action="" method="get">
      Length: <input type="text" name="length" value="<?php if(isset($_GET['length'])) { echo $_GET['length'] ? $_GET['length'] : 8 ; } ?>" /><br />
      <input type="checkbox" name="lower" value="1" <?php if($_GET['lower'] == 1) { echo 'checked'; } ?> /> Lowercase<br />
      <input type="checkbox" name="upper" value="1" <?php if($_GET['upper'] == 1) { echo 'checked'; } ?> /> Uppercase<br />
      <input type="checkbox" name="numbers" value="1" <?php if($_GET['numbers'] == 1) { echo 'checked'; } ?> /> Numbers<br />
      <input type="checkbox" name="symbols" value="1" <?php if($_GET['symbols'] == 1) { echo 'checked'; } ?> /> Symbols<br />
      <input type="submit" value="Submit" />
    </form>
    <p>Generated Password: <?php echo $generated_password; ?></p>
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

  </body>
</html>
