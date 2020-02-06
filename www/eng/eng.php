<?php
# mcoin engine

ini_set("include_path",".:..:eng:../eng");
require("conf/defaultconf.php");
require("conf/conf.php");

$DB_PDO_URL='mysql:host='.$DB_SERVER.';port=3306;dbname='.$DB_DATABASE;


// control error reporting
error_reporting(E_ALL);
ini_set("display_errors",1);

//print("init mcoin engine");

function input_post_str_parameter($name, $defaultvalue=null) {
    if ( isset($_POST[$name]) ) {
        return $_POST[$name];
    } 
    return $defaultvalue;
}

function input_post_int_parameter($name, $defaultvalue=null) {
    $value=input_post_str_parameter($name,$defaultvalue);
    if ( is_numeric($value) ) {
        return intval($value);
    }
    return $defaultvalue;
}

function input_get_str_parameter($name, $defaultvalue=null) {
    if ( isset($_GET[$name]) ) {
        return $_GET[$name];
    } 
    return $defaultvalue;
}

function input_get_int_parameter($name, $defaultvalue=null) {
    $value=input_get_str_parameter($name,$defaultvalue);
    if ( is_numeric($value) ) {
        return intval($value);
    }
    return $defaultvalue;
}

function input_get_float_parameter($name, $defaultvalue=null) {
    $value=input_get_str_parameter($name,$defaultvalue);
    if ( is_numeric($value) ) {
        return floatval($value);
    }
    return $defaultvalue;
}


function ivalue_from_value($value) {
    global $UNIT_VALUE;
    return intval($value/$UNIT_VALUE);
}

function value_from_ivalue($ivalue) {
    global $UNIT_VALUE;
    return $ivalue*$UNIT_VALUE;
}



class CoinDB {
    //protected $dblink = null:
    
