<link rel="stylesheet" href="/theme/classic/pages/developer/pretty/styles/codepen-embed.css"/>
<script src="/theme/classic/pages/developer/pretty/highlight.pack.js"></script>

<?php
	$request = htmlentities($_REQUEST['i']);
    
    include "sample/".$request.".php";
?>

<script>
//hljs.initHighlightingOnLoad();


$(document).ready(function() {
   $('pre code').each(function(i, e) {hljs.highlightBlock(e)});
});

</script>

