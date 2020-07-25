<?php
include "../../../engine.autoloader.php";


$credit = "";
$debit = "";
$row = $engine->db_query("SELECT SUM(amount) AS am, transaction_type FROM bank_alert GROUP BY transaction_type",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){

if($row[$dbc]['transaction_type'] == "CREDIT"){
$credit = $row[$dbc]['am'];}

if($row[$dbc]['transaction_type'] == "DEBIT"){
    $debit = $row[$dbc]['am'];}

   }
?>
<div class="pie pie1" style="background-color: #DD1111;">
    <div class="title">TOTAL CREDIT</div>
    <div class="outer-right mask">
        <div class="inner-right"></div>
    </div>

    <div class="outer-left mask">
        <div class="inner-left"></div>
    </div>
    <div class="content">
        <span><?php echo $engine->toMoney($credit);?></span>
    </div>
    <div class="arrow" id="arrow1"></div>
</div>

<div class="pie pie1" style="background-color: #223F9F;">
    <div class="title">TOTAL DEBIT</div>
    <div class="outer-right mask">
        <div class="inner-right"></div>
    </div>

    <div class="outer-left mask">
        <div class="inner-left"></div>
    </div>
    <div class="content">
        <span><?php echo $engine->toMoney($debit);?></span>
    </div>
    <div class="arrow" id="arrow2"></div>
</div>


<?php
$wave = 0;
$row = $engine->db_query("SELECT SUM(amount) AS am FROM bank_alert WHERE transaction_type = 'CREDIT' AND naration LIKE ?",array("%Rave Settlemen%"));
$wave = $row[0]['am'];
?>
<div class="pie pie1" style="background-color: #3C7628;">
    <div class="title">CREDIT WAVE</div>
    <div class="outer-right mask">
        <div class="inner-right"></div>
    </div>

    <div class="outer-left mask">
        <div class="inner-left"></div>
    </div>
    <div class="content">
        <span><?php echo $engine->toMoney($wave);?></span>
    </div>
    <div class="arrow" id="arrow3"></div>
</div>


<?php
$nibbs = 0;
$row = $engine->db_query("SELECT SUM(amount) AS am FROM bank_alert WHERE transaction_type = 'CREDIT' AND naration LIKE ?",array("%NIBSSPAY Plus Fee%"));
$nibbs = $row[0]['am'];
?>
<div class="pie pie1" style="background-color: #BA8A30;">
    <div class="title">CREDIT NIBBS</div>
    <div class="outer-right mask">
        <div class="inner-right"></div>
    </div>

    <div class="outer-left mask">
        <div class="inner-left"></div>
    </div>
    <div class="content">
        <span><?php echo $engine->toMoney($nibbs);?></span>
    </div>
    <div class="arrow" id="arrow4"></div>
</div>


<?php
$charge = 0;
$row = $engine->db_query("SELECT SUM(amount) AS am FROM bank_alert WHERE transaction_type = 'DEBIT' AND naration LIKE ?",array("%NIP Charge%"));
$charge = $row[0]['am'];
?>
<div class="pie pie1" style="background-color: #C01462;">
    <div class="title">BANK CHARGE</div>
    <div class="outer-right mask">
        <div class="inner-right"></div>
    </div>

    <div class="outer-left mask">
        <div class="inner-left"></div>
    </div>
    <div class="content">
        <span><?php echo $engine->toMoney($charge);?></span>
    </div>
    <div class="arrow" id="arrow5"></div>
</div>