/**
 * @name 商品评论
 * @author andery@foxmail.com
 * @url http://www.ZhiPHP.com
 */
;(function($){
    $.ZhiPHP.comment = {
        settings: {
            container: '#J_comment_area',
            page_list: '#J_cmt_list',
            page_bar: '.J_cmt_page',
            pub_content: '#J_cmt_content',
            pub_btn: '#J_cmt_submit',
            cmt_total:'#J_cmt_total',
            digg_btn:'.J_digg',
            burn_btn:'.J_burn',
            at_btn:'.J_at',
            quote_btn:'.J_quote',
            quote_tip:'#J_quote_tip',
            m:'post'
        },
        init: function(options){
            options && $.extend($.ZhiPHP.comment.settings, options);
            if($(this.settings.container).size()>0){
                $.ZhiPHP.comment.list();
                $.ZhiPHP.comment.publish();    
            }
        },
        //列表
        list: function(){
            var id = $(this.settings.container).attr('data-id');
            //this.load(PINER.root + '/?m='+$.ZhiPHP.comment.settings.m+'&a=comment_list&id='+id);
            var s = $.ZhiPHP.comment.settings;
            $('li', $(s.page_list)).live({
                mouseover: function(){
                    $(this).addClass('hover');
                },
                mouseout: function(){
                    $(this).removeClass('hover');
                }
            });
            $('a', $(s.page_bar)).live('click', function(){
                var url = $(this).attr('href');
                $.ZhiPHP.comment.load(url);
                return false;
            });
            $(s.digg_btn).live('click',function(){
                
                var $this=$(this);
                var id=$this.attr("data-id");
                if(cookie_exist('digg_burn',id)){
                    $.ZhiPHP._tip({content:'已经顶(踩)了', status:false});
                    return;   
                }
                $.get('index.php?m='+s.m+'&a=digg_burn&type=digg',{id:id},function(data){
                    if(data.status>0){
                        $this.text('顶('+data.data+')');
                    }
                },'json');                                
            });
            $(s.burn_btn).live('click',function(){
                var $this=$(this);
                var id=$this.attr("data-id");
                if(cookie_exist('digg_burn',id)){
                    $.ZhiPHP._tip({content:'已经顶(踩)了', status:false});                    
                    return;   
                }
                $.get('index.php?m='+s.m+'&a=digg_burn&type=burn',{id:id},function(data){
                    if(data.status>0){
                        $this.text('踩('+data.data+')');
                    }
                },'json');                                
            });
            $(s.at_btn).live('click',function(){
                if(check_login()){
                    $(s.pub_content).val($(this).attr('data-at')+' ');
                    $(s.pub_content).attr({
                        'data-to_id':$(this).attr('data-to_id'),
                        'data-to_name':$(this).attr('data-to_name')
                    });
                    $.scrollTo('#J_messagebox',300);
                }
            });
            $(s.quote_btn).live('click',function(){
                if(check_login()){
                    var pid=$(this).attr("data-id");
                    $(s.quote_tip).html("引用\""+$(this).attr('data-uname')+"\":<a id='J_cancel_quote'>取消引用</a>");
                    $(s.pub_content).attr({
                        'data-pid':pid
                    });
                    $.scrollTo('#J_messagebox',300);    
                }
            });
            $('#J_cancel_quote').live('click',function(){
                $(s.quote_tip).html('');
                $(s.pub_content).attr({
                    'data-pid':0
                });
            });
        },
        load:function(url){
            var s = $.ZhiPHP.comment.settings;
            $.getJSON(url, function(result){
                if(result.status == 1){
                    $(s.page_list).html(result.data.list);
                    if($.trim(result.data.page).length>0){
                        $(s.page_bar).show().html(result.data.page);    
                    }else{
                        $(s.page_bar).hide();
                    }                    
                    $(s.cmt_total).html(result.data.total);
                }else{
                    $.ZhiPHP.tip({content:result.msg, icon:'error'});
                }
            });  
        },
        //发表评论
        publish: function(){
            var s = $.ZhiPHP.comment.settings;
            $(s.pub_btn).live('click', function(){
                if(!$.ZhiPHP.dialog.islogin()) return !1;
                var id = $(s.container).attr('data-id'),
                    dv = $(s.pub_content).attr('def-val'),
                    to_id=$(s.pub_content).attr('data-to_id'),
                    to_name=$(s.pub_content).attr('data-to_name'),
                    pid=$(s.pub_content).attr('data-pid'),
                    content = $(s.pub_content).val();
                if(content == dv){
                    $(s.pub_content).focus();
                    return false;
                }
                $.ajax({
                    url: PINER.root + '/?m='+$.ZhiPHP.comment.settings.m+'&a=comment',
                    type: 'POST',
                    data: {
                        id: id,
                        content: content,
                        to_id:to_id,
                        to_name:to_name,
                        pid:pid
                    },
                    dataType: 'json',
                    success: function(result){
                        if(result.status == 1){
                            $(s.pub_content).val('');
                            $(s.page_list).prepend(result.data.html);
                            $(s.cmt_total).text(result.data.total);
                            $.scrollTo(s.page_list,300,{ offset:{ top:-100} });
                        }else{
                            $.ZhiPHP.tip({content:result.msg, icon:'error'});
                        }
                    }
                });
            });
        }
    };
    $.ZhiPHP.comment.init();
})(jQuery);