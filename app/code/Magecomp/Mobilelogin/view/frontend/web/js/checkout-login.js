 require(
        ['jquery',
         'Magento_Ui/js/modal/modal',
         "mage/template",
         'mage/translate',
         'jquery/ui',
         "mage/mage"
        ],
        function(
            $,
            modal,mageTemplate,$t
        ) {
        modalWindow: null
         $('body').on('click','.button-as-link', function() {
         var layout = $("#layout").val();
             var options = {
                'type': 'popup',
                'modalClass' : 'customer-login-popup '+ layout,
                'focus': '[name=username]',
                'responsive': true,
                'parentModalClass': '_has-modal-custom _has-auth-shown',
                 'responsiveClass': 'custom-slide',
                'innerScroll': true,
                'buttons': []
            };
            if ($("#customer-popup-login").length != 0) {
            var popup = modal(options, $('#customer-popup-login'));
            $("#customer-popup-login").modal("openModal");

           
            $(".authentication-dropdown").hide();
                $("#customer-popup-login").modal("openModal");
            $("#customer-popup-login").addClass("ultimate")
            $(".registratio-section").hide();
            $(".modals-overlay").css("display", "block");
            $(".forgot-password-section").hide();
            $(".login-section").show();
            $(".forgotimage").hide();
            $(".regimage").hide();
            $(".loginimage").show();
            }
            });
            
            $('body').on('click','.login-option #loginwithajax', function() {
                $(".otp-login").hide();
                $(".otp-login-verify").hide();
                $(".password-login").show();

                $("#loginwithotp").removeClass("active");
                $("#loginwithajax").addClass("active");
            });
            $('body').on('click', ".remind", function() {
           
                 var layout = $("#layout").val();
             var options = {
                'type': 'popup',
                'modalClass' : 'customer-login-popup '+ layout,
                'focus': '[name=username]',
                'responsive': true,
                 'responsiveClass': 'custom-slide',
                'innerScroll': true,
                'buttons': []
            };
            if ($("#customer-popup-login").length != 0) {
            var popup = modal(options, $('#customer-popup-login'));
            $("#customer-popup-login").modal("openModal");

           
            $(".authentication-dropdown").hide();
                $("#customer-popup-login").modal("openModal");
            $("#customer-popup-login").addClass("ultimate");
             $(".modal-title").text("Forgot Password");
            $(".login-section").hide();
            $(".registratio-section").hide();
            $(".mobileforgotverify").hide();
            $(".mobileforgotpassword").hide();
            $(".emailforgot").hide();
            $(".mobileforgot").show();
            $(".forgot-password-section").show();
            $(".forgotimage").show();
            $(".regimage").hide();
            $(".loginimage").hide();
            }
            return false;
            });
            $('body').on('click','.login-option #loginwithotp', function() {
                $(".password-login").hide();
                $(".otp-login-verify").hide();
                $(".otp-login").show();

                $("#loginwithajax").removeClass("active");
                $("#loginwithotp").addClass("active");
            });
            $('body').on('click', '#customer-popup-registration', function() {
               $(".modal-title").text("Register");
                 $(".login-section").hide();
            $(".forgot-password-section").hide();
            $(".registraiontpverify").hide();
            $(".registraionform").hide();
            $(".registraiontpsend").show();
            $(".registratio-section").show();
             $(".forgotimage").hide();
            $(".regimage").show();
            $(".loginimage").hide();
            
            });
            $('body').on('click','.customer-popup-sign-in', function() {
                $(".modal-title").text("Login");
                self._showLogin();
            
            });
            $('body').on('click','.action-close', function() {
                $(".modals-overlay").css("display", "none");
            
            });
            $('body').on('click', '#customer-popup-forgot-password', function() {
                $(".modal-title").text("Forgot Password");
                self._showForgotPassword();
                return false;
            });
            $('body').on('change',"input[type=radio][name=reset-type]", function(){
                var seletedType = this.value;
                if(seletedType == "mobile") {
                    $(".emailforgot").hide();
                    $(".mobileforgot").show();
                } else {
                    $(".mobileforgot").hide();
                    $(".mobileforgotpassword").hide();
                    $(".mobileforgotverify").hide();
                    $(".emailforgot").show();
                }
            });
            $('body').on('click',".registraiontpsend button", function(e) {
                self._ajaxRegistrationOtpSend(e);
            });
            $('body').on('click',".registraiontpverify button", function(e) {
                self._ajaxRegistrationOtpVerify(e);
            });
            $('body').on('click',".registraionform button", function(e) {
                self._ajaxRegistrationCreate(e);
            });
            $('body').on('click',".password-login button", function(e) {
                self._ajaxLogin(e);
            });
            $('body').on('click',".otp-login button", function(e) {
                self._ajaxLoginOtpSend(e);
            });
            $('body').on('click',".otp-login-verify button", function(e) {
                self._ajaxLoginOtpVerify(e);
            });
            $('body').on('click',".mobileforgot button", function(e) {
                self._ajaxForgotOtpSend(e);
            });
            $('body').on('click',".mobileforgotverify button", function(e) {
                self._ajaxForgotOtpVerify(e);
            });
            $('body').on('click',".mobileforgotpassword button", function(e) {
                self._ajaxForgotReset(e);
            });
            $('body').on('click',".emailforgot button", function(e) {
                self._ajaxForgotEmail(e);
            });
            $('body').on('click',".update-mobile-section button", function(e) {
                self._ajaxUpdateMobile(e);
            });
          
       
       _showLogin= function() {
            
            $(".login-option #loginwithotp").trigger("click");

            $(".registratio-section").hide();
            $(".forgot-password-section").hide();
            $(".login-section").show();
            $(".forgotimage").hide();
            $(".regimage").hide();
            $(".loginimage").show();
        },
        _showForgotPassword= function() {
         $('.reset_type').val('mobile').trigger('change');
           $(".login-section").hide();
            $(".registratio-section").hide();
            $(".mobileforgotverify").hide();
            $(".mobileforgotpassword").hide();
            $(".emailforgot").hide();
            $(".mobileforgot").show();
            $(".forgot-password-section").show();
            $(".forgotimage").show();
            $(".regimage").hide();
            $(".loginimage").hide();
            
        },
        _ajaxRegistrationOtpSend= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-otp-send');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid'); //validates form and returns boolean
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: {mobile:mobile},
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraiontpsend .messages .message").remove();
                        if(response.status == true) {
                            $(".registraiontpverify #verifymobile").val($(".registraiontpsend #registermob").val());
                            $(".registraiontpsend").hide();
                            $(".registraiontpverify").show();
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraiontpsend .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraiontpsend .messages .message-error");
                        }
                    }
                });
            }
        },
        _ajaxRegistrationOtpVerify= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-otp-verify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var otp = jQuery(".registraiontpverify #verifyotp").val();
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data:{verifyotp:otp,mobile:mobile},
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraiontpverify .messages .message").remove();
                        if(response.status == true) {
                            $(".registraionform #createmobile").val($(".registraiontpverify #verifymobile").val());
                            $(".registraionform #otp").val($(".registraiontpverify #verifyotp").val());
                            $(".registraiontpverify").hide();
                            $(".registraionform").show();
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraiontpverify .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraiontpverify .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxRegistrationCreate= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#registraion-create-account');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery("#registermob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#registraion-create-account').serialize() + '&mobilenumber='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".registraionform .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".registraionform .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".registraionform .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLogin= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('.login-email');
            var formPostUrl = dataForm.attr("action");
             dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('.login-email').serialize(),
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".password-login .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else 
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".password-login .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".password-login .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLoginOtpSend= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#login-otp');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid'); //validates form and returns boolean
            var mobile = jQuery(".otp-login #loginotpmob").val();
           var countrycode=jQuery('#countryreg').val().replace('+','');      
            mobile=countrycode.concat(mobile);


            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#login-otp').serialize()+ '&loginotpmob='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".otp-login .messages .message").remove();
                        if(response.status == true) {
                            $(".otp-login-verify #verifymobile").val($(".otp-login #loginotpmob").val());
                            $(".otp-login").hide();
                            $(".otp-login-verify").show();
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".otp-login .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".otp-login .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxLoginOtpVerify= function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#loginotp-verify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".otp-login #loginotpmob").val();
            var countrycode=jQuery('#countryreg').val().replace('+','');            
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#loginotp-verify').serialize()+ '&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".otp-login-verify .messages .message").remove();
                        if(response.status == true) {
                            if(response.redirectUrl == "" || response.redirectUrl == null)
                                location.reload(true);
                            else
                                window.location.href = response.redirectUrl;
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".otp-login-verify .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".otp-login-verify .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotOtpSend= function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
           var countrycode=jQuery('#countryreg').val().replace('+','');        
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp').serialize()+'&forgotmob='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgot .messages .message").remove();
                        if(response.status == true) {
                            $(".mobileforgotverify #verifymobile").val($(".mobileforgot #forgotmob").val());
                            $(".mobileforgot").hide();
                            $(".mobileforgotverify").show();
                            $(".mobile").css("display","block");


                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgot .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgot .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotOtpVerify= function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp-veify');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
             var countrycode=jQuery('#countryreg').val().replace('+','');       
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp-veify').serialize()+ '&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgot .messages .message").remove();
                        if(response.status == true) {
                            $(".mobileforgotpassword #mobilenumber").val($(".mobileforgotverify #verifymobile").val());
                            $(".mobileforgotpassword #otp").val($(".mobileforgotverify #verifyotp").val());

                            $(".mobileforgotverify").hide();
                            $(".mobileforgotpassword").show();
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgot .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgot .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotReset= function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-otp-password');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            var mobile = jQuery(".mobileforgot #forgotmob").val();
             var countrycode=jQuery('#countryreg').val().replace('+','');       
            mobile=countrycode.concat(mobile);
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-otp-password').serialize()+'&mobile='+mobile,
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".mobileforgotpassword .messages .message").remove();
                        if(response.status == true) {
                            $(".customer-popup-sign-in").trigger("click");
                            $(".login-option #loginwithajax").trigger("click");
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".mobileforgotpassword .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".mobileforgotpassword .messages .message-error");
                        }
                    }
                });
            }
        },

        _ajaxForgotEmail= function(e)
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataForm = $('#forgot-email');
            var formPostUrl = dataForm.attr("action");
            dataForm.mage('validation', {});
            var status = dataForm.validation('isValid');
            if(status) {
                jQuery.ajax({
                    type: 'post',
                    url: formPostUrl,
                    data: jQuery('#forgot-email').serialize(),
                    cache: false,
                    showLoader: 'true',
                    success: function(response) {
                        $(".emailforgot .messages .message").remove();
                        if(response.status == true) {
                            $('<div>', {
                                "class": 'message-success success message',
                                html: ""
                            }).appendTo(".emailforgot .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".emailforgot .messages .message-success");
                        } else {
                            $('<div>', {
                                "class": 'message-error error message',
                                html: ""
                            }).appendTo(".emailforgot .messages");
                            $('<div>', {
                                "class": '',
                                html: $t(response.message)
                            }).appendTo(".emailforgot .messages .message-error");
                        }
                    }
                });
            }
        }
        
        });