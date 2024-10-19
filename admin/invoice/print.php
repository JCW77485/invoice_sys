<?php 
require ('../../config.php');
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<meta charset="utf-8" />
<?php include "../inc/header.php" ?>
<body>
<?php
$type = isset($_GET['type']) ? $_GET['type'] : 1 ;
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `invoice_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
$tax_rate = isset($tax_rate) ? $tax_rate : $_settings->info('tax_rate');
?>
<style>
table th, table td{
	padding:5px 3px!important;
}

.container {
    display: flex;
}

.box {
     /* Adjust width as needed */
    text-align: left;
    box-sizing: border-box;
}

@media print {
    @page {
        margin: 0; /* Removes margins to prevent extra space */
        /* size: auto; Sets the page size automatically */
    }

    body {
        margin: 40px 50px 10px 50px; /* Add some margin to the content if necessary */
    }

    /* Optionally hide certain elements like navigation or banners */
    header, footer, nav, .no-print {
        display: none;
    }
}

</style>
<!-- <h1 class="text-center"><b>INVOICE</b></h1> -->
<div class='container'>
    <div class='box ' style='width: 70%;'>
        <h1 style=' font-family: "KaiTi", serif;'><b>順記貿易有限公司</b></h1>
        <h3 ><b>SOON KEE THREE TRADING SDN. BHD.</b></h3>
        <div style="font-size:15px">LOT IT 960 KILANG IERABOT KG BARU SUNGAI BULOH</div>
        <div style="font-size:15px">47000 SUNGAI BULOH SELANGOR MALAYSIA</div>
        <div style="font-size:15px">TEL: +(603)61506966 +(603)61308968</div>
        <div style="font-size:15px">EMAIl: st@soonkv.my</div>
    </div>
    <div class='box ' style='width: 30%;'>
        <img src="<?php echo validate_image($_settings->info('logo')) ?>" class="img-thumbnail" style="height:172px;width:175px;object-fit:contain" alt="">
    </div>
</div>
<hr>
<div class='container'>
    <div class='box ' style='width: 60%;'>
        <p>Bill To: <?php echo $customer_name ?></p>
        <!-- <p>Attn: </p> -->
        <p>Tel: +60 17 - 3092292</p>
        <!-- <p>Fax: </p> -->
    </div>
    <div class='box ' style='width: 40%;'>
        <p><b>Invoice Code:</b> <?php echo $invoice_code ?></p>
        <p><b>Billing Date:</b> <?php echo date_format(date_create($date_created),'d/m/Y') ?></p>
    </div>
</div>
<hr>
<table class="table table-bordered">
    <colgroup>
        <col width="5%">
        <col width="40%">
        <col width="10%">
        <col width="10%">
        <col width="20%">
        <col width="20%">
    </colgroup>
    <thead>
        <tr>
            <th class="text-center" >Item</th>
            <th class="text-center" >Description</th>
            <th class="text-center" >Qty</th>
            <th class="text-center" >Unit</th>
            <th class="text-center" >Cost</th>
            <th class="text-center" >Total</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if($type == 1)
            $items = $conn->query("SELECT i.*,p.description,p.id as pid,p.product as `name`,p.category_id as cid FROM invoices_items i inner join product_list p on p.id = i.form_id where i.invoice_id = '{$id}' ");
        else
            $items = $conn->query("SELECT i.*,s.description,s.id as `sid`,s.`service` as `name`,s.category_id as cid FROM invoices_items i inner join service_list s on s.id = i.form_id where i.invoice_id = '{$id}' ");
        
        $i = 1;
        while($row=$items->fetch_assoc()):
            $category = $conn->query("SELECT * FROM `category_list` where id = {$row['cid']}");
            $cat_count = $category->num_rows;
            $res = $cat_count > 0 ? $category->fetch_assoc(): array();
            $cat_name = $cat_count > 0 ? $res['name'] : "N/A";
            $description = stripslashes(html_entity_decode($row['description']));
        ?>
        <tr>
            <td class="text-center"><?php echo $i++ ?></td>
            <td class="">
            <p class="m-0"><small><?php echo $row['name'] ?></small></p>
            </td>
            <td class="text-center"><?php echo $row['quantity'] ?></td>
            <td class="text-center"><?php echo $row['unit'] ?></td>
            <td class="text-right"><?php echo number_format((float)$row['price'], 2, '.', '') ?></td>
            <td class="text-right"><?php echo number_format((float)$row['total'], 2, '.', '') ?></td>
        </tr>
        <?php endwhile; ?>
        <?php for ($x = $i; $x <= 20; $x++) { ?>
        
            <tr style="height: 35px">
            <td class="text-center"> </td>
            <td class="">
            <p class="m-0"><small> </small></p>
            </td>
            <td class="text-center"> </td>
            <td class="text-center"> </td>
            <td class="text-right"> </td>
            <td class="text-right"> </td>
        </tr>

        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="bg-foot">
            <th class="text-right" colspan="5">Sub Total</th>
            <th class="text-right" id="sub_total"><?php echo number_format((float)$sub_total, 2, '.', '') ?></th>
        </tr>
        <!-- <tr class="bg-foot">
            <th class="text-right" colspan="5">Tax Rate</th>
            <th class="text-right" id="tax_rate"><?php echo $tax_rate ?>%</th>
        </tr>
        <tr class="bg-foot">
            <th class="text-right" colspan="5">Tax</th>
            <th class="text-right" id="tax"><?php echo number_format((float)$sub_total * ($tax_rate/100), 2, '.', '' ) ?></th>
        </tr> -->
        <tr class="bg-foot">
            <th class="" colspan="4"><?php echo 'RINGGIT MALAYSIA '.convertAmountToWords($total_amount).' ONLY' ?></th>
            <th class="text-right">Grand Total (RM)</th>
            <th class="text-right" id="gtotal"><?php echo number_format((float)$total_amount, 2, '.', '') ?></th>
        </tr>
    </tfoot>
</table>
<hr>

<div class='container'>
    <div class='box ' style='width: 50%;'>
        
        <span style='font_size:10px'>Notes: All cheques should be crossed and made payment to:</span> 
        <h5><b><?php echo $_settings->info('bank_holder')?></b></h5>
        <h5><b><?php echo $_settings->info('bank') . ' A/C:'. $_settings->info('bank_acc')?></b></h5>
        <?php echo 'Email payment receipt to: '.$_settings->info('email')?>
        <br><br><br><br>
        <p style="margin: 0;">__________________________________________________</p>
        <p>Authorised Signature</p>
        <?php echo $_settings->info('bank_holder')?>
    </div>
    <div class='box ' style="width: 50%;text-align: right;">
        <span  style='font_size:10px'>Received the above goods in Good Order and Conditions</span>
        <br><br><br><br>
        <br><br><br>
        <div style="height:10px"></div>
        <p style="margin: 0;">__________________________________________________</p>
        Receipint's Signature & Company Stamp
    </div>
</div>

</body>
<?php
function convertAmountToWords($number) {
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
    );

    if (!is_numeric($number)) {
        return false;
    }

    if ($number < 0) {
        return strtoupper($negative . convertAmountToWords(abs($number)));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= '  ' .convertAmountToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convertAmountToWords($numBaseUnits) . '  ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convertAmountToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $fraction = str_pad($fraction, 2, '0', STR_PAD_RIGHT); // Ensure two decimal places for "Sen"
        $string .= '  AND ';
        $string .= convertAmountToWords((int)$fraction) . ' SEN';
    }

    return strtoupper($string);
}
?>
</html>