    function __construct() {
        global $DB_PDO_URL,$DB_USER,$DB_PASS;
        $this->dblink = new PDO($DB_PDO_URL,$DB_USER,$DB_PASS,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //$this->dblink->
        
    }

    function resetDB() {
        $q="CREATE TABLE coins (id INT AUTO_INCREMENT PRIMARY KEY, secret VARCHAR(20), value INT)";
        print($q."\n");
        try {
            $this->dblink->query($q);
        } catch (PDOException $e) {
            print("error\n");
            print($e->getMessage());
        }
        print("OK\n");
    }
    
    
    function printVersion() {
        $st=$this->dblink->query('select version();');
        print("vvv: [ ".json_encode($st->fetchAll())." ]");
    }
    
    // devuelve false o el id del coin
    // es solo para admin no exponer en api publico
    function createCoinWithValue($value, $entropy = null) {
        if ($value!=null) {
            $value=ivalue_from_value($value);
        }
        if ($value<0) {
            return false;
        }
        // cualquier cosa aleatoria
        $s1=md5("CoinSEED".rand().$entropy.rand()."+time+".time());
        $secret = substr($s1,0,20);
        $q="INSERT INTO coins (secret,value) VALUES (:secret,:value)";
        $s = $this->dblink->prepare($q);
        if (!$s->execute(array(":secret"=>$secret,":value"=>$value))) {
            return false;
        }
        $coinid = $this->dblink->lastInsertId();
        $value=value_from_ivalue($value);
        return array("id"=>$coinid  ,"auth"=>$secret, "value"=>$value);    
    }
    
    function cleanCoin($id) {
        // hace falta el lock ??
        $this->dblink->query('LOCK TABLES coins WRITE');
        $s= $this->dblink->prepare('DELETE FROM coins WHERE id=:id AND value=0');
        $s->execute(array(":id"=>$id));
        $this->dblink->query('UNLOCK TABLES');
    }
    
    // devuelve false si no existe o su valor
    function getCoinValue($id) {
        $q="SELECT value FROM coins WHERE id=:id";
        
        $s=$this->dblink->prepare($q);
        $r=$s->execute(array("id"=>$id));
        $nr=$s->rowCount();/// cuidado
        if ($nr<=0) {
            return false;
        } if ($nr>1) {
            // algo raro pasa hay dos coins con el mismo id...
        }
        $v=$s->fetch()['value'];
        if ($v!=null) {
            $v=value_from_ivalue($v);
        }
        return $v;
    }

    // devuelve false si no existe o su valor
    function getCoinValueWithAuth($id,$secret) {
        $q="SELECT value FROM coins WHERE id=:id AND secret=:secret";
        
        $s=$this->dblink->prepare($q);
        $r=$s->execute(array("id"=>$id,":secret"=>$secret));
        $nr=$s->rowCount();/// cuidado
        if ($nr<=0) {
            return false;
        } if ($nr>1) {
            // algo raro pasa hay dos coins con el mismo id...
        }
        $v=$s->fetch()['value'];
        if ($v!=null) {
            $v=value_from_ivalue($v);
        }
        return $v;
    }



    // return false si no lo ha transferido true si lo ha transferido a un coin existente
    function transfer($src_id,$src_secret,$dst_id,$value = null) {
        global $MIN_COIN_VALUE,$MAX_COIN;
        if ($value!=null) {
            $value=ivalue_from_value($value);
            if ($value<=0) {
                return array('ok'=>false,'msg'=>'transfer value must be greater than '.$MIN_COIN_VALUE);
            }
        }
        if ($src_id==$dst_id) {
            return array('ok'=>false,'msg'=>'self transfer not allowed');
        }
        if ( $this->dblink->query('LOCK TABLES coins WRITE') === false ) {
            return array('ok'=>false,'msg'=>'transfer error');
        }
        
        $gotcoin = false;

        $setvalue=$this->dblink->prepare('SET @q=:value');
        $setsrc=$this->dblink->prepare('SET @srcid=:srcid');
        $setsecret=$this->dblink->prepare('SET @secret=:secret');
        $setdst=$this->dblink->prepare('SET @dstid=:dstid');
        $setmaxcoin=$this->dblink->prepare('SET @maxcoin=:maxcoin');

        $setsrc->execute(array(":srcid"=>$src_id));
        $setsecret->execute(array(":secret"=>$src_secret));
        $setdst->execute(array(":dstid"=>$dst_id));
        
        if ($value != null) {
            // sacar algo concreto
            $setvalue->execute(array(":value"=>$value));
            $r= $this->dblink->query('UPDATE coins SET value=value-@q WHERE id=@srcid AND secret=@secret  AND value-@q>=0');     
            if ($r->rowCount()>0) {
                $gotcoin=true;
            }
        } else {
            // sacar todo
            $r= $this->dblink->query('SELECT value FROM coins WHERE id=@srcid AND secret=@secret');
            $value=$r->fetchColumn();
            if (is_numeric($value)) {
                $setvalue->execute(array(":value"=>$value));
                $r= $this->dblink->query('UPDATE coins SET value=0 WHERE id=@srcid');
                if ($r->rowCount()>0) {
                    $gotcoin=true;
                }
            }
        }

        if (!$gotcoin) {
            // el coin no existe, no tienes auth o no tiene suficiente dinero    
            // queremos saber la razon ??
        }

        $storedcoin=false;
        if ($gotcoin) {
            // almacenar
            $setmaxcoin->execute(array(":maxcoin"=>$MAX_COIN));
            $r= $this->dblink->query('UPDATE coins SET value=value+@q WHERE id=@dstid AND value+@q<@maxcoin');
            if ( $r->rowCount()==0 ) {
                $storedcoin=false;
                $r= $this->dblink->query('UPDATE coins SET value=value+@q WHERE id=@srcid');
                if ( $r->rowCount()==0 ) {
                    // we have a problem... no podemos devolver al origen lo que hemos sacado
                    
                }
            } else {
                $storedcoin=true;
            }            
        }
        
        $this->dblink->query('DELETE FROM coins WHERE id=@srcid AND value=0');

        $result= $gotcoin && $storedcoin;
        //error_log('got('.$src_id.') '.$gotcoin.' store('.$dst_id.') '.$storedcoin." = ".$result."\n");
        
        if ( $this->dblink->query('LOCK TABLES coins WRITE') === false ) {
            // mas vale que esto no falle...
        }
        
        if (!$result) {
            return array('ok'=>false,'msg'=>'transfer failed');
        }
        
        $value=value_from_ivalue($value);
        return array('ok'=>true,'tvalue'=>$value);
    }

    
    public function __toString() {
        return "coindb[".$this->dblink."]";    
    }
}

?>