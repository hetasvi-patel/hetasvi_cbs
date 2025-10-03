<?php
global $_dbh;
    // generate stored procedure            
            
            $_detailprctype.='IN `TransactionMode` char(1)';
            //  $_prctype= substr_replace($_prctype,"",strlen($_prctype)-1);
 $droptransaction='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_detailtablename).'_transaction`;';
            $_generatetransaction_start='DELIMITER $$
            ';
$_detailgeneratetransaction.='CREATE PROCEDURE `'.str_replace("tbl_","",$_detailtablename).'_transaction`
(
        '.$_detailprctype.'
)
';
            $_detailgeneratetransaction.="
            BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_".$_detailfieldname[0]." = (SELECT COALESCE(MAX(".$_detailfieldname[0].'),0) + 1 FROM '.$_detailtablename.');

                    '; 
            // echo $_prvinsert."<br>";
            //$_prvinsert= substr_replace($_prvinsert,"",strlen($_prvinsert)-27);
            //$_prvinsertvalues= substr_replace($_prvinsertvalues,"",strlen($_prvinsertvalues)-27);
            $_detailprvinsert=substr(trim($_detailprvinsert),0,-1);
            $_detailprvinsertvalues=substr(trim($_detailprvinsertvalues),0,-1);          
            $_detailgeneratetransaction.='insert into '.$_detailtablename.'
                    (
                        '.$_detailprvinsert.'
                    )
                    values
                    ( 
                        '.$_detailprvinsertvalues.'
                    );
                '; 
                    $k=0;
                    for($i=0;$i< $_detailfieldno;$i++)
                    {
                        $k++;
                        $_detailprvupdate.= $_detailfieldname[$k]."=p_".$_detailfieldname[$k].",
                        ";
             
                    }
            //$_prvupdate=substr_replace($_prvupdate,"",strlen($_prvupdate)-27); 
            $_detailprvupdate=substr(trim($_detailprvupdate),0,-1);
            $_detailgeneratetransaction.="
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE ".$_detailtablename.'
                
                SET
                        '.$_detailprvupdate.'

                WHERE '.$_detailfieldname[0].'= p_'.$_detailfieldname[0].';
  
            ';
            $_detailgeneratetransaction.="ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  ".$_detailtablename.' WHERE '.$_detailfieldname[0].'= p_'.$_detailfieldname[0].';
            ';
            //  $_generatetransaction.='SELECT COALESCE(max('.$_fieldname[0].'),0)+ 1 into maxid from '.$_fieldname[0].' where '.$_fieldname[0].'='.$_prvar[0].';';
            // $_generatetransaction.='if(maxid=1) then delete from '.$_tablename.' where '.$_fieldname[0].'='.$_fieldname[0].'; end if;
            $_detailgeneratetransaction.='
            END IF;
        END';
 $_generatetransaction_end=' $$
DELIMITER ;';
$_dbh->exec($droptransaction);
//echo $_detailgeneratetransaction;
//exit;
$_dbh->exec($_detailgeneratetransaction);
            
            //$handle = fopen("../stored_procedures/".str_replace("tbl_","",$_detailtablename)."_transaction.txt", "w");
            //fwrite($handle,$_generatetransaction_start.$_detailgeneratetransaction.$_generatetransaction_end); 
            
            $dropfillmodel='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_detailtablename).'_fillmodel`;';
            $generatefillmodel_start='DELIMITER $$
            ';
             $generatefillmodel='CREATE PROCEDURE `'.str_replace("tbl_","",$_detailtablename).'_fillmodel`
            (IN `p_'.$_detailfieldname[0].'` '.$_detailtype[0].$_detailtypelen[0].')
            BEGIN
SELECT * 
       FROM `'.$_detailtablename.'` 
        WHERE '.$_detailfieldname[0].'= p_'.$_detailfieldname[0].';
        END';
 $generatefillmodel_end=' $$
DELIMITER ;';
            $_dbh->exec($dropfillmodel);
            $_dbh->exec($generatefillmodel);

            //$handle = fopen("../stored_procedures/".str_replace("tbl_","",$_detailtablename)."_fillmodel.txt", "w");
            //fwrite($handle,$generatefillmodel_start.$generatefillmodel.$generatefillmodel_end); 
            
            $dropsearch='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_detailtablename).'_search`;';
                
            $generatesearch='CREATE PROCEDURE `'.str_replace("tbl_","",$_detailtablename).'_search` (IN columns VARCHAR(255), IN tableName VARCHAR(255))
            BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END';
$generatesearch_end=' $$
DELIMITER ;';
//$_dbh->exec($dropsearch);
//$_dbh->exec($generatesearch);
                
            //$handle = fopen("../stored_procedures/".str_replace("tbl_","",$_tablename)."_search.txt", "w");
            //fwrite($handle,$generatesearch); 
?>