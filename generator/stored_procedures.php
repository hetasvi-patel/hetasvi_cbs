<?php
global $_dbh;
    // generate stored procedure            
            
            $_prctype.='IN `TransactionMode` char(1)';
            //  $_prctype= substr_replace($_prctype,"",strlen($_prctype)-1);
 $droptransaction='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_tablename).'_transaction`;';
            $_generatetransaction_start='DELIMITER $$
            ';
$_generatetransaction.='CREATE PROCEDURE `'.str_replace("tbl_","",$_tablename).'_transaction`
(
        '.$_prctype.'
)
';
            $_generatetransaction.="
            BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_".$_fieldname[0]." = (SELECT COALESCE(MAX(".$_fieldname[0].'),0) + 1 FROM '.$_tablename.');

                    '; 
            // echo $_prvinsert."<br>";
            //$_prvinsert= substr_replace($_prvinsert,"",strlen($_prvinsert)-27);
            //$_prvinsertvalues= substr_replace($_prvinsertvalues,"",strlen($_prvinsertvalues)-27);
            $_prvinsert=substr(trim($_prvinsert),0,-1);
            $_prvinsertvalues=substr(trim($_prvinsertvalues),0,-1);          
            $_generatetransaction.='insert into '.$_tablename.'
                    (
                        '.$_prvinsert.'
                    )
                    values
                    ( 
                        '.$_prvinsertvalues.'
                    );
                '; 
                    $k=0;
                    for($i=0;$i< $_fieldno;$i++)
                    {
                        $k++;
                       
                        $_prvupdate.= $_fieldname[$k]."=p_".$_fieldname[$k].",
                        ";
             
                    }
            //$_prvupdate=substr_replace($_prvupdate,"",strlen($_prvupdate)-27); 
            $_prvupdate=substr(trim($_prvupdate),0,-1);
            $_generatetransaction.="
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE ".$_tablename.'
                
                SET
                        '.$_prvupdate.'

                WHERE '.$_fieldname[0].'= p_'.$_fieldname[0].';
  
            ';
            $_generatetransaction.="ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  ".$_tablename.' WHERE '.$_fieldname[0].'= p_'.$_fieldname[0].';
                ALTER TABLE '.$_tablename.' AUTO_INCREMENT = 1;
            ';
            //  $_generatetransaction.='SELECT COALESCE(max('.$_fieldname[0].'),0)+ 1 into maxid from '.$_fieldname[0].' where '.$_fieldname[0].'='.$_prvar[0].';';
            // $_generatetransaction.='if(maxid=1) then delete from '.$_tablename.' where '.$_fieldname[0].'='.$_fieldname[0].'; end if;
            $_generatetransaction.='
            END IF;
        END';
 $_generatetransaction_end=' $$
DELIMITER ;';
 //echo $_generatetransaction;
$_dbh->exec($droptransaction);
$_dbh->exec($_generatetransaction);
            
            //$handle = fopen("../stored_procedures/".str_replace("tbl_","",$_tablename)."_transaction.txt", "w");
            //fwrite($handle,$_generatetransaction_start.$_generatetransaction.$_generatetransaction_end); 
            
            $dropfillmodel='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_tablename).'_fillmodel`;';
            $generatefillmodel_start='DELIMITER $$
            ';
             $generatefillmodel='CREATE PROCEDURE `'.str_replace("tbl_","",$_tablename).'_fillmodel`
            (IN `p_'.$_fieldname[0].'` '.$_type[0].$_typelen[0].')
            BEGIN
SELECT * 
       FROM `'.$_tablename.'` 
        WHERE '.$_fieldname[0].'= p_'.$_fieldname[0].';
        END';
 $generatefillmodel_end=' $$
DELIMITER ;';
            $_dbh->exec($dropfillmodel);
            $_dbh->exec($generatefillmodel);

            //$handle = fopen("../stored_procedures/".str_replace("tbl_","",$_tablename)."_fillmodel.txt", "w");
            //fwrite($handle,$generatefillmodel_start.$generatefillmodel.$generatefillmodel_end); 
            
            $dropsearch='DROP PROCEDURE IF EXISTS `'.str_replace("tbl_","",$_tablename).'_search`;';
                
            $generatesearch='CREATE PROCEDURE `'.str_replace("tbl_","",$_tablename).'_search` (IN columns VARCHAR(255), IN tableName VARCHAR(255))
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