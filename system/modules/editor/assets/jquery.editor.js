(function($){
    
    var Editor = function(element)
    {
       var elem = $(element);
       var obj = this;
       var pid = 0;
       var zone = 0;
       var open = false;
       var state = 'closed';
       var closeonly = false;

        this.update = function()
        {
            $.jGrowl("updating content, please wait", {
                life: 3000, 
                speed:  'slow'
            });
            var post_data = {
                zone_data: window['editor_'+this.zone].getData()
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
                    $('#editor-history_'+responce[x].z_parent).append($("<option/>", { 
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
                var editor = new Editor();
                $("#edit-"+responce[0].z_parent).html(editor.decode_data(responce[0].z_data));
            });
        };
        
        this.decode_data = function(str){
            return decodeURIComponent((str+'').replace(/\+/g, '%20'));
        }
       
        this.toggle = function(){
            if(this.state == 'justclosed'){
                this.open = false;
                this.close();
            }else{
                if(!this.open){
                    this.open = true;
                    this.attach();
                }else{
                    this.close();
                }
            }
        }
        
        this.close = function(){
            switch(this.state){
                case 'justclosed':
                    this.state = 'closed';
                    break;
                case 'open':
                    this.state = 'justclosed';
                    break;
                default: 
                    this.state = 'closed';
            }
            if(this.open){
                var editor_content = window['editor_'+this.zone].getData();
                if(editor_content != ''){
                    $("#"+this.zone).html( editor_content );
                }
            }
            this.open = false;
            $('#editor-edit-button').html("Enable Rich Editor");
        }
        
        this.attach = function(){
            console.log('editeropen');
            this.state = 'open';
            var data = {
                Zone: this.zone,
                ZoneData: $("#"+this.zone).html()
            };
            this.history();
            $( "#"+this.zone ).html($.tmpl( editorTemplate, data ));
            
            $('#editor-history_'+this.zone).change({target: this},function(e) {
                e.data.target.fetch_history($('#editor-history_'+e.data.target.zone).val());
            });
            
            if(document.getElementById(this.zone)){
                window['editor_'+this.zone] = CKEDITOR.inline( document.getElementById( "edit-"+this.zone ) );
            }
            try{
                CKFinder.setupCKEditor( window['editor_'+this.zone], { basePath : '/site/modules/media/assets/', skin : 'v1' });
            }catch(e){
                //ignore, ckfinder is not installed
            }
             $("#zoneupdate_"+this.zone).click({zone: this.zone},function(e) {
                $('#'+e.data.zone).data('editor').update();
            });
            $('#editor-edit-button').html("Disable Rich Editor");
            
        }
    };
    $.fn.editor = function(action, pid)
   {
       return this.each(function()
       {
           var element = $(this);
           if (!element.data('editor')){
                var editor = new Editor(this);
                Temporal.reg_editor(editor);
                element.data('editor', editor);
                editor.pid = pid;
                editor.zone = $(this).attr('id');
           }
           
           switch(action){
               case 'update':
                   element.data('editor').update();
                   break;
               case 'close':
                   element.data('editor').close();
                   break;
               case 'toggle':
                   element.data('editor').toggle();
                   break;
               default:
                   element.data('editor').toggle();
                   break;
           }
       });
   };
})(jQuery);


