/*
依赖：
jquery-1.4.2.js
*/
/*
.brv_validate_msg{ 
	background:#fffcf0;
	color:#727171;
	border:1px #dcdddd solid;
	font-size:12px;
	padding:0px 3px;
	word-wrap:none;
	line-height:20px;
	display:inline-block;
}
.brv_validate_error{ 
	background:#fffcf0;
	border:1px #e3bc71 solid;
}
*/
$(function(){ 
    $(".brv-edit-table select").change(function(){ 
        if($("option:selected",this).attr("hint")=="not")
        {
            alert("该分类禁止选择！");
            $(this).val("0");
        }		        	    
    });  	
});
BrvMsg=$.noop;
Array.prototype.exist=function(o){ 
    for(var i=0;i<this.length;i++){ 
        if(o==this[i])return true;
    }
    return false;
}
function brv_validate($form,rules){
    $('.brv_validate_msg').remove();
    $('.brv_validate_error').removeClass('brv_validate_error');
    
    for(var i=0;i<rules.length;i++){
        var res={
            err:true
        };
        if(typeof rules[i]=='undefined')continue;
        if(typeof rules[i].name=='undefined')continue;
        
        $('[name="'+rules[i].name+'"]',$form).each(function(){
            $(this).removeClass('brv_validate_error');
            var value=$.trim($(this).val());
			var def_value=$.trim($(this).attr('data-default'));
            
            var name=rules[i].name;
            res.obj=$(this);
            var tagName=$(this)[0].tagName.toLowerCase();
            if(tagName=='select'){
                if(value<=0){
                    res.err=false;
                    res.msg="必须选择.";
                    return;
                }
                return true;
            }
            var type=$(this).attr('type');
            if(type=='radio'||type=='checkbox'){
                if($('[name="'+rules[i].name+'"]:checked',$form).size()==0){
                    res.err=false;
                    res.msg="必须选择.";
                }
                return;
            }            
            //检查是否为空
            if(value.length==0||value==def_value){
                res.err=false;
                res.msg="不能为空";
                $(this).val('');
                return;
            }
            var equal=rules[i].equal;
            if(typeof equal!='undefined'){
                if(value!=$.trim($('input[name="'+equal+'"]').val())){
                    res.err=false;
                    res.msg="输入不正确.";
                    return;
                }
            }
            
            var apply=rules[i].apply;
		
            switch(apply){ 
                case 'id':
                    if(!/^[0-9]{17}[0-9|X|x]{1}$/.test(value)){ 
                        res.err=false;
                        res.msg="无效身份证号";
                        return;
                    }
                    break;
                case 'chinese':
                    if(!/[\u4E00-\u9FA5]/g.test(value)){ 
                        res.err=false;
                        res.msg="只能含汉字";
                        return;
                    }
                    break;
                case 'email':
                    if(!/^[a-z-A-Z-0-9]+@[\.\w]+$/.test(value)){ 
                        res.err=false;
                        res.msg="Email格式错误";
                        return;
                    }
                    break;
                case "handphone"://手机号码
                    if(!/^[0-9]{11}$/.test(value)){ 
                        res.err=false;
                        res.msg="无效手机号";
                        return;
                    }
                    break;
                case "phone"://固定电话
                    if(!(/^[0-9]{4}-[0-9]{8}$/.test(value) || /^[0-9]{8}$/.test(value))){ 
                        res.err=false;
                        res.msg="固定电话格式为\"区号(4位,可不填)-号码(8位)\"";
                        return;
                    }
                    break;
                case "int":
                    if(!(/^[1-9]+[0-9]*$/.test(value)||value=="0")){ 
                        res.err=false;
                        res.msg="必须为非负整数";
                        return;
                    }   
                    break;             
                case "pwd"://密码判断
                    //if(!/^[a-z-A-Z]+[a-z-A-Z-0-9_]*[a-z-A-Z-0-9]+$/.test(value)){ 
                    if(!/^[a-z-A-Z-0-9_]*$/.test(value)){ 
                        res.err=false;
                        res.msg="密码只能含字母、数字或下划线";
                        return;
                    }
                    break;					
            }
            //检查最大、最小
            if(['chinese','pwd','string'].exist(apply)){//字符串类型 
                if(value.length<rules[i].min){ 
                    res.err=false;
                    res.msg="不能少于"+rules[i].min+"个字符";
                    return;
                }
                if(value.length>rules[i].max){ 
                    res.err=false;
                    res.msg="不能多于"+rules[i].max+"个字符";
                    return;
                }
            }
            if(['int'].exist(apply)){//数值类型 
                if(parseInt(value)<rules[i].min){ 
                    res.err=false;
                    res.msg="不能小于"+rules[i].min;
                    return;
                }
                if(parseInt(value)>rules[i].max){ 
                    res.err=false;
                    res.msg="不能大于"+rules[i].min;
                    return;
                }
            }
	
        });		
        if(!res.err){ 
            console.log();
            $region=$("#"+res.obj.attr('name')+"_region");
            if($region.size()>0){
                res.obj=$region;
            }
            res.msg_pos={	
                left:	res.obj.offset().left,	
                top:	res.obj.offset().top+res.obj.outerHeight()+1
            };
            var interval=0;
            var flicker=setInterval(function(){ 
                if(interval%2==0){ 
                    res.obj.addClass('brv_validate_error');
                }else{
                    res.obj.removeClass('brv_validate_error');
                }
                if(interval>3){
                    clearInterval(flicker);
                    var html='<b class="brv_validate_msg" \
                    style="left:'+res.msg_pos.left+'px;top:'+res.msg_pos.top+'px;position: absolute;">\
    				'+res.msg+'</b>';
                    $(res.obj).after(html);                    
                }
                interval++;
            },60);	
            return false;
        }
    }
    return true;	
}
