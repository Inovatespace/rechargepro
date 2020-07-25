<script type="text/javascript">
function get_invoice(){
 window.location = "/invoice&id="+$("#in").val();
}
</script>

<div style="padding: 20px;" class="profilebg">

<div style="font-size: 140%; margin-bottom:10px; margin-top:-20px;">Reprint Receipt</div>

e.g 23_2334, 134 4546
<input type="text" id="in" placeholder="Invoice Number" style="padding: 10px; width: 100%;;" class="input" />
<button class="mainbg" onclick="get_invoice()" style="border:none; width:100%; margin-top:5px; color:white; padding: 5px; cursor: pointer;">PRINT</button>
</div>