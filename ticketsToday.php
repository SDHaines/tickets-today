#!/usr/local/bin/php
<?php

/* 
 * This script queries the database for information about sales tickets
 * created today. It runs from cron every 5 minutes with the output piped
 * to a log file. The standard backup of the sql server database is 
 * performed once per hour. In the event of a catastrophic failure
 * we can restore to within an hour and then use this log to recreate
 * sales that would otherwise be lost with the just the standard backup. 
 */

require_once '/usr/local/valCommon/Counterpoint.php';

/* 
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
*/

$tsql = "SELECT
dbo.VI_PS_DOC_HDR.TKT_DT,
dbo.VI_PS_DOC_HDR.TKT_NO,
dbo.VI_INV_COMMIT_TRX.ITEM_NO,
dbo.IM_ITEM.ADDL_DESCR_1,
dbo.VI_INV_COMMIT_TRX.QTY_COMMIT AS QTY_SOLD,
--dbo.IM_INV.QTY_AVAIL
dbo.VI_PS_DOC_HDR.HDR_DISC_COD,
dbo.PS_DOC_PMT.PAY_COD,
dbo.PS_DOC_PMT.AMT
FROM VI_PS_DOC_HDR
INNER JOIN dbo.VI_INV_COMMIT_TRX ON dbo.VI_INV_COMMIT_TRX.DOC_NO = dbo.VI_PS_DOC_HDR.TKT_NO
INNER JOIN dbo.IM_ITEM ON dbo.IM_ITEM.ITEM_NO = dbo.VI_INV_COMMIT_TRX.ITEM_NO
INNER JOIN dbo.IM_INV ON dbo.IM_INV.ITEM_NO = dbo.VI_INV_COMMIT_TRX.ITEM_NO
INNER JOIN dbo.PS_DOC_PMT ON dbo.PS_DOC_PMT.TKT_NO = dbo.VI_PS_DOC_HDR.TKT_NO AND dbo.PS_DOC_PMT.PMT_LIN_TYP = 'T'
ORDER BY dbo.VI_PS_DOC_HDR.TKT_NO";

print "---------------------------- " . date("Y-m-d H:m:s") . " ----------------------------\n";
print "TKT_DT,TKT_NO,ITEM_NO,ADDL_DESCR_1,QTY_SOLD,HDR_DISC_CODE,PAY_COD,AMT\n";

$status = counterpointQuickQuery( $tsql, "printRow" );

function printRow ( $row ){
    print $row['TKT_DT']->format( "Y-m-d H:m:s" ) . "," .
    $row['TKT_NO'] . "," . 
    $row['ITEM_NO'] . "," .
    "\"" . $row['ADDL_DESCR_1'] . "\"" . "," .
    sprintf( "%d", $row['QTY_SOLD'] ) . "," .
    $row['HDR_DISC_COD'] . "," .
    $row['PAY_COD'] . "," . 
    $row['AMT'] . 
    "\n";
}
print "-----------------------------------------------------------------------------\n";
?>