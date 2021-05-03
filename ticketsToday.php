#!/usr/local/bin/php
<?php

require_once '/usr/local/valCommon/Counterpoint.php';

$tsql = "SELECT
dbo.VI_PS_DOC_HDR.TKT_DT,
dbo.VI_PS_DOC_HDR.TKT_NO,
dbo.VI_INV_COMMIT_TRX.ITEM_NO,
dbo.IM_ITEM.ADDL_DESCR_1,
dbo.VI_INV_COMMIT_TRX.QTY_COMMIT AS QTY_SOLD,
--dbo.IM_INV.QTY_AVAIL
dbo.VI_PS_DOC_HDR.HDR_DISC_COD,
dbo.PS_DOC_PMT.PAY_COD
FROM VI_PS_DOC_HDR
INNER JOIN dbo.VI_INV_COMMIT_TRX ON dbo.VI_INV_COMMIT_TRX.DOC_NO = dbo.VI_PS_DOC_HDR.TKT_NO
INNER JOIN dbo.PS_DOC_PMT ON dbo.PS_DOC_PMT.TKT_NO = dbo.VI_PS_DOC_HDR.TKT_NO
INNER JOIN dbo.IM_ITEM ON dbo.IM_ITEM.ITEM_NO = dbo.VI_INV_COMMIT_TRX.ITEM_NO
INNER JOIN dbo.IM_INV ON dbo.IM_INV.ITEM_NO = dbo.VI_INV_COMMIT_TRX.ITEM_NO
ORDER BY dbo.VI_PS_DOC_HDR.TKT_NO";

print "---------------------------- " . date("Y-m-d H:m:s") . " ----------------------------\n";
print "TKT_DT,TKT_NO,ITEM_NO,ADDL_DESCR_1,QTY_SOLD,HDR_DISC_CODE,PAY_COD\n";

$status = counterpointQuickQuery( $tsql, "printRow" );

function printRow ( $row ){
    print $row['TKT_DT']->format( "Y-m-d H:m:s" ) . "," .
    $row['TKT_NO'] . "," . 
    $row['ITEM_NO'] . "," .
    "\"" . $row['ADDL_DESCR_1'] . "\"" . "," .
    sprintf( "%d", $row['QTY_SOLD'] ) . "," .
    $row['HDR_DISC_COD'] . "," .
    $row['PAY_COD'] . "\n";
}
print "-----------------------------------------------------------------------------\n";
?>