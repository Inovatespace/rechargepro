<?php
require 'gapi.class.php';
define('ga_profile_id','131435386');

$ga = new gapi("nextcashandcarry@nextcashandcarry-175412.iam.gserviceaccount.com", "key.p12");
//,,'users'''
//browser'browserVersion','pageviews','visits'
//$filter = 'start-date == 2017-07-30 && end-date == 2017-07-31';
$ga->requestReportData(ga_profile_id,array('browser'),array('pageviews','newUsers'),null,null,"2017-07-31","2017-07-31");
//$ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter,        $startDate, $endDate, $startIndex, $maxResults);

?>
total unique Visitors  <?php echo $ga->getnewUsers() ?><br />
----------------------------------------------------------------------------
Social media unique visitors
<?php
$ga->requestReportData(ga_profile_id,array('socialNetwork'),array('users'),null,null,"2017-07-29","2017-07-31");
?>

<table>
<tr>
  <th>Social Netwotk</th>
  <th>Users</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
  <td><?php echo $result->getUsers() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getUsers() ?></td>
</tr>
</table>

<br />
------------------------------------------------------------------------------------
Top keywords (non-branded)
<?php
$ga->requestReportData(ga_profile_id,array('keyword'),array('users','timeOnPage'),'-users',null,"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>keywords</th>
  <th>Users</th>
  <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getUsers() ?></td>
  <td><?php echo $result->getTimeOnPage() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getTimeOnPage() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getUsers() ?></td>
</tr>
</table>
<br />
------------------------------------------------------------------------------------
Top viewed pages
<?php
$ga->requestReportData(ga_profile_id,array('pagePath'),array('users'),'-users',null,"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
  <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getUsers() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getUsers() ?></td>
</tr>
</table>
<br />
------------------------------------------------------------------------------------
Site Search - Search Terms
<?php
$ga->requestReportData(ga_profile_id,array('searchKeyword'),array('searchUniques'),'-searchUniques',null,"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
  <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getSearchUniques() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getSearchUniques() ?></td>
</tr>
</table>
<br />
------------------------------------------------------------------------------------
Search Engines - Organic Search
<?php
$ga->requestReportData(ga_profile_id,array('source'),array('pageviews','sessionDuration','exits'),'-pageviews','medium==organic',"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
  <th>Time On page</th>
    <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getPageviews() ?></td>
    <td><?php echo $result->getSessionDuration() ?></td>
    <td><?php echo $result->getExits() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getPageviews() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getSessionDuration() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getExits() ?></td>
</tr>
</table>

------------------------------------------------------------------------------------
Top Landing Pages
<?php
$ga->requestReportData(ga_profile_id,array('landingPagePath'),array('entrances','bounces'),'-entrances',null,"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
  <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
  <td><?php echo $result->getEntrances() ?></td>
    <td><?php echo $result->getBounces() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getEntrances() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getBounces() ?></td>
</tr>
</table>
<br />
------------------------------------------------------------------------------------
User  by Country
<?php
$ga->requestReportData(ga_profile_id,array('country'),array('users'),'-users',null,"2017-07-29","2017-07-31",1,10);
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getUsers() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getUsers() ?></td>
</tr>
</table>

<br />
------------------------------------------------------------------------------------
30dayUsers
<?php
$ga->requestReportData(ga_profile_id,array('date'),array('30dayUsers'),'-date',null,"2017-07-01","2017-07-31");
?>

<table>
<tr>
  <th>pagePath</th>
  <th>Users</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->get30dayUsers() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->get30dayUsers() ?></td>
</tr>
</table>


Real Time users an pages visit


othrs by duration, result / page