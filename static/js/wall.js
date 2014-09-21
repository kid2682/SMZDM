/**
 * @name 瀑布流效果
 * @author andery@foxmail.com
 * @url http://www.ZhiPHP.com
 */
 var _WALL_SPAGE=0;
;(function(){
    $.ZhiPHP.wall = {  
        is_empty:true,
        settings : {
            container: '#J_waterfall', //容器
            item_unit: '.J_item', //商品单元
            loading_bar: '#J_wall_loading', //加载条
            page_bar: '#J_wall_page',
            ajax_url: null, //请求地址
            distance: 50, //高度微调
            spage:2,
            max_spage: 5,
            none:'<div class="J_wall_none">抱歉，没有更多数据</div>'
        },
        init: function(options){            
            options && $.extend($.ZhiPHP.wall.settings, options);
            var s = $.ZhiPHP.wall.settings;
            s.ajax_url = $(s.container).attr('data-uri');
            
            var distance = $(s.container).attr('data-distance');
            if(distance != void(0)){
                s.distance = distance;
            }
            //使用masonry插件
            $(s.container)[0] && $(s.container).imagesLoaded( function(){
                $(s.container).masonry({
                    itemSelector: s.item_unit
                });
                $(s.item_unit).animate({opacity: 1});
            });
            $.ZhiPHP.wall.is_loading = !1;
            this.is_empty=$.trim($(s.container).html()).length==0;
            $(window).load($.ZhiPHP.wall.lazy_load);
            $(window).bind('scroll', $.ZhiPHP.wall.lazy_load);  
                      
        },
        //加载
        lazy_load: function(){
            var s = $.ZhiPHP.wall.settings,
                st = $(document).height() - $(window).scrollTop() - $(window).height();
            if (!$.ZhiPHP.wall.is_loading && $(s.loading_bar)[0] && st <= s.distance||s.spage==2){
                $.ZhiPHP.wall.is_loading = !0;
                $.ZhiPHP.wall.loader();
            }
        },
        //加载状态
        is_loading: !0,
        //执行加载
        loader: function(){            
            var s = $.ZhiPHP.wall.settings;            
            if(s.ajax_url==null) return;
            
            if(this.is_empty&&s.spage>2||_WALL_SPAGE>=s.spage) return;
            
            $(s.loading_bar).show();
            $.ajax({
                url: s.ajax_url,
                data: {sp: s.spage},
                type: 'GET',
                dataType: 'json',
                success: function(result){                    
                    if(result.status == 1){
                        _WALL_SPAGE=s.spage;
                        $.getScript("http://bdimg.share.baidu.com/static/js/bds_s_v2.js?cdnversion=" + new Date().getMinutes());                        
                        var html = $(result.data.html).css({opacity: 0});                                                
                        html.find('.J_img').imagesLoaded(function(){
                            html.animate({opacity: 1});
                            if($.trim(result.data.html).length==0&&s.spage==2){
                                $('.J_wall_none').remove();
                                $(s.container).append(s.none);
                            }                                                                                   
                            $(s.container).append(html).masonry('appended', html, true, function(){
                                $(s.loading_bar).hide(); //隐藏加载条
                                $.ZhiPHP.wall.is_loading = !1; //可以继续加载
                                s.spage += 1; //页码加1                                
                                if(s.spage > s.max_spage || !result.data.isfull){
                                    $(s.page_bar).show(); //子页加载完毕
                                    $(window).unbind('scroll', $.ZhiPHP.wall.lazy_load);
                                }
                                !result.data.isfull && $(s.loading_bar).remove();
                            });
                        });
                    }else{
                        $.ZhiPHP.tip({content:result.msg, icon:'error'});
                    }
                }
            });
        }
    }
    $.ZhiPHP.wall.init({distance:PINER.config.wall_distance, max_spage:PINER.config.wall_spage_max});
})(jQuery);