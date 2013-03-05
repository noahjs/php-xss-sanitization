<?php 
  
  if( isset($_POST['val']) AND $_POST['val'] ){  
    $val =  $_POST['val'];
    include('xss_helper.php');
  }else{
    $val = false;
  }
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
 "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TEST of XSS Helper</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>

<body>

  <h1>Type something</h1>

  <form method="POST" action="test.php">
    <textarea name="val" rows=5 cols=50><?= $val; ?></textarea>
    <br>
    <input type="submit" value="Send it!">
  </form>

<?php if( $val ){ ?>

  <h2>url</h2>
  <a href="<?=s($val, 'url');?>"><?=s($val, 'url');?></a>
  
  <img src="<?=s($val, 'url');?>" />

  <br>

  <h2>rawurl</h2>
  <a href="<?=s($val, 'rawurl');?>"><?=s($val, 'rawurl');?></a>

  <br>

  <h2>attr</h2>
  <span title="<?=s($val, 'attr');?>" >SPAN TAG</span>
  <input value="<?=s($val, 'attr');?>" />

  <br>

  <h2>javascript or script</h2>
<script>

function do_something( variable ){
  return variable;
}

var variable = '<?=s($val, 'script');?>';

do_something( variable );

</script>

  <br>

  <h2>html</h2>
  <div><?=s($val, 'textarea');?></div>

  <br>

  <h2>storedhtml</h2>
  <div><?=s($val, 'storedhtml');?></div>

  <br>

  <h2>textarea</h2>
  <textarea><?=s($val, 'textarea');?></textarea>

  <br>

  <h2>text, also the default </h2>
  <div><?=s($val, 'text');?></div>

  <br>

<?php } ?>


</body>
</html>
