(function($){
    
    var RawEditor = function(element)
    {
       var elem = $(element);
       var obj = this;
       var pid = 0;
       var zone = 0;
       var open = false;
       var closeonly = false;

        this.update = function()
        {
            $.jGrowl("updating content, please wait", {
                life: 3000, 
                speed:  'slow'
            });
            var post_data = {
                zone_data: $("#editor-"+this.zone).val()
            };
            var post_url = "http://"+$(location).attr('hostname')+"/update_zone/"+this.zone+"/"+this.pid;
            jQuery.ajax({
                type: "POST",
                url: post_url,
                dataType: "json",
                data: post_data,
                cache: false
            }).done(function( responce ) {
                if(responce.status == 'ok'){
                    $.jGrowl("Zone update sucess", {
                        life: 3000, 
                        speed:  'slow'
                    });
                }
            });
        };
        
        this.history = function()
        {
            
            var post_data = {
                zone: this.zone,
                pid: this.pid
            };
            var post_url = "http://"+$(location).attr('hostname')+"/zone_history/"
            jQuery.ajax({
                type: "POST",
                url: post_url,
                dataType: "json",
                data: post_data,
                cache: false
            }).done(function( responce ) {
                for (x in responce){
                    $('#editor-raw-history_'+responce[x].z_parent).append($("<option/>", { 
                        value: responce[x].zid,
                        text : responce[x].z_creation 
                    }));
                }
            });
        };
        
        
        this.fetch_history = function(zid)
        {
            var post_data = {
                zid: zid
            };
            var post_url = "http://"+$(location).attr('hostname')+"/zone_history_data/"
            jQuery.ajax({
                type: "POST",
                url: post_url,
                dataType: "json",
                data: post_data,
                cache: false
            }).done(function( responce ) {
                var editor = new RawEditor();
                $("#editor-"+responce[0].z_parent).val(editor.decode_data(responce[0].z_data));
            });
        };
        
        this.decode_data = function(str){
            return decodeURIComponent((str+'').replace(/\+/g, '%20'));
        }
       
        this.toggle = function(){
            if(!this.open){
                this.open = true;
                this.attach();
            }else{
                this.close();
            }
        }
        
        this.close = function(){
            console.log('close');
            this.open = false;
            $("#"+this.zone).html( $("#editor-"+this.zone).val() );
        }
        
        this.attach = function(){
            var content = $("#"+this.zone).html();
            var data = {
                "zone": this.zone,
                "zone_data": content
            }
            this.history();
            var result = tmpl("tmpl-form", data);
            $("#"+this.zone).html( result );
            $("#editor-"+this.zone).width( $("#"+this.zone).parent().width() );
            $("#editor-"+this.zone).height( 400 );

            $("#zoneupdate_"+this.zone).click({zone: this.zone},function(e) {
                $('#'+e.data.zone).data('raweditor').update();
            });
            
            $('#editor-raw-history_'+this.zone).change({target: this},function(e) {
                console.log(e);
                e.data.target.fetch_history($('#editor-raw-history_'+e.data.target.zone).val());
            });
            
            
        }
    };
    $.fn.raweditor = function(action, pid)
   {
       return this.each(function()
       {
           var element = $(this);
           if (!element.data('raweditor')){
                var raweditor = new RawEditor(this);
                element.data('raweditor', raweditor);
                raweditor.pid = pid;
                raweditor.zone = $(this).attr('id');
           }
           
           switch(action){
               case 'update':
                   element.data('raweditor').update();
                   break;
               case 'close':
                   element.data('raweditor').close();
                   break;
               case 'toggle':
                   element.data('raweditor').toggle();
                   break;
               default:
                   element.data('raweditor').toggle();
                   break;
           }
       });
   };
})(jQuery);
