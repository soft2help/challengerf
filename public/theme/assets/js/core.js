let dataTables = {
    defaults: {"name":"dt","method":"POST","state":"none","options":{"language":{"url":"/theme/assets/js/datatable/i18n/english.json"}}},   
    basicConfigs: {
        searching: true,
        dom:'<"dataTables_header"lfr>t<"dataTables_footer"ip>',
        select: {
            style: 'single',
            info: false,
            className: 'active',
            selector: 'td:not(:last-child)'
        },
        createdRow: function(row, data, dataIndex){  
        }                        
    }
};

let helpers={
    init: function(){
        $.fn.serializeObject = function(){
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };

    }, 
    trimChars: function(str, c){
        var re = new RegExp("^[" + c + "]+|[" + c + "]+$", "g");
        return str.replace(re,"");
    },
    datePart: function(date){
        if(date)
            return date.split(" ")[0]; 

        return "";
    }
};


let playersList={
    dt: null,
    init: function(){
        this.events.init();
        let _this=this;
        let configs=dataTables.defaults;
        configs.url="/api/player/list";
        let basicConfigs=dataTables.basicConfigs;
        
        $('.players-list')
        .initDataTables(configs,basicConfigs)
        .then((dt)=>{
            _this.dt=dt;
        });
    },
    events:{
        init: function(){
            this.onNewPlayer();
            this.onNewNotification();
            this.onEditPlayer();
            this.onDeletePlayer();
            this.onSubscribePlayer();
        },
        onNewPlayer: function(){
            let formSelector=".playerForm form.player";
            $(".newPlayer").on("click", function(){
                let form='<div class="playerForm">'+$("form.player")[0].outerHTML+'</div>';
                
                swal.fire({
                    html: form,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Save it!',
                    preConfirm: () => {
                        forms.cleanAllErrors(formSelector);
                        let data=forms.serializeJson($(`${formSelector}`));
                        let urlEndpoint="/api/player/new";

                        api.postJson(urlEndpoint,data,formSelector)
                        .then(data=>{
                            playersList.dt.draw(true);
                            Swal.fire(
                                "New Player",
                                data.success,
                                'success'
                                );
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                });
            });
        },
        onNewNotification: function(){
            let formSelector=".notificationForm form.notification";
            $(document).on("click",".newNotification", function(){
                let playerId=$(this).data("playerid");
                let form='<div class="notificationForm">'+$("form.notification")[0].outerHTML+'</div>';
                swal.fire({
                    html: form,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Send it!',
                    preConfirm: () => {
                        forms.cleanAllErrors(formSelector);
                        let data=forms.serializeJson($(`${formSelector}`));
                        let urlEndpoint=`/api/player/${playerId}/notification`;

                        api.postJson(urlEndpoint,data,formSelector)
                        .then(data=>{
                            playersList.dt.draw(true);
                            Swal.fire(
                                "Send Notification",
                                data.success,
                                'success'
                                );
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                });
            });
        },
        onEditPlayer: function(){
            let formSelector=".playerForm form.player";
            $(document).on("click", ".editPlayer", function(){
                let playerId=$(this).data("playerid");
                let form='<div class="playerForm">'+$("form.player")[0].outerHTML+'</div>';
                let urlEndpoint="/api/player/"+playerId;

                swal.fire({
                    html: form,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Edit!',
                    preConfirm: () => {
                        forms.cleanAllErrors(formSelector);
                        let data=forms.serializeJson($(`${formSelector}`));
                        

                        api.putJson(urlEndpoint,data,formSelector)
                        .then(data=>{
                            playersList.dt.draw(true);
                            Swal.fire(
                                "Edit",
                                data.success,
                                'success'
                                );
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                })

                api.getJson(urlEndpoint,0).then(function(data){
                    forms.iterateObject(data,"","",function(key,value){
                        $(`${formSelector} input.${key}`).val(value);
                        if(value==null)
                            value="";

                        $(`${formSelector} select.${key}`).val(value);
                    });
                }).catch((error)=>{
                    console.log(error.message);
                });



            });


        },
        onDeletePlayer: function(){
            $(document).on("click", ".deletePlayer", function(){
                let playerId=$(this).data("playerid");
               
                let urlEndpoint="/api/player/"+playerId;

                swal.fire({
                    html: "Are you sure?",
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete!',
                    preConfirm: () => {
                        api.deleteJson(urlEndpoint)
                        .then(data=>{
                            playersList.dt.draw(true);
                            Swal.fire(
                                "Delete",
                                data.success,
                                'success'
                                );
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                })

            });

        },
        onSubscribePlayer: function(){
            $(document).on("click", ".subscribeUnsubscribe", function(){
                
                let playerId=$(this).data("playerid");
                let urlEndpoint="/api/player/"+playerId+"/subscribe";
                if($(this).hasClass("btn-primary")){
                    urlEndpoint="/api/player/"+playerId+"/unsubscribe";
                }
                
                api.putJson(urlEndpoint)
                        .then(data=>{
                            playersList.dt.draw(true);
                            Swal.fire(
                                "Subscription",
                                data.success,
                                'success'
                                );
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

            });

        }
    }
};

let notificationsList={
    dt: null,
    init: function(){
        this.events.init();
        let _this=this;
        let configs=dataTables.defaults;
        configs.url="/api/notification/list";
       

        let basicConfigs=dataTables.basicConfigs;

        $('.notifications-list')
        .initDataTables(configs,basicConfigs)
        .then((dt)=>{
            _this.dt=dt;
        });
    },
    events:{
        init: function(){
            this.onDeleteNotification();
        },
        onDeleteNotification: function(){
            $(document).on("click", ".deleteNotification", function(){
                let notificationId=$(this).data("notificationid");
               
                let urlEndpoint="/api/notification/"+notificationId;

                swal.fire({
                    html: "Are you sure?",
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete!',
                    preConfirm: () => {
                        api.deleteJson(urlEndpoint)
                        .then(data=>{
                            notificationsList.dt.draw(true);
                            Swal.fire("Delete",data.success,'success');
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                })

                api.getJson(urlEndpoint,0).then(function(data){
                    forms.iterateObject(data,"","",function(key,value){
                        $(`${formSelector} input.${key}`).val(value);
                        if(value==null)
                            value="";

                        $(`${formSelector} select.${key}`).val(value);
                    });
                }).catch((error)=>{
                    console.log(error.message);
                });



            });

        }
    }
};

let notificationsPlayerList={
    dt: null,
    init: function(){
        this.events.init();
        let _this=this;
        let configs=dataTables.defaults;
        let playerId= $("span.player").data("playerid");
        configs.url=`/api/player/${playerId}/notifications`;

        let basicConfigs=dataTables.basicConfigs;

        $('.notificationsplayer-list')
        .initDataTables(configs,basicConfigs)
        .then((dt)=>{
            _this.dt=dt;
        });
    },
    events:{
        init: function(){
            this.onDeleteNotification();
        },
        onDeleteNotification: function(){
            $(document).on("click", ".deleteNotification", function(){
                let notificationId=$(this).data("notificationid");

                let urlEndpoint="/api/notification/"+notificationId;

                swal.fire({
                    html: "Are you sure?",
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete!',
                    preConfirm: () => {
                        api.deleteJson(urlEndpoint)
                        .then(data=>{
                            notificationsPlayerList.dt.draw(true);
                            Swal.fire("Delete",data.success,'success');
                        }).catch(error=>{

                        }).then(function(){                
                           
                        });

                        return  false;
                    }
                });
            });

        }
    }
};

let forms={
    hasError: false,
    init: function(){
        this.hasError=false;
    },
    addError: function(padre,field,textError){
        if(!$(`${padre} input.${field}`).hasClass("is-invalid")){
            $(`${padre} input.${field}`).addClass("is-invalid");
            $(`${padre} input.${field}`).trigger("focus");
        }

        if($(`${padre} .errors.${field}`).length){
            $(`${padre} .errors.${field}`).append(`<div class="error">
            <label class="description">${textError}</label>
            </div>`); 
           
            return;           
        }
        let already=$(`${padre} .${field}`).next(".invalid-feedback").html();
        $(`${padre} .${field}`).next(".invalid-feedback").html(already+'<br>'+textError);
        

        this.hasError=true;
    },
    cleanAllInputs: function(padre=""){
        $(`${padre} input`).val("");
    },
    cleanAllErrors: function(padre= ""){
        $(`${padre} div.error label.description`).parent().remove();
        $(`${padre} .invalid-feedback`).html("");
        $(`${padre} .is-invalid`).removeClass("is-invalid");
        
        this.init();
    },
    cleanFieldError: function(selector){
        selector.siblings("div.error").remove();
        selector.removeClass("error-field");
    },
    disabledInputs: function(){
        $("input.disabled").prop("disabled",true);
    },
    populateForm: function(form,object){
        this.iterateObject(object,"",form);
    },
    iterateObject: function(obj, padre,form,callback=null){
        let _this=this;
        let padreOri=padre;
        

        Object.keys(obj).forEach(key => {
            if(obj[key]==null)
                return;

            if (typeof obj[key] === 'object'){
                let nuevoPadre=padre;
                if(!isNaN(key)){
                    if(nuevoPadre.slice(-1)=="]"){             
                        var n = nuevoPadre.lastIndexOf("[");
                        nuevoPadre=nuevoPadre.substring(0,n);
                    }
                    nuevoPadre=`${nuevoPadre}[${key}]`;                    
                }else{
                    nuevoPadre=`${nuevoPadre}-${key}`
                }
                   
                _this.iterateObject(obj[key],`${nuevoPadre}`,form,callback);
                
            }else{
                let path=`${padreOri}-${key}`
                if(!isNaN(key)){
                    path=`${padreOri}[${key}]`
                }
                   
                

                path = helpers.trimChars(path,"-")

                if(!(typeof callback === 'function')){
                    $(`${form} .${path}`).val(obj[key]);
                }else{
                    callback(path, obj[key]);
                }
               
            }
        });

        return padreOri;


    },
    serializeJson: function(elemento){
      

        return elemento.serializeJSON({
            customTypes: {
              nullOrInt: (str) => {
                if (str === "") 
                    return null;

                if(!isNaN(str))
                    return parseInt(str);

                return null;
              },
              
            }
          })

    }

};

let coreLogin = {
    validateEmail: function(email){
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },
    CheckLogin: function(){
        var dfd = new $.Deferred();

        let selectorUsername=$(`.username`); 
        let selectorPassword=$(`.password`);
        let selectorRememberme=$(`.rememberme`);

        let username=selectorUsername.val();
        let password=selectorPassword.val();
        let rememberme=selectorRememberme.is(":checked");


        api.postForm("/login_check",{
                                        _username: username,
                                        _password:password, 
                                        _rememberme: rememberme
        }).then(data => {
            if("success" in data){
                dfd.resolve(data);
            }else{
                dfd.reject(data);
            }
            
        }).fail((data)=>{dfd.reject(data)});

        return dfd.promise();
    },
    hacerLogin: function(){
        this.CheckLogin().then(function(data){            
            window.location.href = "/";
        }).fail(function(){            
            $(".username").closest("form").removeClass('was-validated')
            $(`.username`).addClass("is-invalid");
            $(`.username`).next(".invalid-tooltip").html("User or password invalid");
        });
    }
};

let api={
    jsonToFormData: function(data){
        var formData = new FormData();

        for (var k in data) {
            formData.append(k, data[k]);
        }

        return new URLSearchParams(formData);           
    },
    apiStatus: function(status,statusText){
    },
    showError: function(titulo,mensaje,modalOrNotificacion=0,options={}, modalId="formErrors"){
        if(!modalOrNotificacion)
            return;

        Swal.fire(titulo,mensaje,'error');
    },
    showSuccess: function(titulo,mensaje,modalOrNotificacion=0,options={}, modalId="formErrors"){      
        if(!modalOrNotificacion)
            return;

        Swal.fire(titulo,mensaje,'success');
    },    
    checkErrorSuccess: function(jsonResponse, padre="", modalOrNotificacion=0,options={}){  
        let getError=false; 
        let allErrors="";
        if('errors' in jsonResponse){
            if(jsonResponse["errors"].length){
                allErrors+=jsonResponse["errors"].join("<br />");                
                getError=true;
            }
        }
        
        if('fieldErrors' in jsonResponse){           
            jsonResponse["fieldErrors"].forEach(fieldError=>{ 
                allErrors+=`<br/>Field: ${fieldError.field}<br/>`;               
                fieldError.errors.forEach((error)=>{
                    allErrors+=` ${error}<br/>`; 

                    let field=fieldError.field.split(".").join("-");
                    field=field.replaceAll("[","-");
                    field=field.replaceAll("]","");
                    forms.addError(padre,field,error);
                });
            });


           
            getError=true;
        }

        if('success' in jsonResponse){
            this.showSuccess("",jsonResponse["success"],modalOrNotificacion,options);
        }

        if('error' in jsonResponse && 'message' in jsonResponse){
            toaster.error(jsonResponse.error,jsonResponse.message,options);
            getError=true;
        }
        
        if(getError){
            this.showError("",allErrors,modalOrNotificacion,options);
            throw new Error(jsonResponse);
        }

        return jsonResponse;
    },
    apiResponse: function(response,padre="",modalOrNotificacion=0,options={}){
        var dfd = new $.Deferred();
        let _this=this;
       
        response
        .then(respuesta=>{
            let status=respuesta.status;
            let statusText=respuesta.statusText;
            this.apiStatus(status,statusText);

            return respuesta.json();
        })
        .then(data => {
            try{
                dfd.resolve(_this.checkErrorSuccess(data,padre,modalOrNotificacion,options));
            }catch(error){
               
                dfd.reject(error);
            }
                
        })
        .catch((error)=>{ 
            Swal.fire("Endpoint","Endpoint Error",'error');
            dfd.reject(error);
        });

        return dfd.promise();
    },
    postRawData: function(url,formData,padre="", modalOrNotificacion=0,options={}){
        let _this=this; 
        const response =  fetch(url, {
            method: 'POST',             
            body: formData
        });

        return _this.apiResponse(response,padre, modalOrNotificacion,options);
    },
    postForm:  function(url,data = {},padre = "", modalOrNotificacion=0, options={}){
        let _this=this; 
        const response =  fetch(url, {
            
        method: 'POST', 
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: _this.jsonToFormData(data)
        });

        return _this.apiResponse(response,padre, modalOrNotificacion,options);
       
          
    },
    postJson: function(url,data={},padre="",modalOrNotificacion=0,options={}){
        let _this=this; 
       
        const response =  fetch(url, {
            method: 'POST',            
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(data)
        });

        return _this.apiResponse(response,padre,modalOrNotificacion,options);
    },
    getJson:  function(url,modalOrNotificacion=0,options={}){
        let _this=this; 
        const response = fetch(url, {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json'
            }
        });

        return _this.apiResponse(response,"",modalOrNotificacion,options);
        
        
    },
    putJson:  function(url,data={},padre="",modalOrNotificacion=0,options={}){
        let _this=this; 
        const response = fetch(url, {
            method: 'PUT', 
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        return _this.apiResponse(response,padre,modalOrNotificacion,options);
    },
    deleteJson:  function(url,modalOrNotificacion=0,options={}){
        let _this=this; 
        const response = fetch(url, {
            method: 'DELETE', 
            headers: {
                'Content-Type': 'application/json'
            }            
        });
        
        return _this.apiResponse(response,"",modalOrNotificacion,options);
    }
};

$(function(){
    if($(".players-list").length)
        playersList.init();

    if($(".notifications-list").length)
        notificationsList.init();

    if($(".notificationsplayer-list").length)
        notificationsPlayerList.init();

    if(!$(".login-form").length){
        api.getJson("/api/user/profile",0)
        .then(function(user){
            if(user.tipo=="SUPER_ADMIN")
                $(".userSuperAdmin").removeClass("hide");

            if(user.tipo=="USER")
                $(".user").removeClass("hide");
        });
    }
});

(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
                coreLogin.hacerLogin();
                event.preventDefault();
                event.stopPropagation();
            }, false);
        });
    }, false);
})();