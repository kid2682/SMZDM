/**
 * @name 专辑评论
 * @author andery@foxmail.com
 * @url http://www.ZhiPHP.com
 */
;(function($){
    $.ZhiPHP.albumcmt = {
        settings: {
            container: '#J_albumcmt_area',
            page_list: '#J_albumcmt_list',
            page_bar: '#J_albumcmt_page',
            pub_content: '#J_albumcmt_content',
            pub_btn: '#J_albumcmt_submit'
        },
        init: function(options){
            options && $.extend($.ZhiPHP.albumcmt.settings, options);
            $.ZhiPHP.albumcmt.list();
            $.ZhiPHP.albumcmt.publish();
        },
        //列表
        list: function(){
            var s = $.ZhiPHP.albumcmt.settings;
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
                $.getJSON(url, function(result){
                    if(result.status == 1){
                        $(s.page_list).html(result.data.list);
                        $(s.page_bar).html(result.data.page);
                    }else{
                        $.ZhiPHP.tip({content:result.msg, icon:'error'});
                    }
                });
                return false;
            });
        },
        //发表评论
        publish: function(){
            var s = $.ZhiPHP.albumcmt.settings;
            $(s.pub_btn).live('click', function(){
                if(!$.ZhiPHP.dialog.islogin()) return !1;
                var aid = $(s.container).attr('data-aid'),
                    dv = $(s.pub_content).attr('def-val'),
                    content = $(s.pub_content).val();
                if(content == dv){
                    $(s.pub_content).focus();
                    return false;
                }
                $.ajax({
                    url: PINER.root + '/?m=album&a=comment',
                    type: 'POST',
                    data: {
                        id: aid,
                        content: content
                    },
                    dataType: 'json',
                    success: function(result){
                        if(result.status == 1){
                            $(s.pub_content).val('');
                            $(s.page_list).prepend(result.data);
                        }else{
                            $.ZhiPHP.tip({content:result.msg, icon:'error'});
                        }
                    }
                });
            });
        }
    };
    $.ZhiPHP.albumcmt.init();
})(jQuery);