<h1>Test page</h1>

<h2>Important script variables</h2>
<pre>REQUEST_URI: <?php echo $_SERVER['REQUEST_URI']?></pre>
<pre>SCRIPT_NAME: <?php echo $_SERVER['SCRIPT_NAME']?></pre>
<pre>SCRIPT_FILENAME: <?php echo $_SERVER['SCRIPT_FILENAME']?></pre>
<pre>PATH_INFO: <?php echo $_SERVER['PATH_INFO']?></pre>
<pre>PHP_SELF: <?php echo $_SERVER['PHP_SELF']?></pre>

<h2>Contents of $_SERVER</h2>
<pre><?php var_export($_SERVER)?></pre>

<h2>Contents of $_ENV</h2>
<pre><?php var_export($_ENV)?></pre>