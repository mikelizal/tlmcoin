
$( document ).ready( function() {

    $('.menuitem').click(function(e) {
        e.preventDefault();
        $('.ocultable').hide();
        $('#'+this.text).show();
        //$('.transferdiv #do').prop('disabled',false);
        //$('.creatediv #do').prop('disabled',false);
    });
    $('.verifydiv #do').click(verify_button);
    $('.transferdiv #do').click(transfer_button);
    $('.creatediv #do').click(create_button);
    $('.reactivate').click(reactivate_button);

    $('.transferdiv #all').change(all_toggle);
    $('.creatediv #all').change(all_toggle);
    
    $('.ocultable').hide();
    $('#about').show();
    
    
});

function print_in_div(div,msg,color=undefined) {
    div.html(msg);
    if ( color != undefined ) {
        div.css('background-color',color);                   
    }
}

function clear_result_div() {
    $('.transferdiv #out').css('background-color','lightgray');
    $('.creatediv #out').css('background-color','lightgray');
    $('.transferdiv #out').html('...');
    $('.creatediv #out').html('...');

}

function print_to_log(msg) {
    var logdiv = $('div#log');
    logdiv.html(msg+"<br>"+logdiv.html());
}

function all_toggle() {
    if ( $('.transferdiv #all').is(':checked') ) {
        $('.transferdiv #valuebox').hide();
    } else {
        $('.transferdiv #valuebox').show();        
    }
    if ( $('.creatediv #all').is(':checked') ) {
        $('.creatediv #valuebox').hide();
    } else {
        $('.creatediv #valuebox').show();        
    }
}


function get_info_from_coinid(coinid, receive_function) {
    $.getJSON('api/status.php',{coinid:coinid},function(r) {
        //alert('received -'+r+'- '+r.coinid+' x '+r.value);
        receive_function(r);
    });
}

function get_info_from_coinname(coinname, receive_function) {
    var s=coinname.split('-');
    get_info_from_coinid(s[0]);
}


function transfer(srcid,srcsecret,dstid,value, receive_function) {
    $.getJSON('api/transfer.php',{srcid:srcid,auth:srcsecret,dstid:dstid,value:value},function(r) {
        receive_function(r);
    });
}

function create(srcid,srcsecret,value, receive_function) {
    $.getJSON('api/new.php',{srcid:srcid,auth:srcsecret,value:value},function(r) {
        receive_function(r);
    });
}

function reactivate_button() {
    $('.transferdiv #do').prop('disabled',false);
    $('.creatediv #do').prop('disabled',false);
    clear_result_div();
}


function verify_button() {
    var coinname=$('.verifydiv #coin').val();
    var coinid=Number.parseInt($('.verifydiv #coinid').val());
    var idfromname=Number.parseInt(coinname.split('-')[0]);
    var id;
    if (coinid>0) {
        id=coinid;
    } else if(idfromname>0) {
        id=idfromname;
        $('.verifydiv #coinid').val(id);
    } else {
        id=0;
        // no hay id que verificar
    }
    
    var out=$('.verifydiv #out');
    if (id>0) {
        coinname=id+"-XXX"
        print_in_div(out,'comprobando...','lightBlue');                

        get_info_from_coinid(id, function(r) {
            if (r.coinid == undefined) {
                print_in_div(out,'coin no existe<br>quiza existio pero ya ha sido gastado','orange')
                print_to_log(coinname+" no existe");
                return;
            }
            if (r.coinid != id) {
                print_in_div(out,'ha ocurrido un error en la verificacion','orange');                
                return;
            } else {
                print_in_div(out,'el coin es valido<br>valor : '+r.value,'lightGreen');              
                print_to_log(coinname+" vale "+r.value);
                return;                
            }
        });    
    } else {
//        alert('o'+($('.verifyid #out').length());
        print_in_div(out,"No hay nada que verificar",'yellow');
    }
}

function transfer_button() {
    $('.transferdiv #do').prop('disabled',true);
    var srccoin=$('.transferdiv #srccoin').val();
    var dstcoin=$('.transferdiv #dstcoin').val();
    var value=Number.parseFloat($('.transferdiv #value').val());
    var all=$('.transferdiv #all').is(':checked');
    var src=srccoin.split('-');
    var dst=dstcoin.split('-');
    var sid=Number.parseInt(src[0]);
    var ssecret=src[1];
    var did=Number.parseInt(dst[0]);

    var out=$('.transferdiv #out');

    if (!sid>0) {
        print_in_div(out,'no hay coin de origen','yellow');
        return;
    }
    if (!did>0) {
        print_in_div(out,'no hay coin de destino','yellow');
        return;
    }
    if (!value>0 && !all) {
        print_in_div(out,'la cantidad a transferir no es valida','yellow');
        return;
    }
    if (all) {
        value='all';
    }

    transfer(sid,ssecret,did,value, function(r) {
        if ( ! r.ok ) {
            print_in_div(out, "error<br>"+r.msg ,"orange");            
        } else {
            print_in_div(out, "transferencia correcta" ,"lightGreen");
            print_to_log(sid+"-XXX >>> "+did+"-XXX +"+r.tvalue);
        }
    });
}


function create_button() {
    $('.creatediv #do').prop('disabled',true);
    var srccoin=$('.creatediv #srccoin').val();
    var value=Number.parseFloat($('.creatediv #value').val());
    var all=$('.creatediv #all').is(':checked');
    var src=srccoin.split('-');
    var sid=Number.parseInt(src[0]);
    var ssecret=src[1];

    var out=$('.creatediv #out');

    if (!sid>0) {
        print_in_div(out,'no hay coin de origen','yellow');
        return;
    }
    if (!value>0 && !all) {
        print_in_div(out,'la cantidad a transferir no es valida','yellow');
        return;
    }
    if (all) {
        value='all';
    }

    create(sid,ssecret,value, function(r) {
        if ( r.error ) {
            print_in_div(out, "error<br>"+r.error ,"orange");
            return;
        }
        if ( r.problem ) {
            print_in_div(out, "error<br>"+r.msg ,"orange");            
        } else {
            print_in_div(out, "coin created<br><br><b>"+(r.id)+"-"+(r.auth)+"</b>","lightgreen");
            print_to_log((r.id)+"-"+(r.auth)+" creado con "+(r.value));
        }
    });
}





