$(function(){       
    $('.showtext_body').show();
    if(def.m!='post'){
        $('.showtext_body img').hide();    
    }
    
    $('.worth a').live('click',function(){
        var $this=$(this);
        var id=parseInt($this.attr('data-id'));
        var type=$this.attr('data-type');
        
        if(cookie_exist('rate_ids',id)){
            $.ZhiPHP._tip({content:'您已经评过分',status:false});
            return;
        }                               
        $.post('index.php?m=post&a=rate',{'type':type,'id':id},function(data){
            $this.text(parseInt($this.text())+1);
            $('#J_rate_result_'+id).html(data.data.total+"位网友中的 <i>"+data.data.valid+"</i> 位认为值得买！");            
        },'json');
    });
    $('.J_scrollto').live('click',function(){
        $.scrollTo('#'+$(this).attr('data-id'),100,{ offset:{ top:-40} });
    });    
    $('.J_favs').live('click',function(){
        var $this=$(this);
        var id=$this.attr("data-id");
        $.get('index.php?m=user&a=favs_add',{id:id},function(data){
            $.ZhiPHP._tip({content:data.msg, status:data.status==1});
            if(data.status==1){
                $this.html(data.data);
            }
        },'json');
    });
    $(".login_btn").mouseover(function(){
        $('ul.loginsub').css('visibility','visible');
    });
    $(".login_btn").mouseout(function(){
        $('ul.loginsub').css('visibility','hidden');
    });
    $("ul.loginsub").mouseover(function(){
        $(this).css('visibility','visible');
    });
    $("ul.loginsub").mouseout(function(){
        $(this).css('visibility','hidden');
    });
    $('.nav .tese').hover(function(){
        $('.navicon',this).css({
            'background-position':'-100px -68px'
        });
        $('.sub_menu').show();
    },function(){
        $('.navicon',this).css({
        'background-position':'-100px -16px'
        });
        $('.sub_menu').hide();
    });  
    init_input();  
    $('.J_fav_item').hover(function(){
        $('.J_panel',this).show();
    },function(){
        $('.J_panel',this).hide();
    });
    $('.J_panel .J_del').live('click',function(){
        var id=$(this).attr('data-id');
        $.post('index.php?m=user&a=favs',{act:'del',id:id},function(data){
            $.ZhiPHP._tip({content:data.msg,status:data.status==1});
            if(data.status==1){
                $('#J_fav_item_'+id).remove();    
            }
            
        },'json');
    });
});
function init_input(){
    $('.text').each(function(){
        $this=$(this);
        var tag=$this[0].tagName;
        if(tag=='INPUT'){
            $this.val($this.attr('data-default'));
        }
        else if(tag=='TEXTAREA'){
            $this.html($this.attr('data-default'));
        }
    }).click(function(){        
        if($(this).attr('data-default')==$(this).val()){
            $(this).val('');    
        }
    }).blur(function(){
        $this=$(this);
        if($this.attr('data-default')==$this.val()||$.trim($this.val())==''){
            $this.val($this.attr('data-default'));    
        }
    });    
}
function checkbox(name,val){
    for(var i=0;i<val.length;i++){
        $('input[name="'+name+'"][value="'+val[i]+'"]').attr('checked',true);    
    }    
}
function check_login(content){
    if(!IS_LOGIN){
        $.ZhiPHP._tip({content:content||'请登录!', status:false,url:[{url:'index.php?m=user&a=register',title:'注册'},{url:'index.php?m=user&a=login',title:'登录'}]});    
    }   
    return IS_LOGIN;
}
function cookie_exist(name,v){
    var val=$.trim($.cookie(name));              
    var ids=val.split(",");        
    for(i in ids){            
        if(v==parseInt(ids[i])){
            return true;
        }
    }
    val+=","+v;
    $.cookie(name,val);  
    return;      
}
function toggle_content($this){
    var is_short=$this.attr("class")=='short_content';
    $showtext_body=$('.showtext_body',$this.parent().parent());
    var old_height,new_height;                
    if(is_short){
        $this.removeClass('short_content').addClass('long_content').text('向上收起');
        $old_height=$showtext_body.height();
        $showtext_body.css({
            'overflow':'visible',
            'height':'auto'    
        });    
        $new_height=$showtext_body.height();
        
        $('img',$showtext_body).show();   
        $showtext_body.removeClass('showcont_l');
        $showtext_body.removeClass('fl'); 
    }else{
        $this.removeClass('long_content').addClass('short_content').text('展开全文');
        $showtext_body.css({
            'overflow':'hidden',
            'height':'169px'    
        });    
        $('img',$showtext_body).hide();
        $showtext_body.addClass('showcont_l');
        $showtext_body.addClass('fl'); 
    }
    $('#J_waterfall').masonry({ 
        itemSelector: '.J_item'							
    });	
}
function post_rate(type,id){
    
}
/*头部公告S*/
function MarqueeNews(){
	$('#news').find("ul").animate({
		marginTop : "-20px"
	}, 1000, function () {
		$(this).css({
				marginTop : "0px"
			}).find("li:first").appendTo(this)
	})
}

var MarNews = setInterval(MarqueeNews, 3000);

function gstop(){
	clearInterval(MarNews);
}

function gstart(){
	MarNews = setInterval(MarqueeNews, 3000);
}

function goup(){
	$('#news').find("ul li").last().insertBefore($('#news').find("ul li").first());	
	$('#news').find("ul").css({marginTop:'-20px'});
	$('#news').find("ul").animate({
		marginTop : "0px"
	}, 500)
}
function godown(){	
	$('#news').find("ul").animate({
		marginTop : "-20px"
	}, 500, function () {
		$(this).css({
				marginTop : "0px"
			}).find("li:first").appendTo(this)
	})
}
/*头部公告E*/