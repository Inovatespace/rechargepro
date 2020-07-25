<?php

include("config.inc.php");

ini_set("max_execution_time",1);
$request = Request::getInstance();

Checksum::isValidFromTo("2009-02-01","2009-12-31");

if(
  (
    $request->hasAction() and
    $request->hasParam() and
    Checksum::proof(
      $request->getAction(),
      $request->getParam(),
      $_SERVER["HTTP_REFERER"],
      $request->getChecksum()
    )
  )

) {

  echo "<html><head><title>Test >Checksum</title></head><body>
<h1>Success</h1>
<p>
	This checksum works from 2009-02-01 until 2009-12-31.
	The link is only reachable from the refering adress
	http://www.phpclasses.org/browse/package/5144.html
</p>
<p>
If you try to access
this page from another browser tab or browser directly,
then you will be automatically redirected to
http://www.phpclasses.org/browse/package/5144.html.
</p>
<p>
Only a browser reload avoids a redirect. {".strftime("%Y-%m-%d %X",time())."}

</p>

<pre style=\"padding:20px; outline:1px solid #ccc;\">

\$chksm = Checksum::build(\"show\",\"me\",\"http://www.phpclasses.org/browse/package/5144.html\");

Checksum::isValidFromTo(\"2009-02-01\",\"2009-12-31\");
if(Checksum::proof(
      \$_GET['act'],
      \$_GET['prm'],
      \$_SERVER['HTTP_REFERER'],
      \$_GET['csm']
    )){
  echo 'success';
} else {
  echo 'failure';
}
</pre>
</body></html>";

} else {
	header("Location:http://www.phpclasses.org/browse/package/5144.html");
}
