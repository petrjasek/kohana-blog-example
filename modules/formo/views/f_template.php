<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="keywords" content="utah web design, utah, salt lake city, web firm, utah search engine optimization, utah seo" />
<meta name="Description" content="Avant Hill Design is one of Utah's cutting-edge web design and development companies." />
<link href='<?=url::base()?>modules/formo/assets/css/formo.css' rel='stylesheet' type='text/css' />
<?php if ($scripts):?>
<script type="text/javascript" src="<?=url::base()?>modules/formo/assets/js/jquery.js"></script>
<script type="text/javascript" src="<?=url::base()?>modules/formo/assets/js/jquery.form.js"></script>
<script type="text/javascript" src="<?=url::base()?>modules/formo/assets/js/jquery.autocomplete.js"></script>
<?php endif; ?>
<title><?=$title?></title>
</head>

<body>
<div id="header">
	<div id='inside_head'>
		<div id='logo'>
			<a href='http://www.avanthill.com/formo'><img src="<?=url::base()?>modules/formo/assets/img/logo.gif" alt="Avant Hill Design" /></a>
		</div>
		<div id='menu'>
			<ul>
			</ul>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div class="container padded" id="hcon">
	<?=$header?>
	<div id='hbox'>
		<?=$content?>
	</div>
</div>

<div id="footer">
<div id="inside_footer">
<p>
</p>
<p>Copyright &copy; 2008 Avant Hill Design. All rights reserved.</p>
</div>
</div> <!-- #footer-->
</body>

</html>